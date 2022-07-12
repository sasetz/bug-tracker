<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UpdateType extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
    ];
    
    public $timestamps = false;
    
    public function updates(): HasMany
    {
        return $this->hasMany(Update::class, 'update_type_id');
    }
}
