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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained('ticket_boards')->cascadeOnDelete();
            $table->foreignId('type_id')->constrained('ticket_types')->cascadeOnDelete();
            $table->foreignId('subtype_id')->nullable()->constrained('ticket_subtypes')->nullOnDelete();
            $table->foreignId('state_id')->constrained('workflow_states')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index(['board_id', 'state_id']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
