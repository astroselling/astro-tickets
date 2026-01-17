<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Ticket extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'board_id',
        'type_id',
        'subtype_id',
        'state_id',
        'title',
        'description',
        'created_by',
        'resolved_at',
        'closed_at',
        'priority',
    ];

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'board_id' => 'integer',
            'type_id' => 'integer',
            'subtype_id' => 'integer',
            'state_id' => 'integer',
            'title' => 'string',
            'description' => 'string',
            'created_by' => 'integer',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'priority' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(TicketBoard::class, 'board_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'type_id');
    }

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(TicketSubtype::class, 'subtype_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(WorkflowState::class, 'state_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actors(): HasMany
    {
        return $this->hasMany(TicketActor::class, 'ticket_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class, 'ticket_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'ticket_tags', 'ticket_id', 'tag_id');
    }
}
