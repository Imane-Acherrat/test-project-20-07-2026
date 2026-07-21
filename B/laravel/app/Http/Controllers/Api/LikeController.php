<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\PaginatesResponses;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    use PaginatesResponses;

    public function store(Request $request, int $id): JsonResponse
    {
        $post = Post::find($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $userId = $request->user()->id;

        if ($post->likes()->where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'You have already liked this post',
            ], 409);
        }

        try {
            DB::transaction(function () use ($post, $userId) {
                $post->likes()->create(['user_id' => $userId]);
                $post->increment('likes_count');
            });
        } catch (QueryException $e) {
            // Unique constraint race condition (double-click / concurrent request).
            return response()->json([
                'message' => 'You have already liked this post',
            ], 409);
        }

        return response()->json([
            'message' => 'Post liked successfully',
            'likesCount' => $post->fresh()->likes_count,
            'isLiked' => true,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = Post::find($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $userId = $request->user()->id;
        $like = $post->likes()->where('user_id', $userId)->first();

        if (! $like) {
            return response()->json([
                'message' => 'You have not liked this post',
            ], 409);
        }

        DB::transaction(function () use ($post, $like) {
            $like->delete();
            $post->decrement('likes_count');
        });

        return response()->json([
            'message' => 'Post unliked successfully',
            'likesCount' => $post->fresh()->likes_count,
            'isLiked' => false,
        ]);
    }

    public function index(Request $request, int $id): JsonResponse
    {
        $post = Post::find($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        [$page, $limit] = $this->paginationParams(defaultLimit: 10, maxLimit: 50);

        $paginator = $post->likes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json($this->paginated($paginator, function ($like) {
            return [
                'id' => $like->user->id,
                'name' => $like->user->name,
                'username' => $like->user->username,
                'profileImage' => $like->user->profile_image
                    ? asset('storage/'.$like->user->profile_image)
                    : null,
            ];
        }));
    }
}
