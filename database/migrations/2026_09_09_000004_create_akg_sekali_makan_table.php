<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akg_sekali_makan', function (Blueprint $table) {
            $table->id();
            $table->string('kelompok_sasaran');
            $table->string('waktu_distribusi');
            $table->string('persentase_akg');

            $table->decimal('energi_kkal_min', 8, 2);
            $table->decimal('energi_kkal_max', 8, 2);
            $table->decimal('protein_g_min', 8, 2);
            $table->decimal('protein_g_max', 8, 2);
            $table->decimal('lemak_g_min', 8, 2);
            $table->decimal('lemak_g_max', 8, 2);
            $table->decimal('karbohidrat_g_min', 8, 2);
            $table->decimal('karbohidrat_g_max', 8, 2);

            $table->timestamps();

            $table->unique(['kelompok_sasaran', 'waktu_distribusi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akg_sekali_makan');
    }
};
