<?php

declare(strict_types=1);

namespace App\Actions\Ticketing;

use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use DomainException;

final class TransitionTicketAction
{
    public function execute(int $ticketId, int $toStateId, int $transitionedBy): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);
        $toState = WorkflowState::findOrFail($toStateId);

        // Validar que el estado destino pertenece al board del ticket
        if ($toState->board_id !== $ticket->board_id) {
            throw new DomainException('Target state does not belong to the ticket board.');
        }

        // Validar que existe una transición válida desde el estado actual
        $transition = WorkflowTransition::where('board_id', $ticket->board_id)
            ->where('from_state_id', $ticket->state_id)
            ->where('to_state_id', $toStateId)
            ->first();

        if ($transition === null) {
            throw new DomainException('Invalid transition from current state to target state.');
        }

        $fromState = $ticket->state;
        $ticket->state_id = $toStateId;

        // Setear resolved_at si el nuevo estado es "resolved"
        if ($toState->name === 'resolved' && $ticket->resolved_at === null) {
            $ticket->resolved_at = now();
        }

        // Setear closed_at si el nuevo estado es terminal
        if ($toState->is_terminal && $ticket->closed_at === null) {
            $ticket->closed_at = now();
        }

        $ticket->save();

        TicketEvent::create([
            'ticket_id' => $ticket->id,
            'user_id' => $transitionedBy,
            'event_type' => 'ticket_status_changed',
            'payload_json' => [
                'from_state_id' => $fromState->id,
                'from_state_name' => $fromState->name,
                'to_state_id' => $toState->id,
                'to_state_name' => $toState->name,
            ],
        ]);

        return $ticket->fresh();
    }
}
