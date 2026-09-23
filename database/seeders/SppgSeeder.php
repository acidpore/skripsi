<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Sppg;
use Illuminate\Database\Seeder;
use RuntimeException;

class SppgSeeder extends Seeder
{
    private const BERKAS = 'dataset/sppg_data_with_coords_clean.csv';
    private const UKURAN_BATCH = 500;

    public function run(): void
    {
        $berkas = base_path(self::BERKAS);

        if (!is_readable($berkas)) {
            throw new RuntimeException('Berkas '.self::BERKAS.' tidak ditemukan atau tidak dapat dibaca.');
        }

        $pointer = fopen($berkas, 'rb');
        $judul = fgetcsv($pointer);
        $batch = [];
        $tersimpan = 0;
        $tanpaKoordinat = 0;

        while (($baris = fgetcsv($pointer)) !== false) {
            $data = @array_combine($judul, $baris);

            if ($data === false || trim((string) $data['nama_sppg']) === '') {
                continue;
            }

            $latitude = $this->koordinat($data['latitude']);
            $longitude = $this->koordinat($data['longitude']);

            if ($latitude === null || $longitude === null) {
                $tanpaKoordinat++;
            }

            $batch[] = [
                'nama' => trim($data['nama_sppg']),
                'provinsi' => trim($data['provinsi']),
                'kab_kota' => trim($data['kab_kota']),
                'kecamatan' => trim($data['kecamatan']),
                'kelurahan_desa' => trim($data['kelurahan_desa']),
                'alamat' => trim($data['alamat']),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $tersimpan++;

            if (count($batch) >= self::UKURAN_BATCH) {
                Sppg::insert($batch);
                $batch = [];
            }
        }

        fclose($pointer);

        if ($batch !== []) {
            Sppg::insert($batch);
        }

        $this->command->info('SPPG tersimpan: '.$tersimpan.', tanpa koordinat: '.$tanpaKoordinat.'.');
    }

    private function koordinat(mixed $nilai): ?float
    {
        $bersih = trim((string) $nilai);

        return $bersih === '' || !is_numeric($bersih) ? null : (float) $bersih;
    }
}
