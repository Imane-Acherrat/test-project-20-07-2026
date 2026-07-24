<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PublicSurveyController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\TestEndpointController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

/*
    Public Routes
*/

// Authentication
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

// Public Participant Access
Route::get('/v1/s/{slug}', [PublicSurveyController::class, 'show']);
Route::post('/v1/s/{slug}/submit', [PublicSurveyController::class, 'submit']);

// Test Webhook Receiver Endpoint
Route::post('/v1/test-webhook/receive', [TestEndpointController::class, 'receive']);


/*
    Protected Private Routes (Requires Bearer Token)
*/
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Auth User
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard & Metrics
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Survey Management
    Route::apiResource('surveys', SurveyController::class);
    Route::post('surveys/{survey}/duplicate', [SurveyController::class, 'duplicate']);

    // Question Builder
    Route::post('surveys/{survey}/questions', [QuestionController::class, 'store']);
    Route::put('questions/{question}', [QuestionController::class, 'update']);
    Route::delete('questions/{question}', [QuestionController::class, 'destroy']);
    Route::post('surveys/{survey}/questions/reorder', [QuestionController::class, 'reorder']);

    // Submissions
    Route::get('surveys/{survey}/submissions', [DashboardController::class, 'submissions']);
    Route::delete('submissions/{submission}', [DashboardController::class, 'deleteSubmission']);

    // Webhooks
    Route::get('surveys/{survey}/webhooks', [WebhookController::class, 'index']);
    Route::post('surveys/{survey}/webhooks', [WebhookController::class, 'store']);
    Route::post('webhook-deliveries/{delivery}/retry', [WebhookController::class, 'retry']);

    // Internal Webhook Testing Inspector
    Route::get('test-webhook/logs', [TestEndpointController::class, 'logs']);
});
