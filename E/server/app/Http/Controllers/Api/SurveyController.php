<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $surveys = $request->user()->surveys()
            ->withCount('submissions')
            ->latest()
            ->paginate(10);

        return response()->json($surveys);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'confirmation_message' => 'nullable|string',
        ]);

        $survey = $request->user()->surveys()->create($validated);

        return response()->json(['message' => 'Survey created', 'data' => $survey], 201);
    }

    public function show(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);
        return response()->json($survey->load('questions.options', 'webhooks'));
    }

    public function update(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:draft,published',
            'confirmation_message' => 'nullable|string',
        ]);

        $survey->update($validated);

        return response()->json(['message' => 'Survey updated', 'data' => $survey]);
    }

    public function destroy(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);
        $survey->delete();

        return response()->json(['message' => 'Survey deleted']);
    }

    public function duplicate(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);

        $newSurvey = $survey->replicate([
            'public_slug',
            'status',
            'created_at',
            'updated_at'
        ]);
        $newSurvey->title = $survey->title . ' (Copy)';
        $newSurvey->status = 'draft';
        $newSurvey->public_slug = (string) Str::uuid();
        $newSurvey->save();

        foreach ($survey->questions as $question) {
            $newQuestion = $question->replicate();
            $newSurvey->questions()->save($newQuestion);

            foreach ($question->options as $option) {
                $newOption = $option->replicate();
                $newQuestion->options()->save($newOption);
            }
        }

        return response()->json(['message' => 'Survey duplicated successfully', 'data' => $newSurvey], 201);
    }

    private function authorizeOwner(Request $request, Survey $survey): void
    {
        if ($survey->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to survey resource.');
        }
    }
}
