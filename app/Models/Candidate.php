<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'cv', 'cv_path', 'bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class);
    }
}
