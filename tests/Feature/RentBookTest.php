<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RentBookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_rents_a_book_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a book with available copies
        $book = Book::factory()->create(['available_copies' => 1]);

        // Send a request to rent the book
        $response = $this->json('POST', route('rent.book'), [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // Assert the response status and message
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'Book rented successfully',
                'rental' => [
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    // You can assert on additional fields here if needed
                ]
            ]);

        // Assert the rental record is created
        $this->assertDatabaseHas('rentals', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // Assert the book's available copies are decreased
        $this->assertEquals(0, $book->fresh()->available_copies);
    }

    #[Test]
    public function it_returns_validation_error_when_user_id_is_missing(): void
    {
        // Create a book
        $book = Book::factory()->create();

        // Send a request without user_id
        $response = $this->json('POST', route('rent.book'), [
            'book_id' => $book->id,
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

    #[Test]
    public function it_returns_validation_error_when_book_id_is_missing(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a request without book_id
        $response = $this->json('POST', route('rent.book'), [
            'user_id' => $user->id,
        ]);

        // Assert the response status and error structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'status' => false,
                'error_type' => 'validation',
                'message' => [
                    'book_id' => ['The book id field is required.'],
                ],
            ]);
    }

    #[Test]
    public function it_returns_error_when_no_copies_are_available(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a book with no available copies
        $book = Book::factory()->create(['available_copies' => 0]);

        // Send a request to rent the book
        $response = $this->json('POST', route('rent.book'), [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // Assert the response status and error message
        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST)
            ->assertJson([
                'status' => false,
                'message' => 'No copies of this book are available for rent.',
            ]);
    }
}
