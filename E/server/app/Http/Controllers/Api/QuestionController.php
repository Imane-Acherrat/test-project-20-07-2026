<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function store(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);

        $validated = $request->validate([
            'type' => 'required|string|in:short_answer,long_answer,single_choice,multiple_choice,dropdown,number,email,date,rating',
            'label' => 'required|string|max:255',
            'is_required' => 'boolean',
            'order' => 'integer',
            'options' => 'nullable|array',
            'options.*.label' => 'required_with:options|string',
            'options.*.value' => 'required_with:options|string',
            'options.*.order' => 'nullable|integer',
        ]);

        $question = DB::transaction(function () use ($survey, $validated) {
            $maxOrder = $survey->questions()->max('order') ?? 0;

            $q = $survey->questions()->create([
                'type' => $validated['type'],
                'label' => $validated['label'],
                'is_required' => $validated['is_required'] ?? false,
                'order' => $validated['order'] ?? ($maxOrder + 1),
            ]);

            if (!empty($validated['options'])) {
                foreach ($validated['options'] as $idx => $opt) {
                    $q->options()->create([
                        'label' => $opt['label'],
                        'value' => $opt['value'],
                        'order' => $opt['order'] ?? ($idx + 1),
                    ]);
                }
            }

            return $q;
        });

        return response()->json(['message' => 'Question added', 'data' => $question->load('options')], 201);
    }

    public function update(Request $request, Question $question): JsonResponse
    {
        $this->authorizeOwner($request, $question->survey);

        $validated = $request->validate([
            'label' => 'sometimes|required|string|max:255',
            'is_required' => 'boolean',
            'order' => 'integer',
        ]);

        $question->update($validated);

        return response()->json(['message' => 'Question updated', 'data' => $question]);
    }

    public function destroy(Request $request, Question $question): JsonResponse
    {
        $this->authorizeOwner($request, $question->survey);
        $question->delete();

        return response()->json(['message' => 'Question deleted']);
    }

    public function reorder(Request $request, Survey $survey): JsonResponse
    {
        $this->authorizeOwner($request, $survey);

        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:questions,id',
            'orders.*.order' => 'required|integer',
        ]);

        foreach ($validated['orders'] as $item) {
            $survey->questions()->where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Questions reordered successfully']);
    }

    private function authorizeOwner(Request $request, Survey $survey): void
    {
        if ($survey->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }
    }
}
