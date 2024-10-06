<?php

namespace App\Mail;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OverdueRentalNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $rental;

    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

    public function build()
    {
        return $this->subject('Overdue Rental Notification')
            ->view('emails.overdue_rental') // Create this view in resources/views/emails
            ->with(['rental' => $this->rental]);
    }
}
