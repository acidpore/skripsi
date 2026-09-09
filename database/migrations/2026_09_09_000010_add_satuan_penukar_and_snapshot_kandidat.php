<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_pangan', function (Blueprint $table) {
            // Berat bersih satu satuan penukar menurut Juknis BGN, null berarti bahan tidak terdaftar sebagai penukar resmi
            $table->decimal('berat_per_sp_gram', 8, 2)->nullable()->after('bdd_persen');
        });

        Schema::table('rekomendasi', function (Blueprint $table) {
            // Snapshot komposisi kandidat terpilih agar riwayat rekomendasi tetap dapat ditelusuri meski data induk berubah
            $table->json('komposisi_terpilih')->nullable()->after('menu_terpilih_id');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_pangan', function (Blueprint $table) {
            $table->dropColumn('berat_per_sp_gram');
        });

        Schema::table('rekomendasi', function (Blueprint $table) {
            $table->dropColumn('komposisi_terpilih');
        });
    }
};
