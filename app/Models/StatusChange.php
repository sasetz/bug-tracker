<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusChange extends Model
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
    
    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'old_status_id');
    }

    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'new_status_id');
    }
}
