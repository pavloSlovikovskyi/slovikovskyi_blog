<?php

namespace App\Http\Controllers\Api\Blog; // Простір імен залишається той самий

use App\Http\Controllers\Controller;
use App\Models\BlogPost; // Переконайтеся, що це правильна модель
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostUiController extends Controller // Нова назва контролера
{
    /**
     * Display a listing of the resource with pagination and sorting for UI.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('Incoming request to PostUiController@index', $request->all());

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');

        $allowedColumns = ['id', 'title', 'published_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        $allowedDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedDirections)) {
            $sortDirection = 'asc';
        }

        try {
            $posts = BlogPost::with(['user', 'category'])
                ->orderBy($sortColumn, $sortDirection)
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $posts->items(),
                'meta' => [
                    'total' => $posts->total(),
                    'per_page' => (int)$posts->perPage(),
                    'current_page' => (int)$posts->currentPage(),
                    'last_page' => (int)$posts->lastPage(),
                ],
                'message' => 'Posts fetched successfully for UI'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in PostUiController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'data' => [],
                'message' => 'Failed to fetch posts for UI: ' . $e->getMessage()
            ], 500);
        }
    }

    // Можете залишити інші методи порожніми, оскільки ми використовуємо лише index
    // public function store(Request $request) {}
    // public function show(string $id) {}
    // public function update(Request $request, string $id) {}
    // public function destroy(string $id) {}
}
