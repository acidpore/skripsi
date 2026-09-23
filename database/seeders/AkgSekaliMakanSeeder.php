<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AkgSekaliMakan;
use Illuminate\Database\Seeder;
use RuntimeException;

class AkgSekaliMakanSeeder extends Seeder
{
    private const BERKAS = 'dataset/tabel_akg_satu_kali_mbg_BGN.csv';

    public function run(): void
    {
        $berkas = base_path(self::BERKAS);

        if (!is_readable($berkas)) {
            throw new RuntimeException('Berkas '.self::BERKAS.' tidak ditemukan atau tidak dapat dibaca.');
        }

        $pointer = fopen($berkas, 'rb');
        $judul = fgetcsv($pointer);
        $tersimpan = 0;

        while (($baris = fgetcsv($pointer)) !== false) {
            $data = @array_combine($judul, $baris);

            if ($data === false || trim((string) $data['Kelompok_Sasaran']) === '') {
                continue;
            }

            AkgSekaliMakan::updateOrCreate(
                [
                    'kelompok_sasaran' => trim($data['Kelompok_Sasaran']),
                    'waktu_distribusi' => trim($data['Waktu_Distribusi']),
                ],
                [
                    'persentase_akg' => trim($data['Persentase_AKG']),
                    'energi_kkal_min' => (float) $data['Energi_kkal_min'],
                    'energi_kkal_max' => (float) $data['Energi_kkal_max'],
                    'protein_g_min' => (float) $data['Protein_g_min'],
                    'protein_g_max' => (float) $data['Protein_g_max'],
                    'lemak_g_min' => (float) $data['Lemak_g_min'],
                    'lemak_g_max' => (float) $data['Lemak_g_max'],
                    'karbohidrat_g_min' => (float) $data['Karbohidrat_g_min'],
                    'karbohidrat_g_max' => (float) $data['Karbohidrat_g_max'],
                ]
            );

            $tersimpan++;
        }

        fclose($pointer);

        $this->command->info('AKG sekali makan tersimpan: '.$tersimpan.'.');
    }
}
