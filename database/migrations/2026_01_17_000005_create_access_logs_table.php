<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('duration_seconds');
            $table->timestamp('accessed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
