<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Mail\OverdueRentalNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class MarkOverdueRentals extends Command
{
    protected $signature = 'rentals:mark-overdue';
    protected $description = 'Mark rentals as overdue and send notifications';

    public function handle()
    {
        $currentDate = Carbon::now();
        // Fetch rentals that are overdue
        $overdueRentals = Rental::where('overdue_at', '<', $currentDate)
            ->where('is_overdue', false)
            ->get();

        foreach ($overdueRentals as $rental) {
            // Mark as overdue
            $rental->update(['is_overdue' => true]);

            // Send email notification
            Mail::to($rental->user->email)->send(new OverdueRentalNotification($rental));
        }

        $this->info('Overdue rentals processed and notifications sent.');
    }
}
