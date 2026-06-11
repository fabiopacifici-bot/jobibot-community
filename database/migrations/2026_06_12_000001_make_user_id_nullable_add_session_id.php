<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Make user_id nullable for guest CV uploads
            $table->foreignId('user_id')->nullable()->change();
            // Add session_id for guest users (stored by Laravel session)
            $table->string('session_id')->nullable()->after('user_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn('session_id');
        });
    }
};