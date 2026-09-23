<?php

declare(strict_types=1);

namespace App\Services\Spk;

use App\Models\AkgSekaliMakan;
use App\Models\Rekomendasi;
use App\Models\Sppg;
use App\Services\Menu\MenuCandidateGenerator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecommendationService
{
    private const MINIMAL_ALTERNATIF = 2;

    public function __construct(
        private readonly MenuCandidateGenerator $candidateGenerator,
        private readonly DecisionMatrixBuilder $matrixBuilder,
        private readonly EntropyWeighting $entropyWeighting,
        private readonly TopsisRanking $topsisRanking,
    ) {}

    public function generate(Sppg $sppg, AkgSekaliMakan $akg, int $jumlahPorsi, array $bahanTersediaPerKomponen = []): Rekomendasi
    {
        $seed = (int) crc32(sprintf('%d-%d-%d', $sppg->id, $akg->id, $jumlahPorsi));
        $kandidat = $this->candidateGenerator->generate($akg, $bahanTersediaPerKomponen, $seed);

        if (count($kandidat) < self::MINIMAL_ALTERNATIF) {
            throw new RuntimeException('Kandidat menu yang memenuhi rentang energi AKG kurang dari dua, perankingan tidak dapat dijalankan. Tambah pilihan bahan tersedia atau periksa kembali stok yang diinput.');
        }

        $hasilMatriks = $this->matrixBuilder->build(
            array_column($kandidat, 'gizi'),
            $akg,
            config('spk.kriteria')
        );

        $bobot = $this->entropyWeighting->calculate($hasilMatriks['matriks']);
        $rankingTopsis = $this->topsisRanking->rank($hasilMatriks['matriks'], $bobot, $hasilMatriks['tipe_kriteria']);

        return DB::transaction(fn (): Rekomendasi => Rekomendasi::create([
            'sppg_id' => $sppg->id,
            'akg_sekali_makan_id' => $akg->id,
            'komposisi_terpilih' => $this->kandidatTeratas($kandidat, $rankingTopsis),
            'jumlah_porsi' => $jumlahPorsi,
            'bobot_entropy' => $bobot,
            'hasil_ranking_topsis' => $this->annotate($rankingTopsis, $kandidat, $hasilMatriks['matriks']),
        ]));
    }

    // Snapshot lengkap disimpan, bukan rujukan ke data induk, agar hasil rekomendasi lama tetap dapat ditelusuri meski TKPI di-seed ulang
    private function kandidatTeratas(array $kandidat, array $ranking): ?array
    {
        $teratas = $ranking[0] ?? null;

        return $teratas === null ? null : ($kandidat[$teratas['index']] ?? null);
    }

    private function annotate(array $ranking, array $kandidat, array $matriks): array
    {
        return array_map(static function (array $entri) use ($kandidat, $matriks): array {
            $baris = $kandidat[$entri['index']] ?? [];

            return $entri + [
                'komposisi' => $baris['komposisi'] ?? [],
                'gizi' => $baris['gizi'] ?? [],
                'skor_kriteria' => $matriks[$entri['index']] ?? [],
            ];
        }, $ranking);
    }
}
