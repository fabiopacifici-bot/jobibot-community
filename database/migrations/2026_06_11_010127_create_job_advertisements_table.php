<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('Fulltime');
            $table->string('salary')->nullable();
            $table->string('work_from')->default('Remote');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source')->nullable(); // 'remotive', 'manual', 'ai-generated'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_advertisements');
    }
};