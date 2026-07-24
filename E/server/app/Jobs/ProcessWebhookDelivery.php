<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Throwable;

class ProcessWebhookDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public Webhook $webhook,
        public Submission $submission,
        public ?WebhookDelivery $delivery = null
    ) {}

    public function handle(): void
    {
        if (!$this->delivery) {
            $this->delivery = WebhookDelivery::create([
                'webhook_id' => $this->webhook->id,
                'submission_id' => $this->submission->id,
                'status' => 'pending',
                'attempts' => 0,
            ]);
        }

        $this->delivery->increment('attempts');
        $this->delivery->update(['last_tried_at' => now()]);

        $answersData = $this->submission->answers()
            ->with('question:id,label,type')
            ->get()
            ->map(fn($answer) => [
                'question_id' => $answer->question_id,
                'label' => $answer->question->label,
                'value' => $answer->value,
            ]);

        $payload = [
            'event' => 'survey.submission.created',
            'survey_id' => $this->webhook->survey_id,
            'submission_id' => $this->submission->id,
            'submitted_at' => $this->submission->submitted_at,
            'data' => $answersData,
        ];

        $jsonPayload = json_encode($payload);
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'SurveyPlatform-Webhook/1.0',
        ];

        if (!empty($this->webhook->secret)) {
            $headers['X-Signature'] = hash_hmac('sha256', $jsonPayload, $this->webhook->secret);
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->post($this->webhook->url, $payload);

            if ($response->successful()) {
                $this->delivery->update([
                    'status' => 'success',
                    'response_code' => $response->status(),
                    'response_body' => substr($response->body(), 0, 2000),
                    'error_message' => null,
                ]);
            } else {
                $this->delivery->update([
                    'status' => 'failed',
                    'response_code' => $response->status(),
                    'response_body' => substr($response->body(), 0, 2000),
                    'error_message' => 'HTTP request failed with status ' . $response->status(),
                ]);
            }
        } catch (Throwable $e) {
            $this->delivery->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
