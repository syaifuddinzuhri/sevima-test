<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

if (!function_exists('authorizePost')) {
    function authorizePost(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            abort(403, 'You do not have permission to modify this post.');
        }
    }
}

if (!function_exists('authorizeFollow')) {
    function authorizeFollow(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            abort(403, 'You cannot follow yourself');
        }
    }
}
