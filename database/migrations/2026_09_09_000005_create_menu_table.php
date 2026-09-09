<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->foreignId('akg_sekali_makan_id')->constrained('akg_sekali_makan')->cascadeOnDelete();
            // Null berarti menu acuan nasional yang dapat dipakai seluruh SPPG
            $table->foreignId('sppg_id')->nullable()->constrained('sppg')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['akg_sekali_makan_id', 'sppg_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
