<?php

declare(strict_types=1);

use App\Models\Ticket;
use App\Models\TicketBoard;
use App\Models\TicketComment;
use App\Models\TicketEvent;
use App\Models\TicketType;
use App\Models\User;
use App\Models\WorkflowState;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

it('adds a comment to a ticket', function (): void {
    $user = User::factory()->create();
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

    $response = postJson("/api/v1/tickets/{$ticket->id}/comments", [
        'content' => 'This is a test comment',
        'is_internal' => false,
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'id',
            'ticket_id',
            'user_id',
            'content',
            'is_internal',
        ]);

    $comment = TicketComment::find($response->json('id'));
    expect($comment)->not->toBeNull();
    expect($comment->content)->toBe('This is a test comment');
    expect($comment->is_internal)->toBeFalse();
    expect($comment->user_id)->toBe($user->id);
});

it('creates ticket_event when comment is added', function (): void {
    $user = User::factory()->create();
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

    $response = postJson("/api/v1/tickets/{$ticket->id}/comments", [
        'content' => 'This is a test comment',
    ]);

    $response->assertSuccessful();

    $commentId = $response->json('id');
    $event = TicketEvent::where('ticket_id', $ticket->id)
        ->where('event_type', 'comment_added')
        ->first();

    expect($event)->not->toBeNull();
    expect($event->user_id)->toBe($user->id);
    expect($event->payload_json)->toHaveKey('comment_id');
    expect($event->payload_json['comment_id'])->toBe($commentId);
    expect($event->payload_json['is_internal'])->toBeFalse();
});

it('creates internal comment when is_internal is true', function (): void {
    $user = User::factory()->create();
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

    $response = postJson("/api/v1/tickets/{$ticket->id}/comments", [
        'content' => 'This is an internal comment',
        'is_internal' => true,
    ]);

    $response->assertSuccessful();

    $comment = TicketComment::find($response->json('id'));
    expect($comment->is_internal)->toBeTrue();
});
