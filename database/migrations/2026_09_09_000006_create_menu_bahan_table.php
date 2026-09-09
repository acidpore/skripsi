<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->cascadeOnDelete();
            $table->foreignId('bahan_pangan_id')->constrained('bahan_pangan')->cascadeOnDelete();
            $table->string('komponen');
            // Berat bersih, yaitu bagian yang dapat dimakan sesuai dasar perhitungan gizi TKPI
            $table->decimal('berat_bersih_gram', 8, 2);
            $table->timestamps();

            $table->unique(['menu_id', 'bahan_pangan_id', 'komponen']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_bahan');
    }
};
