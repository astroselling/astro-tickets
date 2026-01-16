<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained('ticket_boards')->cascadeOnDelete();
            $table->foreignId('from_state_id')->constrained('workflow_states')->cascadeOnDelete();
            $table->foreignId('to_state_id')->constrained('workflow_states')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['board_id', 'from_state_id', 'to_state_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
