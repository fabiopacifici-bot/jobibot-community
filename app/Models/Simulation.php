<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Simulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'status',
        'job_advertisement_id',
        'cv_match_score',
        'simulation_score',
        'considerations',
    ];

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
        return $this->hasMany(SimulationMessage::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Simulation $simulation) {
            if (empty($simulation->uuid)) {
                $simulation->uuid = (string) Str::uuid();
            }
        });
    }
}
