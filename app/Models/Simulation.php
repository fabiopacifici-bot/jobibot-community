<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Simulation extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'candidate_id',
        'status',
        'job_advertisement_id',
        'cv_match_score',
        'simulation_score',
        'considerations',
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobAdvertisement()
    {
        return $this->belongsTo(JobAdvertisement::class);
    }

    public function messages()
    {
        return $this->hasMany(SimulationMessage::class, 'simulation_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Simulation $simulation) {
            $simulation->uuid = (string) Str::uuid();
        });
    }
}