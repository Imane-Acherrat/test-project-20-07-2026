<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;
use App\Traits\PaginatesResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use PaginatesResponses;

    /**
     * GET /posts - paginated home feed with search, hashtag filter, sorting.
     */
    public function index(Request $request): JsonResponse
    {
        [$page, $limit] = $this->paginationParams(defaultLimit: 12, maxLimit: 50);

        $sort = $request->query('sort', 'latest');
        $search = trim((string) $request->query('search', ''));
        $hashtag = $request->query('hashtag');

        $query = Post::query()
            ->with(['creator', 'hashtags'])
            ->withIsLikedBy($request->user()?->id);

        if ($search !== '') {
            $query->where('description', 'like', '%'.$search.'%');
        }

        if ($hashtag) {
            $normalized = Hashtag::normalize(urldecode($hashtag));
            $query->whereHas(
                'hashtags',
                fn ($q) => $q->where('name', $normalized)
            );
        }

        $query = match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'popular' => $query->orderBy('likes_count', 'desc')->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json(
            $this->paginated($paginator, fn (Post $post) => (new PostResource($post))->toArray($request))
        );
    }

    /**
     * POST /posts
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();

        $imagePath = $request->file('image')->store('posts', 'public');

        $post = DB::transaction(function () use ($request, $data, $imagePath) {
            $post = $request->user()->posts()->create([
                'description' => $data['description'],
                'image' => $imagePath,
            ]);

            $this->syncHashtags($post, $data['hashtags'] ?? []);

            return $post;
        });

        $post->load(['creator', 'hashtags']);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => (new PostResource(
                $post->setAttribute('is_liked', false)
            ))->toArray($request),
        ], 201);
    }

    /**
     * GET /posts/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $post = Post::query()
            ->with(['creator', 'hashtags'])
            ->withIsLikedBy($request->user()?->id)
            ->find($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json([
            'post' => (new PostResource($post))->toArray($request),
        ]);
    }

    /**
     * PUT /posts/{id}
     */
    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        $post = Post::find($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to modify this post',
            ], 403);
        }

        $data = $request->validated();

        DB::transaction(function () use ($request, $post, $data) {
            if (isset($data['description'])) {
                $post->description = $data['description'];
            }

            if ($request->hasFile('image')) {
                $oldImage = $post->image;
                $post->image = $request->file('image')->store('posts', 'public');
                Storage::disk('public')->delete($oldImage);
            }

            $post->save();

            if (array_key_exists('hashtags', $data)) {
                $this->syncHashtags($post, $data['hashtags']);
            }
        });

        $post->refresh()->load(['creator', 'hashtags']);
        $post->setAttribute(
            'is_liked',
            $post->likes()->where('user_id', $request->user()->id)->exists()
        );

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => (new PostResource($post))->toArray($request),
        ]);
    }

    /**
     * DELETE /posts/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = Post::find($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to modify this post',
            ], 403);
        }

        DB::transaction(function () use ($post) {
            Storage::disk('public')->delete($post->image);
            $post->hashtags()->detach();
            $post->likes()->delete();
            $post->delete();
        });

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }

    /**
     * GET /users/{username}/posts
     */
    public function creatorPosts(Request $request, string $username): JsonResponse
    {
        $user = User::where('username', $username)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        [$page, $limit] = $this->paginationParams(defaultLimit: 10, maxLimit: 50);
        $sort = $request->query('sort', 'latest');

        $query = $user->posts()
            ->with(['creator', 'hashtags'])
            ->withIsLikedBy($request->user()?->id)
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

        $paginator = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json(
            $this->paginated($paginator, fn (Post $post) => (new PostResource($post))->toArray($request))
        );
    }

    /**
     * Normalize, dedupe and sync hashtags for a post; reuses existing
     * hashtag rows so names stay unique across the whole database.
     */
    private function syncHashtags(Post $post, array $rawHashtags): void
    {
        $names = collect($rawHashtags)
            ->map(fn ($tag) => Hashtag::normalize($tag))
            ->filter(fn ($tag) => $tag !== '')
            ->unique()
            ->values();

        $ids = $names->map(fn ($name) => Hashtag::findOrCreateByName($name)->id);

        $post->hashtags()->sync($ids);
    }
}
