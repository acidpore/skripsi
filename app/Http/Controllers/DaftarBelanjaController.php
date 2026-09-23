<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Rekomendasi;
use App\Services\Menu\MaterialRequirementService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DaftarBelanjaController extends Controller
{
    public function __construct(private readonly MaterialRequirementService $materialRequirementService) {}

    public function cetak(Rekomendasi $rekomendasi): \Illuminate\View\View
    {
        $this->otorisasi($rekomendasi);

        return view('daftar-belanja.cetak', [
            'rekomendasi' => $rekomendasi->load(['sppg', 'akgSekaliMakan']),
            'kebutuhanBahan' => $this->materialRequirementService->calculateForRekomendasi($rekomendasi),
        ]);
    }

    public function csv(Rekomendasi $rekomendasi): StreamedResponse
    {
        $this->otorisasi($rekomendasi);

        $kebutuhanBahan = $this->materialRequirementService->calculateForRekomendasi($rekomendasi);
        $namaBerkas = 'daftar-belanja-rekomendasi-'.$rekomendasi->id.'.csv';

        return response()->streamDownload(function () use ($kebutuhanBahan): void {
            $keluaran = fopen('php://output', 'wb');
            fputcsv($keluaran, ['Bahan', 'Berat Bersih (kg)', 'Berat Kotor (kg)']);

            foreach ($kebutuhanBahan as $kebutuhan) {
                fputcsv($keluaran, [
                    $kebutuhan->bahanPangan->nama,
                    round($kebutuhan->berat_bersih_gram / 1000, 2),
                    round($kebutuhan->beratKotorKilogram(), 2),
                ]);
            }

            fclose($keluaran);
        }, $namaBerkas, ['Content-Type' => 'text/csv']);
    }

    // Operator cuma boleh export rekomendasi milik SPPG-nya sendiri, admin bebas
    private function otorisasi(Rekomendasi $rekomendasi): void
    {
        if (Auth::user()->isOperator() && $rekomendasi->sppg_id !== Auth::user()->sppg_id) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
