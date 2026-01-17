<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TicketBoard extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function types(): HasMany
    {
        return $this->hasMany(TicketType::class, 'board_id');
    }

    public function states(): HasMany
    {
        return $this->hasMany(WorkflowState::class, 'board_id');
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(WorkflowTransition::class, 'board_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'board_id');
    }
}
