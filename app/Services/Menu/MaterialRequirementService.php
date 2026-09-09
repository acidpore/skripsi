<?php

declare(strict_types=1);

namespace App\Services\Menu;

use App\Models\BahanPangan;
use App\Models\KebutuhanBahan;
use App\Models\Menu;
use App\Models\Rekomendasi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MaterialRequirementService
{
    public function calculateForRekomendasi(Rekomendasi $rekomendasi): Collection
    {
        $menu = $rekomendasi->menuTerpilih;

        if ($menu === null) {
            return collect();
        }

        $kebutuhan = $this->aggregate([$menu], $rekomendasi->jumlah_porsi);

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

    public function aggregate(iterable $menus, int $jumlahPorsi): array
    {
        $kebutuhan = [];

        foreach ($menus as $menu) {
            foreach ($this->rowsForMenu($menu, $jumlahPorsi) as $bahanId => $baris) {
                if (!isset($kebutuhan[$bahanId])) {
                    $kebutuhan[$bahanId] = $baris;

                    continue;
                }

                $kebutuhan[$bahanId]['berat_bersih_gram'] += $baris['berat_bersih_gram'];
                $kebutuhan[$bahanId]['berat_kotor_gram'] += $baris['berat_kotor_gram'];
            }
        }

        return $kebutuhan;
    }

    private function rowsForMenu(Menu $menu, int $jumlahPorsi): array
    {
        $baris = [];

        foreach ($menu->bahan as $menuBahan) {
            $bahan = $menuBahan->bahanPangan;

            if ($bahan === null) {
                continue;
            }

            $beratBersih = $menuBahan->berat_bersih_gram * $jumlahPorsi;

            $baris[$bahan->id] = [
                'bahan_pangan_id' => $bahan->id,
                'nama' => $bahan->nama,
                'berat_bersih_gram' => $beratBersih,
                'berat_kotor_gram' => $this->beratKotor($beratBersih, $bahan->bdd_persen),
            ];
        }

        return $baris;
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
