<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TicketType extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'board_id',
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
            'board_id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(TicketBoard::class, 'board_id');
    }

    public function subtypes(): HasMany
    {
        return $this->hasMany(TicketSubtype::class, 'type_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'type_id');
    }
}
