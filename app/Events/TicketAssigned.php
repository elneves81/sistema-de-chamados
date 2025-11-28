<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAssigned
{
    use Dispatchable, SerializesModels;

    public $ticket;
    public $technician;

    public function __construct(Ticket $ticket, $technician)
    {
        $this->ticket = $ticket;
        $this->technician = $technician;
    }
}
