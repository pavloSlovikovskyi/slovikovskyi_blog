<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::info('Fetching all categories.');
        // Отримуємо всі категорії
        $categories = BlogCategory::all();

        return response()->json([
            'data' => $categories,
            'message' => 'Categories fetched successfully.'
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
        Log::info('Attempting to store a new category.', $request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:blog_categories,title',
            'parent_id' => 'nullable|integer|exists:blog_categories,id',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        try {
            $category = BlogCategory::create($validated);

            Log::info('Category created successfully.', ['category_id' => $category->id]);
            return response()->json([
                'data' => $category,
                'message' => 'Category created successfully.'
            ], 201); // 201 Created
        } catch (\Exception $e) {
            Log::error('Failed to create category: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BlogCategory $category)
    {
        Log::info('Fetching category with ID: ' . $category->id);
        return response()->json([
            'data' => $category,
            'message' => 'Category fetched successfully.'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, BlogCategory $category)
    {
        Log::info('Attempting to update category.', ['id' => $category->id, 'data' => $request->all()]);

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:blog_categories,title,' . $category->id,
            'parent_id' => 'nullable|integer|exists:blog_categories,id',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']);


        try {
            $category->update($validated);

            Log::info('Category updated successfully.', ['category_id' => $category->id]);
            return response()->json([
                'data' => $category,
                'message' => 'Category updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update category: ' . $e->getMessage(), ['category_id' => $category->id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BlogCategory $category)
    {
        Log::info('Attempting to delete category.', ['category_id' => $category->id]);
        try {
            $category->delete();

            Log::info('Category deleted successfully.', ['category_id' => $category->id]);
            return response()->json([
                'message' => 'Category deleted successfully.'
            ], 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete category: ' . $e->getMessage(), ['category_id' => $category->id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }
}
