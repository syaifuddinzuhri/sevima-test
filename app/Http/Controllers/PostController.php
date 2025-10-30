<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CommentPostRequest;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $builder = Post::withCount('likes')->withCount('comments')->with(['user'])->where('user_id', '<>', $user->id);
            $result = $builder->paginate(
                $request->limit ?? 20,
                ['*'],
                'page',
                $request->page ?? 1
            )
                ->appends($request->all());
            return Response::success($result);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function indexMe(Request $request)
    {
        try {
            $user = $request->user();
            $builder = Post::withCount('likes')->withCount('comments')->where('user_id', $user->id);
            $result = $builder->paginate(
                $request->limit ?? 20,
                ['*'],
                'page',
                $request->page ?? 1
            )
                ->appends($request->all());
            return Response::success($result);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function getComments(Request $request, Post $post)
    {
        try {
            $builder = PostComment::with([
                'user:id,name,email,username',
                'replies' => function ($query) {
                    $query->select('id', 'post_id', 'user_id', 'parent_comment_id', 'comment', 'created_at')
                        ->with('user:id,name,email,username');
                },
            ])
                ->where('post_id', $post->id)
                ->whereNull('parent_comment_id')
                ->select('id', 'post_id', 'user_id', 'parent_comment_id', 'comment', 'created_at');

            $result = $builder->paginate(
                $request->limit ?? 20,
                ['*'],
                'page',
                $request->page ?? 1
            )->appends($request->all());

            return Response::success($result);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function detail(Request $request, Post $post)
    {
        try {
            $post->loadCount(['likes', 'comments'])->load(['user']);
            return Response::success($post);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function create(CreatePostRequest $request)
    {
        try {
            $user = $request->user();
            $path = $request->file('image')->store('posts', 'public');

            Post::create([
                'user_id' => $user->id,
                'caption' => $request->caption,
                'image_path' => $path,
            ]);
            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        try {
            authorizePost($request, $post);

            $post->update([
                'caption' => $request->caption,
            ]);

            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function delete(Request $request, Post $post)
    {
        try {
            authorizePost($request, $post);

            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            $post->delete();
            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function likeUnlike(Request $request, Post $post)
    {
        try {
            $user = $request->user();

            $like = PostLike::where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->first();

            if ($like) {
                $like->delete();
                $isLiked = false;
            } else {
                PostLike::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
                $isLiked = true;
            }
            return Response::success([
                'is_liked' => $isLiked,
                'count' => $post->likes->count()
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }

    public function createComment(CommentPostRequest $request, Post $post)
    {
        try {
            $user = $request->user();
            PostComment::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'comment' => $request->comment,
                'parent_comment_id' => $request->parent_comment_id,
            ]);

            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }
}
