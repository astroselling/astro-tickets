<?php

declare(strict_types=1);

use App\Models\Ticket;
use App\Models\TicketBoard;
use App\Models\TicketEvent;
use App\Models\TicketType;
use App\Models\User;
use App\Models\WorkflowState;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

it('transitions ticket from new to triage', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $newState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'new')
        ->first();
    $triageState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'triage')
        ->first();

    $ticket = Ticket::create([
        'board_id' => $board->id,
        'type_id' => $type->id,
        'state_id' => $newState->id,
        'title' => 'Test Ticket',
        'created_by' => $user->id,
    ]);

    actingAs($user);

    $response = postJson("/api/v1/tickets/{$ticket->id}/transition", [
        'state_id' => $triageState->id,
    ]);

    $response->assertSuccessful();

    $ticket->refresh();
    expect($ticket->state_id)->toBe($triageState->id);
});

it('fails if transition is invalid', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $newState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'new')
        ->first();
    $closedState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'closed')
        ->first();

    $ticket = Ticket::create([
        'board_id' => $board->id,
        'type_id' => $type->id,
        'state_id' => $newState->id,
        'title' => 'Test Ticket',
        'created_by' => $user->id,
    ]);

    actingAs($user);

    $response = postJson("/api/v1/tickets/{$ticket->id}/transition", [
        'state_id' => $closedState->id,
    ]);

    $response->assertUnprocessable();
});

it('creates ticket_event when ticket status changes', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $newState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'new')
        ->first();
    $triageState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'triage')
        ->first();

    $ticket = Ticket::create([
        'board_id' => $board->id,
        'type_id' => $type->id,
        'state_id' => $newState->id,
        'title' => 'Test Ticket',
        'created_by' => $user->id,
    ]);

    actingAs($user);

    $response = postJson("/api/v1/tickets/{$ticket->id}/transition", [
        'state_id' => $triageState->id,
    ]);

    $response->assertSuccessful();

    $event = TicketEvent::where('ticket_id', $ticket->id)
        ->where('event_type', 'ticket_status_changed')
        ->first();

    expect($event)->not->toBeNull();
    expect($event->user_id)->toBe($user->id);
    expect($event->payload_json)->toHaveKey('from_state_name');
    expect($event->payload_json)->toHaveKey('to_state_name');
    expect($event->payload_json['from_state_name'])->toBe('new');
    expect($event->payload_json['to_state_name'])->toBe('triage');
});

it('sets resolved_at when transitioning to resolved state', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $inProgressState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'in_progress')
        ->first();
    $resolvedState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'resolved')
        ->first();

    $ticket = Ticket::create([
        'board_id' => $board->id,
        'type_id' => $type->id,
        'state_id' => $inProgressState->id,
        'title' => 'Test Ticket',
        'created_by' => $user->id,
    ]);

    actingAs($user);

    $response = postJson("/api/v1/tickets/{$ticket->id}/transition", [
        'state_id' => $resolvedState->id,
    ]);

    $response->assertSuccessful();

    $ticket->refresh();
    expect($ticket->resolved_at)->not->toBeNull();
});

it('sets closed_at when transitioning to terminal state', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $resolvedState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'resolved')
        ->first();
    $closedState = WorkflowState::where('board_id', $board->id)
        ->where('name', 'closed')
        ->first();

    $ticket = Ticket::create([
        'board_id' => $board->id,
        'type_id' => $type->id,
        'state_id' => $resolvedState->id,
        'title' => 'Test Ticket',
        'created_by' => $user->id,
    ]);

    actingAs($user);

    $response = postJson("/api/v1/tickets/{$ticket->id}/transition", [
        'state_id' => $closedState->id,
    ]);

    $response->assertSuccessful();

    $ticket->refresh();
    expect($ticket->closed_at)->not->toBeNull();
});
