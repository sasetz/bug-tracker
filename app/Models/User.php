<?php

namespace App\Models;

use App\Preferences\HasPreferences;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/* @property integer $id
 * @property string $name
 * @property string $email
 * @property Carbon $email_verified_at
 * @property string $password
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasPreferences;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * An array that represents what preferences a model has
     * and what are the default values on that model
     * 
     * @var string[]
     */
    protected array $preferencesDefaults = [
        'language' => 'en',
        'notification_channels' => [
            'email',
            'database',
        ],
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('is_admin')
            ->withTimestamps();
    }
    
    public function receivedInvites(): HasMany
    {
        return $this->hasMany(Invite::class, 'receiver_id');
    }

    public function sentInvites(): HasMany
    {
        return $this->hasMany(Invite::class, 'user_id');
    }
    
    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class, 'user_id');
    }
    
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'author_id');
    }
    
    public function subscribedTickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_subscriptions')
            ->as('subscription');
    }
    
    public function assignedTickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'ticket_assignees')
            ->withTimestamps()
            ->as('assigned');
    }

    /**
     * A user can make many updates to a ticket.
     * 
     * @return HasMany
     */
    public function updates(): HasMany
    {
        return $this->hasMany(Update::class);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Model helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(Project $project): bool
    {
        return $this->ownedProjects()->get()->contains($project) || 
            ($project->users->find($this) != null && 
                $project->users->find($this)->pivot->is_admin === 1);
    }

    public function isOwner(Project $project): bool
    {
        return $this->ownedProjects()->get()->contains($project);
    }
    
    public function isAdded(Project $project): bool
    {
        return $this->isAdmin($project) || 
            $project->users()->get()->contains($this);
    }
}
