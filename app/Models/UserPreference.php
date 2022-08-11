<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/* @property integer $id
 * @property string $name
 * @property string $value
 * @property User $user
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class UserPreference extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'value',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
