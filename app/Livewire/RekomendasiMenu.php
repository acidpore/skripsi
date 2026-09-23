<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\AkgSekaliMakan;
use App\Models\Rekomendasi;
use App\Models\Sppg;
use App\Services\Menu\MaterialRequirementService;
use App\Services\Menu\MenuCandidateGenerator;
use App\Services\Menu\MenuPersistenceService;
use App\Services\Spk\RecommendationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

#[Layout('components.layouts.app')]
class RekomendasiMenu extends Component
{
    public string $sppgCari = '';

    public ?int $sppgId = null;

    public ?int $akgId = null;

    public int $porsi = 100;

    public ?int $rekomendasiId = null;

    public ?string $pesanError = null;

    public ?string $pesanSukses = null;

    public array $namaMenuPerPeringkat = [];

    // Operator terkunci ke SPPG miliknya sendiri, hanya admin yang bisa memilih SPPG mana pun
    public bool $sppgTerkunci = false;

    public function mount(): void
    {
        if (Auth::user()->isOperator()) {
            $this->sppgId = Auth::user()->sppg_id;
            $this->sppgCari = Auth::user()->sppg?->nama ?? '';
            $this->sppgTerkunci = true;
        }
    }

    public function rules(): array
    {
        return [
            'sppgId' => ['required', 'exists:sppg,id'],
            'akgId' => ['required', 'exists:akg_sekali_makan,id'],
            'porsi' => ['required', 'integer', 'min:1', 'max:100000'],
        ];
    }

    public function messages(): array
    {
        return [
            'sppgId.required' => 'Satuan Pelayanan (SPPG) wajib dipilih terlebih dahulu.',
            'sppgId.exists' => 'SPPG yang dipilih tidak ditemukan. Ketik ulang lalu pilih dari daftar.',
            'akgId.required' => 'Kelompok sasaran dan waktu makan wajib dipilih.',
            'akgId.exists' => 'Kelompok sasaran yang dipilih tidak sah.',
            'porsi.required' => 'Jumlah porsi wajib diisi.',
            'porsi.integer' => 'Jumlah porsi harus berupa bilangan bulat.',
            'porsi.min' => 'Jumlah porsi minimal 1.',
            'porsi.max' => 'Jumlah porsi maksimal 100.000.',
        ];
    }

    #[Computed]
    public function daftarSppg(): Collection
    {
        if (trim($this->sppgCari) === '') {
            return collect();
        }

        return Sppg::query()
            ->where('nama', 'like', '%'.$this->sppgCari.'%')
            ->orderBy('nama')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function daftarAkg(): Collection
    {
        $generator = app(MenuCandidateGenerator::class);

        return AkgSekaliMakan::query()
            ->orderBy('kelompok_sasaran')
            ->orderBy('waktu_distribusi')
            ->get()
            ->filter(fn (AkgSekaliMakan $akg): bool => $generator->didukung($akg));
    }

    #[Computed]
    public function sppgTerpilih(): ?Sppg
    {
        return $this->sppgId === null ? null : Sppg::find($this->sppgId);
    }

    #[Computed]
    public function rekomendasi(): ?Rekomendasi
    {
        return $this->rekomendasiId === null ? null : Rekomendasi::find($this->rekomendasiId);
    }

    #[Computed]
    public function kebutuhanBahan(): Collection
    {
        if ($this->rekomendasi === null) {
            return collect();
        }

        return app(MaterialRequirementService::class)->calculateForRekomendasi($this->rekomendasi);
    }

    public function pilihSppg(int $sppgId): void
    {
        $this->sppgId = $sppgId;
        $this->sppgCari = Sppg::find($sppgId)?->nama ?? '';
        unset($this->daftarSppg);
    }

    public function generate(): void
    {
        $this->pesanError = null;
        $this->pesanSukses = null;
        $this->rekomendasiId = null;

        $this->validate();

        if (Auth::user()->isOperator() && $this->sppgId !== Auth::user()->sppg_id) {
            $this->pesanError = 'Operator hanya dapat membuat rekomendasi untuk SPPG miliknya sendiri.';

            return;
        }

        $sppg = Sppg::findOrFail($this->sppgId);
        $akg = AkgSekaliMakan::findOrFail($this->akgId);

        try {
            $rekomendasi = app(RecommendationService::class)->generate($sppg, $akg, $this->porsi);
        } catch (RuntimeException $e) {
            $this->pesanError = $e->getMessage();

            return;
        }

        $this->rekomendasiId = $rekomendasi->id;
        unset($this->kebutuhanBahan);
    }

    public function simpan(int $indeksPeringkat): void
    {
        if ($this->rekomendasi === null) {
            return;
        }

        $nama = trim($this->namaMenuPerPeringkat[$indeksPeringkat] ?? '');

        if ($nama === '') {
            $this->pesanError = 'Nama menu wajib diisi sebelum disimpan.';

            return;
        }

        $baris = $this->rekomendasi->hasil_ranking_topsis[$indeksPeringkat] ?? null;

        if ($baris === null) {
            return;
        }

        app(MenuPersistenceService::class)->simpanDariRekomendasi($this->rekomendasi, $nama, $baris['komposisi']);

        // Bahan yang perlu dibeli mengikuti menu yang benar-benar dipilih operator, bukan selalu peringkat 1
        $this->rekomendasi->update([
            'komposisi_terpilih' => ['komposisi' => $baris['komposisi'], 'gizi' => $baris['gizi']],
        ]);

        $this->pesanError = null;
        $this->pesanSukses = 'Menu "'.$nama.'" berhasil disimpan.';
        unset($this->rekomendasi);
        unset($this->kebutuhanBahan);
    }

    public function render()
    {
        return view('livewire.rekomendasi-menu');
    }
}
