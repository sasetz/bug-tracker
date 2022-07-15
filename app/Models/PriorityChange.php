<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriorityChange extends Model
{
    use HasFactory;
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function oldPriority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'old_priority_id');
    }

    public function newPriority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'new_priority_id');
    }
}
