<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BahanPangan;
use Illuminate\Console\Command;

class DaftarPenukar extends Command
{
    protected $signature = 'spk:penukar {--sp=1}';

    protected $description = 'Menampilkan bahan penukar yang terpetakan beserta energi per satuan penukar';

    public function handle(): int
    {
        $jumlahSp = (float) $this->option('sp');

        $baris = BahanPangan::query()
            ->whereNotNull('berat_per_sp_gram')
            ->orderBy('kelompok')
            ->orderBy('nama')
            ->get()
            ->map(fn (BahanPangan $bahan): array => [
                $bahan->kelompok,
                $bahan->nama,
                $bahan->berat_per_sp_gram,
                round($bahan->energi_kal * $bahan->berat_per_sp_gram * $jumlahSp / BahanPangan::BASIS_GIZI_GRAM),
                $bahan->bdd_persen,
            ])
            ->all();

        $this->table(['Kelompok', 'Nama TKPI', 'g/SP', 'kkal per '.$jumlahSp.' SP', 'BDD'], $baris);

        return self::SUCCESS;
    }
}
