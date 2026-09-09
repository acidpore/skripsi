<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sppg', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('provinsi');
            $table->string('kab_kota');
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan_desa')->nullable();
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index('provinsi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sppg');
    }
};
