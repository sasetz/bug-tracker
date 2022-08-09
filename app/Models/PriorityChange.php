<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PriorityChange extends Model
{
    use HasFactory;
    public $timestamps = false;

    /**
     * Relation to the update it represents.
     *
     * @return MorphOne
     */
    public function ticketUpdate(): MorphOne
    {
        return $this->morphOne(Update::class, 'changeable');
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
