<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sppg_id')->constrained('sppg')->cascadeOnDelete();
            $table->foreignId('akg_sekali_makan_id')->constrained('akg_sekali_makan')->cascadeOnDelete();
            $table->foreignId('menu_terpilih_id')->nullable()->constrained('menu')->nullOnDelete();
            $table->unsignedInteger('jumlah_porsi');

            $table->json('bobot_entropy');
            $table->json('hasil_ranking_topsis');
            $table->json('hasil_ranking_saw');
            $table->decimal('koefisien_spearman', 6, 4)->nullable();

            $table->timestamps();

            $table->index(['sppg_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};
