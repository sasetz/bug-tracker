<?php

namespace App\Models\TicketChanges;

use App\Models\Ticket;
use App\Models\Update;
use App\Models\User;
use Database\Factories\TicketChanges\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $id
 * @property User $user
 * @property Ticket $ticket
 * @property string $body
 */
class Comment extends Model
{
    use HasFactory;
    public $timestamps = false;
    public string $type = 'comment';

    public function ticketUpdate(): MorphOne
    {
        return $this->morphOne(Update::class, 'changeable');
    }
    
    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }
}
