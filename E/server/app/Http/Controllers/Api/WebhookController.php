<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWebhookDelivery;
use App\Models\Survey;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);
        return response()->json($survey->webhooks);
    }

    public function store(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'is_active' => 'boolean',
            'secret' => 'nullable|string|max:255',
        ]);

        $webhook = $survey->webhooks()->create($validated);

        return response()->json(['message' => 'Webhook created', 'data' => $webhook], 201);
    }

    public function retry(Request $request, WebhookDelivery $delivery): JsonResponse
    {
        if ($delivery->webhook->survey->user_id !== $request->user()->id) {
            abort(403);
        }

        ProcessWebhookDelivery::dispatch($delivery->webhook, $delivery->submission, $delivery);

        return response()->json(['message' => 'Webhook delivery queued for retry.']);
    }

    private function authorizeOwner(Request $request, Survey $survey): void
    {
        if ($survey->user_id !== $request->user()->id) {
            abort(403);
        }
    }
}
