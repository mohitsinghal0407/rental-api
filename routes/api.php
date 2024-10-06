<?php

use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\RentalController;
use Illuminate\Support\Facades\Route;

// Book Routes
Route::get('books', [BookController::class, 'search'])->name('search.books');   // Search books by title or genre
Route::get('books/stats', [BookController::class, 'getStats'])->name('get.stats');  // Get stats for books (most overdue, most popular, etc.)

// Rental Routes
Route::post('rentals', [RentalController::class, 'rent'])->name('rent.book');  // Rent a book
Route::post('rentals/return', [RentalController::class, 'returnBook'])->name('return.book');  // Return a rented book
Route::get('rentals/history', [RentalController::class, 'rentalHistory'])->name('rental.history');  // View rental history for a user
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/rentals/history', [RentalController::class, 'rentalHistory']);
});
