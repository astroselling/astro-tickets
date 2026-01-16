<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class WorkflowState extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'board_id',
        'name',
        'label',
        'is_initial',
        'is_terminal',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'board_id' => 'integer',
            'name' => 'string',
            'label' => 'string',
            'is_initial' => 'boolean',
            'is_terminal' => 'boolean',
            'order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(TicketBoard::class, 'board_id');
    }

    public function fromTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'from_state_id');
    }

    public function toTransitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'to_state_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'state_id');
    }
}
