<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Services\ImageServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;

class PostController extends Controller
{

    public function index()
    {
        try {
            $postsPerPage = 3;
            $posts = Post::with('user')
                ->orderBy('updated_at', 'desc')
                ->simplePaginate($postsPerPage);
            $pageCount = count(Post::all()) / $postsPerPage;

            return response()->json([
                'paginate' => $posts,
                'page_count' => ceil($pageCount)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in PostController.index',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StorePostRequest $request)
    {
        try {
            if ($request->hasFile('image') == false) {
                return response()->json(['error' => 'There is no image upload'], 200);
            }

            $post = new Post;

            (new ImageServices)->updateImage($post, $request, '/images/posts/', 'store');

            $post->title = $request->get('title');
            $post->location = $request->get('location');
            $post->description = $request->get('description');

            $post->save();

            return response()->json('New post created.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in PostController.store',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function show(int $id)
    {
        try {
            $post = Post::with('user')->findOrFail($id);

            return response()->json($post, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in PostController.show',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function update(UpdatePostRequest $request, int $id)
    {
        try {
            $post = Post::findOrFail($id);

            if ($request->hasFile('image')) {
                (new ImageServices)->updateImage($post, $request, '/images/posts/', 'update');
            }

            $post->title = $request->get('title');
            $post->location = $request->get('location');
            $post->description = $request->get('description');

            $post->save();

            return response()->json('Post updated.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in PostController.update',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);

            if (!empty($post->image)) {
                $currentImage = public_path() . '/images/posts/' . $post->image;

                if (file_exists($currentImage)) {
                    unlink($currentImage);
                }
            }

            $post->delete();

            return response()->json('Post deleted.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong in PostController.destroy',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
