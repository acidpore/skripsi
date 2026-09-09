<?php

declare(strict_types=1);

namespace App\Services\Menu;

use App\Models\BahanPangan;
use App\Models\Menu;

class MenuNutritionCalculator
{
    public const ZAT_GIZI = [
        'energi_kal',
        'protein_g',
        'lemak_g',
        'karbohidrat_g',
        'kalsium_mg',
        'besi_mg',
        'vitamin_c_mg',
    ];

    public function calculate(Menu $menu): array
    {
        $total = array_fill_keys(self::ZAT_GIZI, 0.0);

        foreach ($menu->bahan as $menuBahan) {
            $bahan = $menuBahan->bahanPangan;

            if ($bahan === null) {
                continue;
            }

            $faktor = $menuBahan->berat_bersih_gram / BahanPangan::BASIS_GIZI_GRAM;

            foreach (self::ZAT_GIZI as $zatGizi) {
                $total[$zatGizi] += ((float) ($bahan->{$zatGizi} ?? 0.0)) * $faktor;
            }
        }

        return $total;
    }
}
