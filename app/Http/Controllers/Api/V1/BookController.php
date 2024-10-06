<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Rental;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'genre' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'error' => 'validation',
                'message' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Initialize the query
            $query = Book::query();

            // Apply search filters if present
            if (!empty($validated['title'])) {
                $query->where('title', 'like', '%' . $validated['title'] . '%');
            }
            if (!empty($validated['genre'])) {
                $query->where('genre', $validated['genre']);
            }

            // Get the results
            $books = $query->get();
            return response()->json(['message' => 'Books retrieved successfully', 'books' => $books], 200);
        }
        catch (Exception $e) {
            // If there's an error, catch the exception and return a custom response
            return response()->json([
                'status' => false,
                'error_type' => 'exception',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            // Check if any rentals exist
            if (Rental::count() === 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'No rental data available',
                    'most_overdue' => [],
                    'most_popular' => [],
                    'least_popular' => []
                ]);
            }

            // Stats logic: most overdue, most popular, least popular
            $mostOverdue = Book::withCount(['rentals' => function ($query) {
                $query->where('is_overdue', true);
            }])->orderBy('rentals_count', 'desc')->first();

            $mostPopular = Book::withCount('rentals')->orderBy('rentals_count', 'desc')->first();
            $leastPopular = Book::withCount('rentals')->orderBy('rentals_count', 'asc')->first();

            return response()->json([
                'status' => true,
                'most_overdue' => $mostOverdue,
                'most_popular' => $mostPopular,
                'least_popular' => $leastPopular,
            ]);
        }
        catch (\Exception $e) {
            // If there's an error, catch the exception and return a custom response
            return response()->json([
                'status' => false,
                'error_type' => 'exception',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
