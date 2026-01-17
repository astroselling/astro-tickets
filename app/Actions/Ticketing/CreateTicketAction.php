<?php

declare(strict_types=1);

namespace App\Actions\Ticketing;

use App\Models\Ticket;
use App\Models\TicketBoard;
use App\Models\TicketEvent;
use App\Models\TicketType;
use App\Models\WorkflowState;
use DomainException;

final class CreateTicketAction
{
    public function execute(
        int $boardId,
        int $typeId,
        ?int $subtypeId,
        string $title,
        ?string $description,
        int $createdBy,
        int $priority = 0
    ): Ticket {
        $board = TicketBoard::findOrFail($boardId);
        $type = TicketType::findOrFail($typeId);

        if ($type->board_id !== $board->id) {
            throw new DomainException('Ticket type does not belong to the specified board.');
        }

        if ($subtypeId !== null) {
            $subtype = $type->subtypes()->find($subtypeId);
            if ($subtype === null) {
                throw new DomainException('Ticket subtype does not belong to the specified type.');
            }
        }

        $initialState = WorkflowState::where('board_id', $boardId)
            ->where('is_initial', true)
            ->firstOrFail();

        $ticket = Ticket::create([
            'board_id' => $boardId,
            'type_id' => $typeId,
            'subtype_id' => $subtypeId,
            'state_id' => $initialState->id,
            'title' => $title,
            'description' => $description,
            'created_by' => $createdBy,
            'priority' => $priority,
        ]);

        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'user_id' => $createdBy,
            'event_type' => 'ticket_created',
            'payload_json' => [
                'board_id' => $boardId,
                'type_id' => $typeId,
                'subtype_id' => $subtypeId,
                'title' => $title,
            ],
        ]);

        return $ticket;
    }
}
