<?php

namespace App\Models\TicketChanges;

use App\Models\Label;
use App\Models\Update;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class LabelChange extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    public string $type = 'label';

    /**
     * Relation to the update it represents.
     *
     * @return MorphOne
     */
    public function ticketUpdate(): MorphOne
    {
        return $this->morphOne(Update::class, 'changeable');
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }
}
