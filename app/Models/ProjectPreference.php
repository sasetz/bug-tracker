<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/* @property integer $id
 * @property string $name
 * @property string $value
 * @property Project $project
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class ProjectPreference extends Model
{
    use HasFactory;
    
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
