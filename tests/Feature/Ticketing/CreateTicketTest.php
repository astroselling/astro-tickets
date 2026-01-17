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

it('creates a ticket successfully', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $initialState = WorkflowState::where('board_id', $board->id)
        ->where('is_initial', true)
        ->first();

    actingAs($user);

    $response = postJson('/api/v1/tickets', [
        'board_id' => $board->id,
        'type_id' => $type->id,
        'title' => 'Test Ticket',
        'description' => 'Test Description',
        'priority' => 5,
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'id',
            'board_id',
            'type_id',
            'title',
            'description',
            'state_id',
            'created_by',
            'priority',
        ]);

    $ticket = Ticket::find($response->json('id'));
    expect($ticket)->not->toBeNull();
    expect($ticket->title)->toBe('Test Ticket');
    expect($ticket->description)->toBe('Test Description');
    expect($ticket->state_id)->toBe($initialState->id);
    expect($ticket->created_by)->toBe($user->id);
    expect($ticket->priority)->toBe(5);
});

it('creates a ticket_event when ticket is created', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();

    actingAs($user);

    $response = postJson('/api/v1/tickets', [
        'board_id' => $board->id,
        'type_id' => $type->id,
        'title' => 'Test Ticket',
        'description' => 'Test Description',
    ]);

    $response->assertSuccessful();

    $ticketId = $response->json('id');
    $event = TicketEvent::where('ticket_id', $ticketId)
        ->where('event_type', 'ticket_created')
        ->first();

    expect($event)->not->toBeNull();
    expect($event->user_id)->toBe($user->id);
    expect($event->payload_json)->toHaveKey('title');
    expect($event->payload_json['title'])->toBe('Test Ticket');
});

it('sets initial state correctly when creating ticket', function (): void {
    $user = User::factory()->create();
    $board = TicketBoard::where('name', 'incidents')->first();
    $type = TicketType::where('board_id', $board->id)->first();
    $initialState = WorkflowState::where('board_id', $board->id)
        ->where('is_initial', true)
        ->first();

    actingAs($user);

    $response = postJson('/api/v1/tickets', [
        'board_id' => $board->id,
        'type_id' => $type->id,
        'title' => 'Test Ticket',
    ]);

    $response->assertSuccessful();

    $ticket = Ticket::find($response->json('id'));
    expect($ticket->state_id)->toBe($initialState->id);
});
