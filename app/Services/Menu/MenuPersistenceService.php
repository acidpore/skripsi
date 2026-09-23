<?php

declare(strict_types=1);

namespace App\Services\Menu;

use App\Models\Menu;
use App\Models\Rekomendasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class MenuPersistenceService
{
    // Menyimpan satu kandidat dari hasil rekomendasi sebagai menu tetap SPPG, default kandidat peringkat pertama TOPSIS
    public function simpanDariRekomendasi(Rekomendasi $rekomendasi, string $nama, ?array $komposisi = null): Menu
    {
        $komposisi ??= $rekomendasi->komposisi_terpilih['komposisi'] ?? null;

        if ($komposisi === null) {
            throw new RuntimeException('Rekomendasi ini tidak memiliki komposisi kandidat untuk disimpan.');
        }

        return DB::transaction(function () use ($rekomendasi, $nama, $komposisi): Menu {
            $menu = Menu::create([
                'kode' => 'MN-'.Str::upper(Str::random(8)),
                'nama' => $nama,
                'akg_sekali_makan_id' => $rekomendasi->akg_sekali_makan_id,
                'sppg_id' => $rekomendasi->sppg_id,
            ]);

            foreach ($komposisi as $komponen => $bahan) {
                $menu->bahan()->create([
                    'bahan_pangan_id' => $bahan['bahan_pangan_id'],
                    'komponen' => $komponen,
                    'berat_bersih_gram' => $bahan['berat_bersih_gram'],
                ]);
            }

            $rekomendasi->update(['menu_terpilih_id' => $menu->id]);

            return $menu;
        });
    }
}
