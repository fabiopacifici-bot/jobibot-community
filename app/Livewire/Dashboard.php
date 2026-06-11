<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\Simulation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $simulations = collect();
        $candidate = null;

        if (Auth::check()) {
            $candidate = Candidate::where('user_id', Auth::id())->first();
            if ($candidate) {
                $simulations = Simulation::where('candidate_id', $candidate->id)
                    ->with('messages')
                    ->latest()
                    ->limit(10)
                    ->get();
            }
        }

        return view('livewire.dashboard', [
            'candidate' => $candidate,
            'simulations' => $simulations,
            'totalSims' => $simulations->count(),
            'completedSims' => $simulations->where('status', 'completed')->count(),
            'avgScore' => $simulations->where('status', 'completed')->avg('simulation_score'),
        ]);
    }
}
