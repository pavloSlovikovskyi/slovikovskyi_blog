<?php

namespace App\Http\Controllers\Api\FrontControllersPosts;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Incoming request to PostController@store', $request->all());

        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
                'category_id' => 'nullable|exists:blog_categories,id',
                'excerpt' => 'nullable|string',
                'content_raw' => 'nullable|string',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date',
                'user_id' => 'required|exists:users,id',
            ]);

            // Якщо slug не надано, генеруємо його з title
            if (empty($validatedData['slug'])) {
                $validatedData['slug'] = Str::slug($validatedData['title']);
            }

            // Логіка для published_at: якщо is_published і published_at не задано, встановлюємо поточну дату
            if (isset($validatedData['is_published']) && $validatedData['is_published'] && empty($validatedData['published_at'])) {
                $validatedData['published_at'] = Carbon::now();
            }
            // Якщо is_published не встановлено (false) і published_at все ще є, робимо його null
            elseif (isset($validatedData['is_published']) && !$validatedData['is_published']) {
                $validatedData['published_at'] = null;
            }

            Log::info('🛠️ Перед створенням поста', $validatedData);
            $post = BlogPost::create($validatedData);
            Log::info('📦 Створено пост', $post->toArray());

            return response()->json([
                'data' => $post,
                'message' => 'Post created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation error in PostController@store: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in PostController@store: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to create post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        Log::info('Incoming request to PostController@show for ID: ' . $id);

        try {
            $post = BlogPost::with(['user', 'category'])->find($id);

            if (!$post) {
                return response()->json([
                    'data' => null,
                    'message' => 'Post not found.'
                ], 404);
            }

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        Log::info('Incoming request to PostController@update for ID: ' . $id, $request->all());

        try {
            $post = BlogPost::find($id);

            if (!$post) {
                return response()->json([
                    'data' => null,
                    'message' => 'Post not found.'
                ], 404);
            }

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $id,
                'category_id' => 'nullable|exists:blog_categories,id',
                'excerpt' => 'nullable|string',
                'content_raw' => 'nullable|string',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date',
                'user_id' => 'required|exists:users,id',
            ]);

            // Якщо slug не надано, генеруємо його з title
            if (empty($validatedData['slug'])) {
                $validatedData['slug'] = Str::slug($validatedData['title']);
            }

            // Логіка для published_at: якщо is_published і published_at не задано, встановлюємо поточну дату
            if (isset($validatedData['is_published']) && $validatedData['is_published'] && empty($validatedData['published_at'])) {
                $validatedData['published_at'] = Carbon::now();
            }
            // Якщо is_published не встановлено (false) і published_at все ще є, робимо його null
            elseif (isset($validatedData['is_published']) && !$validatedData['is_published']) {
                $validatedData['published_at'] = null;
            }

            $post->update($validatedData);

            return response()->json([
                'data' => $post,
                'message' => 'Post updated successfully.'
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation error in PostController@update: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in PostController@update: ' . $e->getMessage(), ['post_id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to update post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        Log::info('Incoming request to PostController@destroy for ID: ' . $id);

        try {
            $post = BlogPost::find($id);

            if (!$post) {
                return response()->json([
                    'data' => null,
                    'message' => 'Post not found.'
                ], 404);
            }

            $post->delete();

            return response()->json([
                'message' => 'Post deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in PostController@destroy: ' . $e->getMessage(), ['post_id' => $id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to delete post: ' . $e->getMessage()
            ], 500);
        }
    }
}
