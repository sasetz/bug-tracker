<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/* @property integer $id
 * @property Ticket $ticket
 * @property User $user
 * @property mixed $changeable
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class Update extends Model
{
    use HasFactory;

    /**
     * Update model has a link to an actual update payload, including old and new values.
     * 
     * @return MorphTo
     */
    public function changeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
