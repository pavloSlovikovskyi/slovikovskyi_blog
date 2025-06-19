<?php

namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Get all blog posts with their user and category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Отримуємо всі пости разом з їхніми авторами та категоріями
        // Метод `with()` оптимізує запити до БД.
        $posts = BlogPost::with(['user', 'category'])->get();

        // Повертаємо дані у форматі JSON
        // Дані обгортаються в ключ 'data' для кращої структури API.
        return response()->json([
            'data' => $posts,
            'message' => 'Posts fetched successfully'
        ]);
    }
}
