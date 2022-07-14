<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property User $user
 * @property Ticket $ticket
 * @property string $old
 * @property string $new
 */
class TitleChange extends Model
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
}
