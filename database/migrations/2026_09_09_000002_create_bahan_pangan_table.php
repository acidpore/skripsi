<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_pangan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('jenis')->nullable();
            $table->string('kelompok')->nullable();

            $table->decimal('energi_kal', 8, 2)->default(0);
            $table->decimal('protein_g', 8, 2)->default(0);
            $table->decimal('lemak_g', 8, 2)->default(0);
            $table->decimal('karbohidrat_g', 8, 2)->default(0);

            // Nullable karena tkpi_bdd.csv belum memuat tiga zat gizi ini, menunggu verifikasi TKPI.xlsx
            $table->decimal('kalsium_mg', 8, 2)->nullable();
            $table->decimal('besi_mg', 8, 2)->nullable();
            $table->decimal('vitamin_c_mg', 8, 2)->nullable();

            $table->decimal('bdd_persen', 5, 2)->default(100);
            $table->timestamps();

            $table->index('kelompok');
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_pangan');
    }
};
