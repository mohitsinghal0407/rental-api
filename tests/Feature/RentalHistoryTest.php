<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rental;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RentalHistoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_rental_history_for_a_valid_user(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create rental records for the user
        Rental::factory()->count(3)->create(['user_id' => $user->id]);

        // Send a GET request to the rentalHistory API
        $response = $this->json('GET', route('rental.history'), [
            'user_id' => $user->id,
        ]);

        // Assert the response status and structure
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson([
                'status' => true,
                'rental_history' => [
                    // Check if the rental history contains the expected data
                    // You can further assert on specific fields here if needed
                ]
            ]);
    }

    #[Test]
    public function it_returns_validation_error_when_user_id_is_missing(): void
    {
        // Send a GET request without user_id
        $response = $this->json('GET', route('rental.history'));

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
    public function it_returns_validation_error_when_user_id_is_invalid(): void
    {
        // Send a GET request with a non-existent user_id
        $response = $this->json('GET', route('rental.history'), [
            'user_id' => 9999, // Assuming this user ID does not exist
        ]);

        // Assert the response status and error structure
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'status' => false,
            'error_type' => 'validation',
            'message' => [
                'user_id' => ['The selected user id is invalid.'],
            ],
        ]);
    }
}
