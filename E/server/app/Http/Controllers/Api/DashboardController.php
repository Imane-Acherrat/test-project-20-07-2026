<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Survey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        $totalSurveys = $user->surveys()->count();
        $publishedSurveys = $user->surveys()->where('status', 'published')->count();

        $surveyIds = $user->surveys()->pluck('id');
        $totalResponses = Submission::whereIn('survey_id', $surveyIds)->count();

        $recentSurveys = $user->surveys()
            ->latest()
            ->take(5)
            ->get();

        $recentSubmissions = Submission::whereIn('survey_id', $surveyIds)
            ->with('survey:id,title')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'metrics' => [
                'total_surveys' => $totalSurveys,
                'published_surveys' => $publishedSurveys,
                'total_responses' => $totalResponses,
            ],
            'recent_surveys' => $recentSurveys,
            'recent_submissions' => $recentSubmissions,
        ]);
    }

    public function submissions(Request $request, Survey $survey): JsonResponse
    {
        if ($survey->user_id !== $request->user()->id) {
            abort(403);
        }

        $query = $survey->submissions()->with('answers.question');

        // Optional search filter across answer values
        if ($search = $request->query('search')) {
            $query->whereHas('answers', function ($q) use ($search) {
                $q->where('value', 'LIKE', "%{$search}%");
            });
        }

        $submissions = $query->latest()->paginate(15);

        return response()->json($submissions);
    }

    public function deleteSubmission(Request $request, Submission $submission): JsonResponse
    {
        if ($submission->survey->user_id !== $request->user()->id) {
            abort(403);
        }

        $submission->delete();

        return response()->json(['message' => 'Submission deleted']);
    }
}
