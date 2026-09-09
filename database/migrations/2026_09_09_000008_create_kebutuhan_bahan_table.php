<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebutuhan_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekomendasi_id')->constrained('rekomendasi')->cascadeOnDelete();
            $table->foreignId('bahan_pangan_id')->constrained('bahan_pangan')->cascadeOnDelete();
            $table->decimal('berat_bersih_gram', 12, 2);
            $table->decimal('berat_kotor_gram', 12, 2);
            $table->timestamps();

            $table->unique(['rekomendasi_id', 'bahan_pangan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_bahan');
    }
};
