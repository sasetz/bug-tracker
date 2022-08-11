<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* @property integer $id
 * @property string $name
 * @property integer $number
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class Ticket extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'priority_id',
    ];
    
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_subscriptions')->as('subscription');
    }
    
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_assignees')
            ->withTimestamps()
            ->as('assigned');
    }
    
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(Update::class);
    }
}
