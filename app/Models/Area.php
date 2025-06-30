<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    /** @use HasFactory<\Database\Factories\PlantFactory> */
    use HasFactory;
    protected $fillable = ['plant_id', 'name', 'enabled'];

    /**
     * Get the plant that owns the area.
     */
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
