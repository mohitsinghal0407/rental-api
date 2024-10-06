<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class ReturnBookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_book_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a book
        $book = Book::factory()->create(['available_copies' => 1]);

        // Create a rental record for the user and book
        $rental = Rental::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'return_at' => null,
            'overdue_at' => Carbon::now()->addDays(7), // Example overdue date
            'is_overdue' => false,
        ]);

        // Send a request to return the book
        $response = $this->json('POST', route('return.book'), [
            'user_id' => $user->id,
            'rental_id' => $rental->id,
        ]);

        // Assert the response status and message
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'Book returned successfully.'
            ]);

        // Assert the rental's return date is updated
        $this->assertNotNull($rental->fresh()->return_at);
        $this->assertEquals(false, $rental->fresh()->is_overdue);

        // Assert the book's available copies are updated
        $this->assertEquals(2, $book->fresh()->available_copies);
    }

    /** @test */
    public function it_returns_validation_error_when_user_id_is_missing()
    {
        // Create a rental record
        $rental = Rental::factory()->create();

        // Send a request without user_id
        $response = $this->json('POST', route('return.book'), [
            'rental_id' => $rental->id,
        ]);

        // Assert the response status and error structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'error_type' => 'validation',
                'message' => [
                    'user_id' => ['The user id field is required.'],
                ],
            ]);
    }

    /** @test */
    public function it_returns_validation_error_when_rental_id_is_missing()
    {
        // Create a user
        $user = User::factory()->create();

        // Send a request without rental_id
        $response = $this->json('POST', route('return.book'), [
            'user_id' => $user->id,
        ]);

        // Assert the response status and error structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'error_type' => 'validation',
                'message' => [
                    'rental_id' => ['The rental id field is required.'],
                ],
            ]);
    }

    /** @test */
    public function it_returns_error_when_book_is_already_returned()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a book
        $book = Book::factory()->create(['available_copies' => 1]);

        // Create a rental record for the user and book
        $rental = Rental::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'return_at' => Carbon::now(), // Marked as already returned
        ]);

        // Send a request to return the book
        $response = $this->json('POST', route('return.book'), [
            'user_id' => $user->id,
            'rental_id' => $rental->id,
        ]);

        // Assert the response status and error message
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST)
            ->assertJson([
                'status' => false,
                'message' => 'This rental is already returned.',
            ]);
    }
}
