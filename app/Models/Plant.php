<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    /** @use HasFactory<\Database\Factories\PlantFactory> */
    use HasFactory;
    protected $fillable = ['name', 'enabled'];

    /**
     * Get the areas associated with the plant.
     */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
