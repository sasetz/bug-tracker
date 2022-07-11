<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Update extends Model
{
    use HasFactory;
    
    /*
     * An update is an immutable resource. It is created and managed automatically,
     * a user cannot perform any actions with it except for viewing. It can
     * be deleted only if the ticket that contains it, is deleted, too.
     */
    
    public function type(): BelongsTo
    {
        return $this->belongsTo(UpdateType::class, 'update_type_id');
    }
    
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
