<?php

namespace App\Models\TicketChanges;

use App\Models\Ticket;
use App\Models\Update;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property User $user
 * @property Ticket $ticket
 * @property string $old
 * @property string $new
 */
class TitleChange extends Model
{
    use HasFactory;
    public $timestamps = false;
    public string $type = 'title';

    /**
     * Relation to the update it represents.
     *
     * @return MorphOne
     */
    public function ticketUpdate(): MorphOne
    {
        return $this->morphOne(Update::class, 'changeable');
    }
}
