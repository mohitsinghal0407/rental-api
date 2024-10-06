<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rent(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'book_id' => 'required|exists:books,id',
                'user_id' => 'required|exists:users,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'status' => false,
                'error_type' => 'validation',
                'message' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Check if the book is available
            $book = Book::find($validated['book_id']);
            if ($book->available_copies <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'No copies of this book are available for rent.'
                ], 400);
            }

            $rentalExist = Rental::where(['book_id' => $validated['book_id'], 'user_id' => $validated['user_id']])->whereNull('return_at')->first();
            if($rentalExist) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already rented this book.'
                ], 400);
            }

            $overdueDays = config('rentals.overdue_days'); // Get days from the config
            // Create the rental record
            $rental = Rental::create([
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'issue_at' => Carbon::now(),
                'overdue_at' => Carbon::now()->addDays($overdueDays),
            ]);

            // Decrease the available copies of the book
            $book->available_copies--;
            $book->save();

            return response()->json(['status' => true, 'message' => 'Book rented successfully', 'rental' => $rental], 200);
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

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnBook(Request $request): JsonResponse
    {
        try {
            // Validate the request (expecting rental_id)
            $validated = $request->validate([
                'user_id' => 'required|exists:rentals,user_id',
                'rental_id' => 'required|exists:rentals,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json([
                'status' => false,
                'error_type' => 'validation',
                'message' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Find the rental
            $rental = Rental::where(['id' => $validated['rental_id'], 'user_id' => $validated['user_id']])->first();
            if (!$rental) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid rental id.'
                ], 404);
            }

            // Check if the rental is already returned
            if ($rental->return_at) {
                return response()->json([
                    'status' => false,
                    'message' => 'This rental is already returned.'
                ], 400);
            }

            // Update the return date
            $return_date = Carbon::now();
            $rental->return_at = $return_date;
            if($rental->overdue_at < $return_date) {
                $rental->is_overdue = true;
            }
            $rental->save();

            // Increase the available copies for the book
            $book = Book::find($rental->book_id);
            if ($book) {
                $book->available_copies++;
                $book->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Book returned successfully.'
            ], 200);
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

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rentalHistory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'error_type' => 'validation',
                'message' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        // Fetch rental history using the user ID
        $rentals = Rental::where('user_id', $validated['user_id'])->get();
        return response()->json(['status' => true, 'rental_history' => $rentals], 200);
    }
}
