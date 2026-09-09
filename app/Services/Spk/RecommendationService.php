<?php

declare(strict_types=1);

namespace App\Services\Spk;

use App\Models\AkgSekaliMakan;
use App\Models\Menu;
use App\Models\Rekomendasi;
use App\Models\Sppg;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecommendationService
{
    private const MINIMAL_ALTERNATIF = 2;

    public function __construct(
        private readonly DecisionMatrixBuilder $matrixBuilder,
        private readonly EntropyWeighting $entropyWeighting,
        private readonly TopsisRanking $topsisRanking,
        private readonly SawRanking $sawRanking,
        private readonly SpearmanCorrelation $spearmanCorrelation,
    ) {}

    public function generate(Sppg $sppg, AkgSekaliMakan $akg, int $jumlahPorsi): Rekomendasi
    {
        $menus = $this->fetchAlternatives($sppg, $akg);

        if ($menus->count() < self::MINIMAL_ALTERNATIF) {
            throw new RuntimeException('Alternatif menu untuk kelompok sasaran ini kurang dari dua, perankingan tidak dapat dijalankan.');
        }

        $hasilMatriks = $this->matrixBuilder->build($menus, $akg, config('spk.kriteria'));
        $bobot = $this->entropyWeighting->calculate($hasilMatriks['matriks']);

        $rankingTopsis = $this->topsisRanking->rank($hasilMatriks['matriks'], $bobot, $hasilMatriks['tipe_kriteria']);
        $rankingSaw = $this->sawRanking->rank($hasilMatriks['matriks'], $bobot, $hasilMatriks['tipe_kriteria']);
        $spearman = $this->spearmanCorrelation->calculate($rankingTopsis, $rankingSaw);

        return DB::transaction(fn (): Rekomendasi => Rekomendasi::create([
            'sppg_id' => $sppg->id,
            'akg_sekali_makan_id' => $akg->id,
            'menu_terpilih_id' => $this->resolveMenuTerpilih($menus, $rankingTopsis),
            'jumlah_porsi' => $jumlahPorsi,
            'bobot_entropy' => $bobot,
            'hasil_ranking_topsis' => $this->annotate($rankingTopsis, $menus, $hasilMatriks),
            'hasil_ranking_saw' => $this->annotate($rankingSaw, $menus, $hasilMatriks),
            'koefisien_spearman' => $spearman,
        ]));
    }

    private function fetchAlternatives(Sppg $sppg, AkgSekaliMakan $akg): Collection
    {
        $query = Menu::query()
            ->with('bahan.bahanPangan')
            ->where('akg_sekali_makan_id', $akg->id)
            ->tersediaUntukSppg($sppg->id)
            ->orderBy('kode');

        $batas = config('spk.batas_alternatif');

        return $batas === null ? $query->get() : $query->limit((int) $batas)->get();
    }

    private function resolveMenuTerpilih(Collection $menus, array $ranking): ?int
    {
        $teratas = $ranking[0] ?? null;

        return $teratas === null ? null : $menus->values()[$teratas['index']]->id ?? null;
    }

    private function annotate(array $ranking, Collection $menus, array $hasilMatriks): array
    {
        return array_map(static function (array $entri) use ($menus, $hasilMatriks): array {
            $menu = $menus->values()[$entri['index']] ?? null;

            return $entri + [
                'menu_id' => $menu?->id,
                'kode' => $menu?->kode,
                'nama' => $menu?->nama,
                'gizi' => $hasilMatriks['gizi'][$entri['index']] ?? [],
                'skor_kriteria' => $hasilMatriks['matriks'][$entri['index']] ?? [],
            ];
        }, $ranking);
    }
}
