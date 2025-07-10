<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actionplan extends Model
{
    /** @use HasFactory<\Database\Factories\ActionplanFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plant_id',
        'leader_id',
        'user_id', //a quien esta asignado el plan de accion
        'created_id',
        'date_start',
        'date_end',
        'description',
        'status',
        'priority',
    ]

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_id');
    }
    
}
