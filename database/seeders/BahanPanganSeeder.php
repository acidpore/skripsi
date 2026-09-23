<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BahanPangan;
use Illuminate\Database\Seeder;
use RuntimeException;

class BahanPanganSeeder extends Seeder
{
    private const BERKAS = 'dataset/tkpi_bdd.csv';
    private const KODE_VALID = '/^[A-Z]{2}\d+$/';
    private const UKURAN_BATCH = 500;

    public function run(): void
    {
        $berkas = base_path(self::BERKAS);

        if (!is_readable($berkas)) {
            throw new RuntimeException('Berkas '.self::BERKAS.' tidak ditemukan atau tidak dapat dibaca.');
        }

        $pointer = fopen($berkas, 'rb');
        $judul = $this->judulTanpaBom(fgetcsv($pointer));
        $batch = [];
        $dilewati = 0;

        while (($baris = fgetcsv($pointer)) !== false) {
            $data = $this->petakan($judul, $baris);

            // Baris kedua berkas sumber berisi keterangan tabel, bukan data bahan, sehingga dikenali lewat pola kode
            if ($data === null) {
                $dilewati++;

                continue;
            }

            $batch[] = $data;

            if (count($batch) >= self::UKURAN_BATCH) {
                $this->simpan($batch);
                $batch = [];
            }
        }

        fclose($pointer);
        $this->simpan($batch);

        $this->command->info('Bahan pangan tersimpan. Baris dilewati: '.$dilewati.'.');
    }

    // Berkas sumber diekspor dengan BOM UTF-8, tanpa dibuang kunci kolom pertama tidak akan pernah cocok
    private function judulTanpaBom(array $judul): array
    {
        $judul[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $judul[0]);

        return $judul;
    }

    private function petakan(array $judul, array $baris): ?array
    {
        $data = @array_combine($judul, $baris);

        if ($data === false || !preg_match(self::KODE_VALID, (string) ($data['kode'] ?? ''))) {
            return null;
        }

        return [
            'kode' => $data['kode'],
            'nama' => trim((string) $data['nama_bahan_makanan']),
            'jenis' => trim((string) $data['jenis']),
            'kelompok' => trim((string) $data['kelompok']),
            'energi_kal' => $this->angka($data['energi_kal']),
            'protein_g' => $this->angka($data['protein_g']),
            'lemak_g' => $this->angka($data['lemak_g']),
            'karbohidrat_g' => $this->angka($data['karbohidrat_g']),
            'bdd_persen' => $this->angkaAtauNull($data['bdd_persen']),
        ];
    }

    // Angka gizi pada TKPI memakai koma sebagai pemisah desimal
    private function angka(mixed $nilai): float
    {
        return (float) str_replace(',', '.', trim((string) $nilai));
    }

    // Tanda hubung pada TKPI berarti nilai tidak tersedia, berbeda arti dengan nol dan tidak boleh disamakan
    private function angkaAtauNull(mixed $nilai): ?float
    {
        $bersih = trim((string) $nilai);

        return $bersih === '-' || $bersih === '' ? null : $this->angka($bersih);
    }

    private function simpan(array $batch): void
    {
        if ($batch !== []) {
            BahanPangan::upsert($batch, ['kode']);
        }
    }
}
