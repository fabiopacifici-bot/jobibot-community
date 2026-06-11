<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function simulation()
    {
        return $this->belongsTo(Simulation::class, 'simulation_id');
    }
}
