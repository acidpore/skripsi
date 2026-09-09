<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Null hanya untuk admin, operator wajib terikat pada satu SPPG
            $table->foreignId('sppg_id')->nullable()->after('id')->constrained('sppg')->nullOnDelete();
            $table->string('role')->default('operator')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sppg_id']);
            $table->dropColumn(['sppg_id', 'role']);
        });
    }
};
