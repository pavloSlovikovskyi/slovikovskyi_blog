<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Отримати список всіх користувачів (авторів).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = User::select('id', 'name')->get();

            Log::info('Fetched users successfully.', ['users_count' => $users->count()]);

            return response()->json([
                'data' => $users,
                'message' => 'Users fetched successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching users in UserController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }
}
