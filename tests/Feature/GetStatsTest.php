<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetStatsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_no_rental_data_if_no_rentals_exist(): void
    {
        // Send a request to get stats when there are no rentals
        $response = $this->json('GET', route('get.stats'));

        // Assert the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'status' => true,
                'message' => 'No rental data available',
                'most_overdue' => [],
                'most_popular' => [],
                'least_popular' => []
            ]);
    }
}
