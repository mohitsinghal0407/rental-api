<?php

namespace Tests\Feature;

use App\Console\Commands\MarkOverdueRentals;
use App\Mail\OverdueRentalNotification;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MarkOverdueRentalsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_marks_rentals_as_overdue_and_sends_notifications(): void
    {
        // Fake the mail system so no real emails are sent
        Mail::fake();

        // Create a user and rental in the past to test overdue logic
        $user = User::factory()->create();
        $rental = Rental::factory()->create([
            'user_id' => $user->id,
            'overdue_at' => Carbon::now()->subDays(3), // 3 days overdue
            'is_overdue' => false,
        ]);

        // Execute the command
        $this->artisan('rentals:mark-overdue')
            ->expectsOutput('Overdue rentals processed and notifications sent.')
            ->assertExitCode(0);

        // Assert the rental was marked as overdue
        $this->assertTrue($rental->fresh()->is_overdue);

        // Assert that an email was sent to the user
        Mail::assertSent(OverdueRentalNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    #[Test]
    public function it_does_not_send_notifications_if_no_rentals_are_overdue(): void
    {
        // Fake the mail system so no real emails are sent
        Mail::fake();

        // Create a user and a non-overdue rental
        $user = User::factory()->create();
        $rental = Rental::factory()->create([
            'user_id' => $user->id,
            'overdue_at' => Carbon::now()->addDays(3), // Not yet overdue
            'is_overdue' => false,
        ]);

        // Execute the command
        $this->artisan('rentals:mark-overdue')
            ->expectsOutput('Overdue rentals processed and notifications sent.')
            ->assertExitCode(0);

        // Assert that no email was sent
        Mail::assertNothingSent();

        // Assert the rental is still not marked as overdue
        $this->assertFalse($rental->fresh()->is_overdue);
    }
}
