<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhookDelivery;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicSurveyController extends Controller
{
    public function show(string $slug): JsonResponse
    {
        $survey = Survey::where('public_slug', $slug)
            ->where('status', 'published')
            ->with(['questions.options'])
            ->first();

        if (!$survey) {
            return response()->json(['error' => 'Survey not found or inactive.'], 404);
        }

        return response()->json($survey);
    }

    public function submit(Request $request, string $slug): JsonResponse
    {
        $survey = Survey::where('public_slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $rules = [];
        $questions = $survey->questions;

        foreach ($questions as $question) {
            $fieldKey = "answers.{$question->id}";
            $fieldRules = [$question->is_required ? 'required' : 'nullable'];

            switch ($question->type) {
                case 'number':
                case 'rating':
                    $fieldRules[] = 'numeric';
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'multiple_choice':
                    $fieldRules[] = 'array';
                    break;
            }

            $rules[$fieldKey] = $fieldRules;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $submission = DB::transaction(function () use ($survey, $request) {
            $sub = $survey->submissions()->create([
                'submitted_at' => now(),
            ]);

            foreach ($request->input('answers', []) as $questionId => $value) {
                $sub->answers()->create([
                    'question_id' => $questionId,
                    'value' => $value,
                ]);
            }

            return $sub;
        });

        $activeWebhooks = $survey->webhooks()->where('is_active', true)->get();
        foreach ($activeWebhooks as $webhook) {
            ProcessWebhookDelivery::dispatch($webhook, $submission);
        }

        return response()->json([
            'message' => $survey->confirmation_message ?? 'Thank you! Your response has been recorded.',
            'submission_id' => $submission->id
        ], 201);
    }
}
