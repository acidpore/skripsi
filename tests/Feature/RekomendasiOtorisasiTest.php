<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\RekomendasiMenu;
use App\Models\AkgSekaliMakan;
use App\Models\Rekomendasi;
use App\Models\Sppg;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class RekomendasiOtorisasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatRekomendasi(Sppg $sppg): Rekomendasi
    {
        $akg = AkgSekaliMakan::create([
            'kelompok_sasaran' => 'Uji', 'waktu_distribusi' => 'Siang', 'persentase_akg' => '30%',
            'energi_kkal_min' => 1, 'energi_kkal_max' => 2, 'protein_g_min' => 1, 'protein_g_max' => 2,
            'lemak_g_min' => 1, 'lemak_g_max' => 2, 'karbohidrat_g_min' => 1, 'karbohidrat_g_max' => 2,
        ]);

        return Rekomendasi::create([
            'sppg_id' => $sppg->id, 'akg_sekali_makan_id' => $akg->id, 'jumlah_porsi' => 10,
            'bobot_entropy' => [], 'hasil_ranking_topsis' => [],
        ]);
    }

    private function buatSppg(string $nama): Sppg
    {
        return Sppg::create(['nama' => $nama, 'provinsi' => 'P', 'kab_kota' => 'K', 'kecamatan' => 'C', 'kelurahan_desa' => 'D', 'alamat' => 'A']);
    }

    public function test_operator_tidak_bisa_membuka_cetak_rekomendasi_sppg_lain(): void
    {
        $milikSendiri = $this->buatSppg('Sendiri');
        $milikLain = $this->buatSppg('Lain');
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR, 'sppg_id' => $milikSendiri->id]);
        $rekomendasiLain = $this->buatRekomendasi($milikLain);

        $this->actingAs($operator)->get(route('rekomendasi.cetak', $rekomendasiLain))->assertForbidden();
    }

    public function test_server_menolak_porsi_di_luar_batas(): void
    {
        $sppg = $this->buatSppg('Sendiri');
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR, 'sppg_id' => $sppg->id]);

        $akgId = $this->buatRekomendasi($sppg)->akg_sekali_makan_id;

        foreach ([0, 100001] as $porsi) {
            Livewire::actingAs($operator)->test(RekomendasiMenu::class)
                ->set('akgId', $akgId)
                ->set('porsi', $porsi)
                ->call('generate')
                ->assertHasErrors(['porsi']);
        }
    }

    public function test_rekomendasi_id_tidak_bisa_diubah_dari_klien(): void
    {
        $milikSendiri = $this->buatSppg('Sendiri');
        $operator = User::factory()->create(['role' => User::ROLE_OPERATOR, 'sppg_id' => $milikSendiri->id]);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($operator)->test(RekomendasiMenu::class)->set('rekomendasiId', 1);
    }
}
