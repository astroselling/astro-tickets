<?php

declare(strict_types=1);

namespace App\Actions\Ticketing;

use App\Models\Ticket;
use App\Models\TicketActor;
use App\Models\TicketEvent;
use App\Models\User;

final class AssignTicketAction
{
    public function execute(int $ticketId, int $userId, int $assignedBy): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);
        $user = User::findOrFail($userId);

        TicketActor::updateOrCreate(
            [
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'role' => 'assignee',
            ],
            [
                'ticket_id' => $ticketId,
                'user_id' => $userId,
                'role' => 'assignee',
            ]
        );

        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'user_id' => $assignedBy,
            'event_type' => 'ticket_assigned',
            'payload_json' => [
                'assigned_to_user_id' => $userId,
                'assigned_to_user_name' => $user->name,
            ],
        ]);

        return $ticket->fresh();
    }
}
