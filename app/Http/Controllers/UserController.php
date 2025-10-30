<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\UserFollow;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $builder = User::where('id', '<>', $user->id)
                ->when($request->filled('is_followed'), function ($query) use ($request, $user) {
                    if ($request->is_followed == 1) {
                        $query->whereHas('followers', function ($q) use ($user) {
                            $q->where('follower_id', $user->id);
                        });
                    } elseif ($request->is_followed == 0) {
                        $query->whereDoesntHave('followers', function ($q) use ($user) {
                            $q->where('follower_id', $user->id);
                        });
                    }
                });
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

    public function detail(Request $request, User $user)
    {
        try {
            $user->loadCount(['followers', 'followings', 'posts']);
            return Response::success($user);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }


    public function getPosts(Request $request, User $user)
    {
        try {
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

    public function handleFollow(Request $request, User $user)
    {
        try {
            authorizeFollow($request, $user);

            $userAuth = $request->user();

            $follow = UserFollow::where('follower_id', $userAuth->id)
                ->where('followee_id', $user->id)
                ->first();

            if ($follow) {
                $follow->delete();
            } else {
                UserFollow::create([
                    'follower_id' => $userAuth->id,
                    'followee_id' => $user->id,
                ]);
            }

            return Response::success();
        } catch (\Throwable $th) {
            return Response::error($th->getMessage(), $th->getCode());
        }
    }
}
