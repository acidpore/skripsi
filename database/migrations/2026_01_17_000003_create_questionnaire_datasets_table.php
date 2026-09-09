<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('q1', 1);
            $table->char('q2', 1);
            $table->char('q3', 1);
            $table->char('q4', 1);
            $table->char('q5', 1);
            $table->char('q6', 1);
            $table->char('q7', 1);
            $table->char('q8', 1);
            $table->char('q9', 1);
            $table->char('q10', 1);
            $table->char('q11', 1);
            $table->char('q12', 1);
            $table->char('q13', 1);
            $table->char('q14', 1);
            $table->char('q15', 1);
            $table->char('q16', 1);
            $table->enum('manual_label', ['Visual', 'Aural', 'ReadWrite', 'Kinesthetic'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_datasets');
    }
};
