<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Ticketing\AddCommentAction;
use App\Actions\Ticketing\AssignTicketAction;
use App\Actions\Ticketing\CreateTicketAction;
use App\Actions\Ticketing\TransitionTicketAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticketing\AddCommentRequest;
use App\Http\Requests\Ticketing\AssignTicketRequest;
use App\Http\Requests\Ticketing\StoreTicketRequest;
use App\Http\Requests\Ticketing\TransitionTicketRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TicketsController extends Controller
{
    public function __construct(
        private readonly CreateTicketAction $createTicketAction,
        private readonly AssignTicketAction $assignTicketAction,
        private readonly AddCommentAction $addCommentAction,
        private readonly TransitionTicketAction $transitionTicketAction
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['board', 'type', 'subtype', 'state', 'creator']);

        if ($request->has('board_id')) {
            $query->where('board_id', $request->integer('board_id'));
        }

        if ($request->has('type_id')) {
            $query->where('type_id', $request->integer('type_id'));
        }

        if ($request->has('state_id')) {
            $query->where('state_id', $request->integer('state_id'));
        }

        if ($request->has('created_by')) {
            $query->where('created_by', $request->integer('created_by'));
        }

        $tickets = $query->latest()->paginate(15);

        return response()->json($tickets);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $user = $request->user() ?? User::firstOrFail();
        
        $ticket = $this->createTicketAction->execute(
            boardId: $request->integer('board_id'),
            typeId: $request->integer('type_id'),
            subtypeId: $request->has('subtype_id') ? $request->integer('subtype_id') : null,
            title: $request->string('title')->toString(),
            description: $request->has('description') ? $request->string('description')->toString() : null,
            createdBy: $user->id,
            priority: $request->integer('priority', 0)
        );

        return response()->json($ticket->load(['board', 'type', 'subtype', 'state', 'creator']), 201);
    }

    public function show(Ticket $ticket): JsonResponse
    {
        $ticket->load(['board', 'type', 'subtype', 'state', 'creator', 'actors.user', 'comments.user', 'tags', 'events.user']);

        return response()->json($ticket);
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $user = $request->user() ?? User::firstOrFail();
        
        $ticket = $this->assignTicketAction->execute(
            ticketId: $ticket->id,
            userId: $request->integer('user_id'),
            assignedBy: $user->id
        );

        return response()->json($ticket->load(['board', 'type', 'subtype', 'state', 'creator', 'actors.user']));
    }

    public function transition(TransitionTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $user = $request->user() ?? User::firstOrFail();
        
        $ticket = $this->transitionTicketAction->execute(
            ticketId: $ticket->id,
            toStateId: $request->integer('state_id'),
            transitionedBy: $user->id
        );

        return response()->json($ticket->load(['board', 'type', 'subtype', 'state', 'creator']));
    }

    public function comments(AddCommentRequest $request, Ticket $ticket): JsonResponse
    {
        $user = $request->user() ?? User::firstOrFail();
        
        $comment = $this->addCommentAction->execute(
            ticketId: $ticket->id,
            userId: $user->id,
            content: $request->string('content')->toString(),
            isInternal: $request->boolean('is_internal', false)
        );

        return response()->json($comment->load(['user']), 201);
    }
}
