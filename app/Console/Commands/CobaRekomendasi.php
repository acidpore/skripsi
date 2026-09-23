<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AkgSekaliMakan;
use App\Models\Sppg;
use App\Services\Menu\MaterialRequirementService;
use App\Services\Menu\MenuPersistenceService;
use App\Services\Spk\RecommendationService;
use Illuminate\Console\Command;

class CobaRekomendasi extends Command
{
    protected $signature = 'spk:coba {kelompok_sasaran} {--porsi=100} {--simpan}';

    protected $description = 'Menjalankan satu siklus rekomendasi untuk satu kelompok sasaran sebagai pemeriksaan cepat';

    public function handle(
        RecommendationService $recommendationService,
        MaterialRequirementService $materialRequirementService,
        MenuPersistenceService $menuPersistenceService
    ): int {
        $akg = AkgSekaliMakan::where('kelompok_sasaran', 'like', $this->argument('kelompok_sasaran').'%')->first();

        if ($akg === null) {
            $this->error('Kelompok sasaran tidak ditemukan.');

            return self::FAILURE;
        }

        $sppg = Sppg::first();

        if ($sppg === null) {
            $this->error('Belum ada data SPPG, jalankan seeder lebih dahulu.');

            return self::FAILURE;
        }

        $rekomendasi = $recommendationService->generate($sppg, $akg, (int) $this->option('porsi'));

        $this->info($akg->label().' | porsi '.$rekomendasi->jumlah_porsi);

        $this->line('');
        $this->line('Bobot Entropy:');
        foreach ($rekomendasi->bobot_entropy as $kriteria => $bobot) {
            $this->line('  '.$kriteria.': '.round($bobot, 4));
        }

        $this->line('');
        $this->line('Lima peringkat teratas TOPSIS:');
        foreach (array_slice($rekomendasi->hasil_ranking_topsis, 0, 5) as $baris) {
            $this->line('  '.$baris['rank'].'. '.$this->ringkasKomposisi($baris['komposisi']).' | skor '.round($baris['score'], 4).' | energi '.round($baris['gizi']['energi_kal']).' kkal');
        }

        $this->line('');
        $this->line('Kebutuhan bahan:');
        foreach ($materialRequirementService->calculateForRekomendasi($rekomendasi) as $kebutuhan) {
            $this->line('  '.$kebutuhan->bahanPangan->nama.': bersih '.round($kebutuhan->berat_bersih_gram / 1000, 2).' kg, kotor '.round($kebutuhan->beratKotorKilogram(), 2).' kg');
        }

        if ($this->option('simpan')) {
            $menu = $menuPersistenceService->simpanDariRekomendasi($rekomendasi, $akg->label().' - '.now()->format('Y-m-d H:i'));
            $this->line('');
            $this->info('Disimpan sebagai menu tetap: '.$menu->kode.' ('.$menu->bahan()->count().' bahan)');
        }

        return self::SUCCESS;
    }

    private function ringkasKomposisi(array $komposisi): string
    {
        return implode(' + ', array_column($komposisi, 'nama'));
    }
}
