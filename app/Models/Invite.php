<?php

namespace App\Models;

use App\Http\Resources\ProjectResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/* @property integer $id
 * @property boolean $accepted
 * @property User $user
 * @property User $receiver
 * @property Project $project
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class Invite extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'receiver_id',
        'project_id',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
