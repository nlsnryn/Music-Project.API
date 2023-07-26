<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostByUserController extends Controller
{
    public function show(int $user_id)
    {
        try {
            $posts = Post::where('user_id', $user_id)->get();

            return response()->json($posts, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in PostByUserController.show',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
