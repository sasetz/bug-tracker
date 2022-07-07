<?php

namespace App\Models;

use App\Preferences\HasPreferences;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, HasPreferences;
    
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Owner of the project
     * 
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withTimestamps()->withPivot('is_admin');
    }
    
    public function preferences(): HasMany
    {
        return $this->hasMany(ProjectPreference::class, 'project_id');
    }
}
