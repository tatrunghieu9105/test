<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $tickets;
    public float $total;

    public function __construct(array $tickets, float $total)
    {
        $this->tickets = $tickets;
        $this->total = $total;
    }

    public function build()
    {
        return $this->subject('Xác nhận thanh toán vé xem phim')
            ->view('emails.ticket_paid');
    }
}


