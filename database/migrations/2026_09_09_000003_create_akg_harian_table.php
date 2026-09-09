<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akg_harian', function (Blueprint $table) {
            $table->id();
            $table->string('kelompok');
            $table->string('kelompok_umur');
            $table->decimal('berat_badan_kg', 5, 1)->nullable();
            $table->decimal('tinggi_badan_cm', 5, 1)->nullable();
            $table->decimal('energi_kkal', 8, 2);
            $table->decimal('protein_g', 8, 2);
            $table->decimal('lemak_total_g', 8, 2);
            $table->decimal('karbohidrat_g', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akg_harian');
    }
};
