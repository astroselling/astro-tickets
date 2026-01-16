<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TicketBoard;
use App\Models\TicketType;
use App\Models\WorkflowState;
use App\Models\WorkflowTransition;
use Illuminate\Database\Seeder;

final class TicketingSeeder extends Seeder
{
    public function run(): void
    {
        // Boards
        $incidentsBoard = TicketBoard::create([
            'name' => 'incidents',
            'description' => 'Incidents Board',
            'is_active' => true,
        ]);

        $requestsBoard = TicketBoard::create([
            'name' => 'requests',
            'description' => 'Requests Board',
            'is_active' => true,
        ]);

        $activationBoard = TicketBoard::create([
            'name' => 'activation',
            'description' => 'Activation Board',
            'is_active' => true,
        ]);

        // Types
        $incidentType = TicketType::create([
            'board_id' => $incidentsBoard->id,
            'name' => 'incident',
            'description' => 'Incident Type',
            'is_active' => true,
        ]);

        $requestType = TicketType::create([
            'board_id' => $requestsBoard->id,
            'name' => 'request',
            'description' => 'Request Type',
            'is_active' => true,
        ]);

        $activationType = TicketType::create([
            'board_id' => $activationBoard->id,
            'name' => 'activation',
            'description' => 'Activation Type',
            'is_active' => true,
        ]);

        // Incidents Board States
        $incidentsNew = WorkflowState::create([
            'board_id' => $incidentsBoard->id,
            'name' => 'new',
            'label' => 'New',
            'is_initial' => true,
            'is_terminal' => false,
            'order' => 1,
        ]);

        $incidentsTriage = WorkflowState::create([
            'board_id' => $incidentsBoard->id,
            'name' => 'triage',
            'label' => 'Triage',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 2,
        ]);

        $incidentsInProgress = WorkflowState::create([
            'board_id' => $incidentsBoard->id,
            'name' => 'in_progress',
            'label' => 'In Progress',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 3,
        ]);

        $incidentsResolved = WorkflowState::create([
            'board_id' => $incidentsBoard->id,
            'name' => 'resolved',
            'label' => 'Resolved',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 4,
        ]);

        $incidentsClosed = WorkflowState::create([
            'board_id' => $incidentsBoard->id,
            'name' => 'closed',
            'label' => 'Closed',
            'is_initial' => false,
            'is_terminal' => true,
            'order' => 5,
        ]);

        // Incidents Board Transitions
        WorkflowTransition::create([
            'board_id' => $incidentsBoard->id,
            'from_state_id' => $incidentsNew->id,
            'to_state_id' => $incidentsTriage->id,
            'label' => 'Start Triage',
        ]);

        WorkflowTransition::create([
            'board_id' => $incidentsBoard->id,
            'from_state_id' => $incidentsTriage->id,
            'to_state_id' => $incidentsInProgress->id,
            'label' => 'Start Work',
        ]);

        WorkflowTransition::create([
            'board_id' => $incidentsBoard->id,
            'from_state_id' => $incidentsInProgress->id,
            'to_state_id' => $incidentsResolved->id,
            'label' => 'Resolve',
        ]);

        WorkflowTransition::create([
            'board_id' => $incidentsBoard->id,
            'from_state_id' => $incidentsResolved->id,
            'to_state_id' => $incidentsClosed->id,
            'label' => 'Close',
        ]);

        // Requests Board States
        $requestsNew = WorkflowState::create([
            'board_id' => $requestsBoard->id,
            'name' => 'new',
            'label' => 'New',
            'is_initial' => true,
            'is_terminal' => false,
            'order' => 1,
        ]);

        $requestsReview = WorkflowState::create([
            'board_id' => $requestsBoard->id,
            'name' => 'review',
            'label' => 'Review',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 2,
        ]);

        $requestsPlanned = WorkflowState::create([
            'board_id' => $requestsBoard->id,
            'name' => 'planned',
            'label' => 'Planned',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 3,
        ]);

        $requestsInProgress = WorkflowState::create([
            'board_id' => $requestsBoard->id,
            'name' => 'in_progress',
            'label' => 'In Progress',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 4,
        ]);

        $requestsDone = WorkflowState::create([
            'board_id' => $requestsBoard->id,
            'name' => 'done',
            'label' => 'Done',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 5,
        ]);

        $requestsClosed = WorkflowState::create([
            'board_id' => $requestsBoard->id,
            'name' => 'closed',
            'label' => 'Closed',
            'is_initial' => false,
            'is_terminal' => true,
            'order' => 6,
        ]);

        // Requests Board Transitions
        WorkflowTransition::create([
            'board_id' => $requestsBoard->id,
            'from_state_id' => $requestsNew->id,
            'to_state_id' => $requestsReview->id,
            'label' => 'Start Review',
        ]);

        WorkflowTransition::create([
            'board_id' => $requestsBoard->id,
            'from_state_id' => $requestsReview->id,
            'to_state_id' => $requestsPlanned->id,
            'label' => 'Plan',
        ]);

        WorkflowTransition::create([
            'board_id' => $requestsBoard->id,
            'from_state_id' => $requestsPlanned->id,
            'to_state_id' => $requestsInProgress->id,
            'label' => 'Start Work',
        ]);

        WorkflowTransition::create([
            'board_id' => $requestsBoard->id,
            'from_state_id' => $requestsInProgress->id,
            'to_state_id' => $requestsDone->id,
            'label' => 'Complete',
        ]);

        WorkflowTransition::create([
            'board_id' => $requestsBoard->id,
            'from_state_id' => $requestsDone->id,
            'to_state_id' => $requestsClosed->id,
            'label' => 'Close',
        ]);

        // Activation Board States
        $activationNew = WorkflowState::create([
            'board_id' => $activationBoard->id,
            'name' => 'new',
            'label' => 'New',
            'is_initial' => true,
            'is_terminal' => false,
            'order' => 1,
        ]);

        $activationWaitingCustomer = WorkflowState::create([
            'board_id' => $activationBoard->id,
            'name' => 'waiting_customer',
            'label' => 'Waiting Customer',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 2,
        ]);

        $activationInProgress = WorkflowState::create([
            'board_id' => $activationBoard->id,
            'name' => 'in_progress',
            'label' => 'In Progress',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 3,
        ]);

        $activationActivated = WorkflowState::create([
            'board_id' => $activationBoard->id,
            'name' => 'activated',
            'label' => 'Activated',
            'is_initial' => false,
            'is_terminal' => false,
            'order' => 4,
        ]);

        $activationClosed = WorkflowState::create([
            'board_id' => $activationBoard->id,
            'name' => 'closed',
            'label' => 'Closed',
            'is_initial' => false,
            'is_terminal' => true,
            'order' => 5,
        ]);

        // Activation Board Transitions
        WorkflowTransition::create([
            'board_id' => $activationBoard->id,
            'from_state_id' => $activationNew->id,
            'to_state_id' => $activationWaitingCustomer->id,
            'label' => 'Wait for Customer',
        ]);

        WorkflowTransition::create([
            'board_id' => $activationBoard->id,
            'from_state_id' => $activationWaitingCustomer->id,
            'to_state_id' => $activationInProgress->id,
            'label' => 'Start Activation',
        ]);

        WorkflowTransition::create([
            'board_id' => $activationBoard->id,
            'from_state_id' => $activationInProgress->id,
            'to_state_id' => $activationActivated->id,
            'label' => 'Activate',
        ]);

        WorkflowTransition::create([
            'board_id' => $activationBoard->id,
            'from_state_id' => $activationActivated->id,
            'to_state_id' => $activationClosed->id,
            'label' => 'Close',
        ]);
    }
}
