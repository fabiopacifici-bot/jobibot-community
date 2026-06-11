<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAdvertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'salary',
        'work_from',
        'description',
        'requirements',
        'source_url',
        'source',
    ];

    public function simulations()
    {
        return $this->hasMany(Simulation::class);
    }
}