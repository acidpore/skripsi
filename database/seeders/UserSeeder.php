<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Sppg;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    // Akun uji coba, kata sandi wajib diganti sebelum dipakai di luar lingkungan pengembangan
    private const KATA_SANDI_UJI = 'password';

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@spk-mbg.test'],
            [
                'name' => 'Admin SPK MBG',
                'password' => self::KATA_SANDI_UJI,
                'role' => User::ROLE_ADMIN,
                'sppg_id' => null,
            ]
        );

        $sppgPertama = Sppg::first();

        if ($sppgPertama === null) {
            $this->command->warn('Belum ada data SPPG, operator uji coba tidak dibuat.');

            return;
        }

        User::updateOrCreate(
            ['email' => 'operator@spk-mbg.test'],
            [
                'name' => 'Operator '.$sppgPertama->nama,
                'password' => self::KATA_SANDI_UJI,
                'role' => User::ROLE_OPERATOR,
                'sppg_id' => $sppgPertama->id,
            ]
        );
    }
}
