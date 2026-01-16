<?php

declare(strict_types=1);

use App\Models\Ticket;
use App\Models\TicketActor;
use App\Models\TicketBoard;
use App\Models\TicketEvent;
use App\Models\TicketType;
use App\Models\User;
use App\Models\WorkflowState;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

it('assigns a ticket to a user', function (): void {
    $user = User::factory()->create();
    $assignee = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $initialState = WorkflowState::where('board_id', $board->id)
        ->where('is_initial', true)
        ->first();

    $ticket = Ticket::create([
        'board_id' => $board->id,
        'type_id' => $type->id,
        'state_id' => $initialState->id,
        'title' => 'Test Ticket',
        'created_by' => $user->id,
    ]);

    actingAs($user);

    $response = postJson("/api/v1/tickets/{$ticket->id}/assign", [
        'user_id' => $assignee->id,
    ]);

    $response->assertSuccessful();

    $ticketActor = TicketActor::where('ticket_id', $ticket->id)
        ->where('user_id', $assignee->id)
        ->where('role', 'assignee')
        ->first();

    expect($ticketActor)->not->toBeNull();
});

it('creates ticket_event when ticket is assigned', function (): void {
    $user = User::factory()->create();
    $assignee = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $initialState = WorkflowState::where('board_id', $board->id)
        ->where('is_initial', true)
        ->first();

    $ticket = Ticket::create([
        'board_id' => $board->id,
        'type_id' => $type->id,
        'state_id' => $initialState->id,
        'title' => 'Test Ticket',
        'created_by' => $user->id,
    ]);

    actingAs($user);

    $response = postJson("/api/v1/tickets/{$ticket->id}/assign", [
        'user_id' => $assignee->id,
    ]);

    $response->assertSuccessful();

    $event = TicketEvent::where('ticket_id', $ticket->id)
        ->where('event_type', 'ticket_assigned')
        ->first();

    expect($event)->not->toBeNull();
    expect($event->user_id)->toBe($user->id);
    expect($event->payload_json)->toHaveKey('assigned_to_user_id');
    expect($event->payload_json['assigned_to_user_id'])->toBe($assignee->id);
    expect($event->payload_json['assigned_to_user_name'])->toBe($assignee->name);
});
