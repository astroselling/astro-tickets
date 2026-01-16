<?php

declare(strict_types=1);

namespace App\Actions\Ticketing;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketEvent;

final class AddCommentAction
{
    public function execute(
        int $ticketId,
        int $userId,
        string $content,
        bool $isInternal = false
    ): TicketComment {
        $ticket = Ticket::findOrFail($ticketId);

        $comment = TicketComment::create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'content' => $content,
            'is_internal' => $isInternal,
        ]);

        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'event_type' => 'comment_added',
            'payload_json' => [
                'comment_id' => $comment->id,
                'is_internal' => $isInternal,
            ],
        ]);

        return $comment;
    }
}
