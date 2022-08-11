<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/* @property integer $id
 * @property string $name
 * @property string $description
 * @property string $color
 * @property Project $project
 * @property Collection $tickets
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class Label extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'color',
    ];
    
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
