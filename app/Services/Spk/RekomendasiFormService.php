<?php

declare(strict_types=1);

namespace App\Services\Spk;

use App\Models\AkgSekaliMakan;
use App\Models\Rekomendasi;
use App\Models\Sppg;
use App\Models\User;
use App\Services\Menu\MenuCandidateGenerator;
use Illuminate\Support\Collection;

class RekomendasiFormService
{
    public function __construct(private readonly MenuCandidateGenerator $candidateGenerator) {}

    public function cariSppg(string $kataKunci): Collection
    {
        if (trim($kataKunci) === '') {
            return collect();
        }

        return Sppg::query()
            ->cariNama($kataKunci)
            ->limit((int) config('spk.pencarian_sppg_maksimal'))
            ->get();
    }

    public function daftarAkgDidukung(): Collection
    {
        return AkgSekaliMakan::query()
            ->berurutan()
            ->get()
            ->filter(fn (AkgSekaliMakan $akg): bool => $this->candidateGenerator->didukung($akg))
            ->values();
    }

    public function sppg(?int $sppgId): ?Sppg
    {
        return $sppgId === null ? null : Sppg::find($sppgId);
    }

    // Rekomendasi milik SPPG lain diperlakukan seolah tidak ada bagi operator
    public function rekomendasiMilik(?int $rekomendasiId, User $pengguna): ?Rekomendasi
    {
        $rekomendasi = $rekomendasiId === null ? null : Rekomendasi::find($rekomendasiId);

        return $rekomendasi !== null && $pengguna->dapatMengaksesSppg($rekomendasi->sppg_id) ? $rekomendasi : null;
    }
}
