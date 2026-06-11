<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_advertisement_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->integer('cv_match_score')->nullable();
            $table->integer('simulation_score')->nullable();
            $table->text('considerations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};
