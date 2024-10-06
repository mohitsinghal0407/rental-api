<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchBooksTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_books_based_on_title_search(): void
    {
        // Create some books
        Book::factory()->create(['title' => 'The Great Gatsby', 'genre' => 'Fiction']);
        Book::factory()->create(['title' => 'Moby Dick', 'genre' => 'Fiction']);
        Book::factory()->create(['title' => 'War and Peace', 'genre' => 'Historical Fiction']);

        // Send a request to search by title
        $response = $this->json('GET', route('search.books'), [
            'title' => 'Great',
        ]);

        // Assert the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'message' => 'Books retrieved successfully',
                'books' => [
                    [
                        'title' => 'The Great Gatsby',
                        'genre' => 'Fiction',
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_returns_books_based_on_genre_search(): void
    {
        // Create some books
        Book::factory()->create(['title' => 'The Great Gatsby', 'genre' => 'Fiction']);
        Book::factory()->create(['title' => 'Moby Dick', 'genre' => 'Fiction']);
        Book::factory()->create(['title' => 'War and Peace', 'genre' => 'Historical Fiction']);

        // Send a request to search by genre
        $response = $this->json('GET', route('search.books'), [
            'genre' => 'Fiction',
        ]);

        // Assert the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'message' => 'Books retrieved successfully',
                'books' => [
                    [
                        'title' => 'The Great Gatsby',
                        'genre' => 'Fiction',
                    ],
                    [
                        'title' => 'Moby Dick',
                        'genre' => 'Fiction',
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_returns_books_based_on_title_and_genre_search(): void
    {
        // Create some books
        Book::factory()->create(['title' => 'The Great Gatsby', 'genre' => 'Fiction']);
        Book::factory()->create(['title' => 'Moby Dick', 'genre' => 'Fiction']);
        Book::factory()->create(['title' => 'War and Peace', 'genre' => 'Historical Fiction']);

        // Send a request to search by title and genre
        $response = $this->json('GET', route('search.books'), [
            'title' => 'Moby',
            'genre' => 'Fiction',
        ]);

        // Assert the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'message' => 'Books retrieved successfully',
                'books' => [
                    [
                        'title' => 'Moby Dick',
                        'genre' => 'Fiction',
                    ],
                ],
            ]);
    }

    #[Test]
    public function it_returns_no_books_when_no_matches_found(): void
    {
        // Create some books
        Book::factory()->create(['title' => 'The Great Gatsby', 'genre' => 'Fiction']);
        Book::factory()->create(['title' => 'Moby Dick', 'genre' => 'Fiction']);

        // Send a request to search for non-existing title
        $response = $this->json('GET', route('search.books'), [
            'title' => 'Non-Existing Book',
        ]);

        // Assert the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'message' => 'Books retrieved successfully',
                'books' => [], // No books found
            ]);
    }

    #[Test]
    public function it_returns_validation_error_when_invalid_params_provided(): void
    {
        // Send a request with invalid title parameter
        $response = $this->json('GET', route('search.books'), [
            'title' => str_repeat('a', 256), // Exceeds max length
        ]);

        // Assert the response status and error structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'error' => 'validation',
            'message' => [
                'title' => ['The title field must not be greater than 255 characters.'],
            ],
        ]);
    }
}
