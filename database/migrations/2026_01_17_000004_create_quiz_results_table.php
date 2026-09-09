<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->enum('quiz_type', ['pretest', 'posttest', 'regular'])->default('regular');
            $table->unsignedTinyInteger('score');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};
