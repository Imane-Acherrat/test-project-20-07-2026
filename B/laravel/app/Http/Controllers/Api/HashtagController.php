<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Hashtag;
use App\Models\Post;
use App\Traits\PaginatesResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    use PaginatesResponses;

    /**
     * GET /hashtags/{name}/posts
     */
    public function posts(Request $request, string $name): JsonResponse
    {
        $normalized = Hashtag::normalize($name);
        $hashtag = Hashtag::where('name', $normalized)->first();

        if (! $hashtag) {
            return response()->json(['message' => 'Hashtag not found'], 404);
        }

        [$page, $limit] = $this->paginationParams(defaultLimit: 10, maxLimit: 50);
        $sort = $request->query('sort', 'latest');

        $query = $hashtag->posts()
            ->with(['creator', 'hashtags'])
            ->withIsLikedBy($request->user()?->id)
            ->orderBy('posts.created_at', $sort === 'oldest' ? 'asc' : 'desc');

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'hashtag' => [
                'id' => $hashtag->id,
                'name' => $hashtag->name,
                'postsCount' => $hashtag->posts()->count(),
            ],
            ...$this->paginated($paginator, fn (Post $post) => (new PostResource($post))->toArray($request)),
        ]);
    }

    /**
     * GET /hashtags/trending
     */
    public function trending(Request $request): JsonResponse
    {
        $days = max(1, (int) $request->query('days', 7));
        $limit = (int) $request->query('limit', 10);
        $limit = $limit < 1 ? 10 : min($limit, 50); // validated upper bound

        $from = now()->subDays($days)->startOfDay();
        $to = now()->endOfDay();

        $rows = Hashtag::query()
            ->select('hashtags.id', 'hashtags.name')
            ->selectRaw('count(distinct posts.id) as posts_count')
            ->join('hashtag_post', 'hashtag_post.hashtag_id', '=', 'hashtags.id')
            ->join('posts', 'posts.id', '=', 'hashtag_post.post_id')
            ->whereBetween('posts.created_at', [$from, $to])
            ->groupBy('hashtags.id', 'hashtags.name')
            ->orderByDesc('posts_count')
            ->orderBy('hashtags.name', 'asc') // tie-break alphabetically
            ->limit($limit)
            ->get();

        $data = $rows->values()->map(function ($row, $index) {
            return [
                'name' => $row->name,
                'postsCount' => (int) $row->posts_count,
                'rank' => $index + 1,
            ];
        });

        return response()->json([
            'period' => [
                'days' => $days,
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
            ],
            'data' => $data,
        ]);
    }
}
