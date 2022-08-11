<?php

namespace App\Models\TicketChanges;

use App\Models\Update;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AssigneeChange extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    public string $type = 'assignee';

    /**
     * Relation to the update it represents.
     * 
     * @return MorphOne
     */
    public function ticketUpdate(): MorphOne
    {
        return $this->morphOne(Update::class, 'changeable');
    }
    
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
