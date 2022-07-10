<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;
    
    /*****************************************
     * 
     * Status IDS:
     * 
     * - Open --------> 1
     * - Closed ------> 2
     * - Ignored -----> 3
     * 
     * 
     *****************************************
     */
    
    protected $fillable = [
        'name',
    ];
    
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
