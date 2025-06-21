<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Get all blog posts with their user and category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $posts = BlogPost::with(['user', 'category'])->get();

        return response()->json([
            'data' => $posts,
            'message' => 'Posts fetched successfully'
        ]);
    }
    public function show(string $id)
    {
        Log::info('Incoming request to PostController@show for ID: ' . $id);

        try {
            // Знаходимо пост за ID, завантажуючи пов'язані моделі user та category
            $post = BlogPost::with(['user', 'category'])->find($id);

            // Якщо пост не знайдено, повертаємо 404
            if (!$post) {
                return response()->json([
                    'data' => null,
                    'message' => 'Post not found.'
                ], 404);
            }

            // Повертаємо знайдений пост у форматі JSON
            return response()->json([
                'data' => $post,
                'message' => 'Post fetched successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in PostController@show: ' . $e->getMessage(), ['post_id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'data' => null,
                'message' => 'Failed to fetch post: ' . $e->getMessage()
            ], 500);
        }
    }
}
