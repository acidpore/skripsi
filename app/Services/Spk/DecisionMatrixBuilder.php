<?php

declare(strict_types=1);

namespace App\Services\Spk;

use App\Models\AkgSekaliMakan;
use App\Services\Menu\MenuNutritionCalculator;
use Illuminate\Support\Collection;

class DecisionMatrixBuilder
{
    public function __construct(
        private readonly MenuNutritionCalculator $nutritionCalculator,
        private readonly SuitabilityScorer $suitabilityScorer,
    ) {}

    public function build(Collection $menus, AkgSekaliMakan $akg, array $definisiKriteria): array
    {
        $matriks = [];
        $gizi = [];

        foreach ($menus->values() as $posisi => $menu) {
            $kandunganGizi = $this->nutritionCalculator->calculate($menu);
            $gizi[$posisi] = $kandunganGizi;
            $matriks[$posisi] = $this->scoreRow($kandunganGizi, $akg, $definisiKriteria);
        }

        return [
            'matriks' => $matriks,
            'gizi' => $gizi,
            'tipe_kriteria' => $this->criteriaTypes($definisiKriteria),
        ];
    }

    private function scoreRow(array $kandunganGizi, AkgSekaliMakan $akg, array $definisiKriteria): array
    {
        $baris = [];

        foreach ($definisiKriteria as $kunci => $kriteria) {
            $baris[$kunci] = $this->suitabilityScorer->score(
                (float) ($kandunganGizi[$kriteria['kolom_gizi']] ?? 0.0),
                (float) $akg->{$kriteria['kolom_min']},
                (float) $akg->{$kriteria['kolom_max']},
            );
        }

        return $baris;
    }

    private function criteriaTypes(array $definisiKriteria): array
    {
        return array_map(static fn (array $kriteria): string => $kriteria['tipe'], $definisiKriteria);
    }
}
