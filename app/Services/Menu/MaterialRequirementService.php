<?php

declare(strict_types=1);

namespace App\Services\Menu;

use App\Models\BahanPangan;
use App\Models\KebutuhanBahan;
use App\Models\Rekomendasi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MaterialRequirementService
{
    public function calculateForRekomendasi(Rekomendasi $rekomendasi): Collection
    {
        $komposisi = $rekomendasi->komposisi_terpilih['komposisi'] ?? [];

        if ($komposisi === []) {
            return collect();
        }

        $kebutuhan = $this->hitung($komposisi, $rekomendasi->jumlah_porsi);

        DB::transaction(function () use ($rekomendasi, $kebutuhan): void {
            $rekomendasi->kebutuhanBahan()->delete();

            foreach ($kebutuhan as $baris) {
                KebutuhanBahan::create([
                    'rekomendasi_id' => $rekomendasi->id,
                    'bahan_pangan_id' => $baris['bahan_pangan_id'],
                    'berat_bersih_gram' => $baris['berat_bersih_gram'],
                    'berat_kotor_gram' => $baris['berat_kotor_gram'],
                ]);
            }
        });

        return $rekomendasi->kebutuhanBahan()->with('bahanPangan')->get();
    }

    public function hitung(array $komposisi, int $jumlahPorsi): array
    {
        $bahanPerId = BahanPangan::query()
            ->whereIn('id', array_column($komposisi, 'bahan_pangan_id'))
            ->get()
            ->keyBy('id');

        $kebutuhan = [];

        foreach ($komposisi as $komponen) {
            $bahan = $bahanPerId->get($komponen['bahan_pangan_id']);

            if ($bahan === null) {
                continue;
            }

            $beratBersih = ((float) $komponen['berat_bersih_gram']) * $jumlahPorsi;
            $baris = [
                'bahan_pangan_id' => $bahan->id,
                'nama' => $bahan->nama,
                'berat_bersih_gram' => $beratBersih,
                'berat_kotor_gram' => $this->beratKotor($beratBersih, $bahan->bdd_persen),
            ];

            if (!isset($kebutuhan[$bahan->id])) {
                $kebutuhan[$bahan->id] = $baris;

                continue;
            }

            $kebutuhan[$bahan->id]['berat_bersih_gram'] += $baris['berat_bersih_gram'];
            $kebutuhan[$bahan->id]['berat_kotor_gram'] += $baris['berat_kotor_gram'];
        }

        return $kebutuhan;
    }

    // Konversi BDD, berat kotor = berat bersih dibagi BDD dikali 100
    private function beratKotor(float $beratBersihGram, ?float $bddPersen): float
    {
        $bdd = $bddPersen === null || $bddPersen <= 0.0
            ? (float) BahanPangan::BDD_PENUH_PERSEN
            : $bddPersen;

        return $beratBersihGram / $bdd * BahanPangan::BASIS_GIZI_GRAM;
    }
}
