<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Rekomendasi;
use App\Models\Sppg;
use App\Services\Menu\MaterialRequirementService;
use App\Services\Menu\MenuPersistenceService;
use App\Services\Spk\RecommendationService;
use App\Services\Spk\RekomendasiFormService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use RuntimeException;

#[Layout('components.layouts.app')]
class RekomendasiMenu extends Component
{
    public string $sppgCari = '';

    public ?int $sppgId = null;

    public ?int $akgId = null;

    public ?int $porsi = null;

    #[Locked]
    public ?int $rekomendasiId = null;

    public ?string $pesanError = null;

    public ?string $pesanSukses = null;

    public array $namaMenuPerPeringkat = [];

    // Operator terkunci ke SPPG miliknya sendiri, hanya admin yang bisa memilih SPPG mana pun
    public bool $sppgTerkunci = false;

    public function mount(): void
    {
        $this->porsi = (int) config('spk.porsi.bawaan');

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
            'porsi' => ['required', 'integer', 'min:'.config('spk.porsi.minimal'), 'max:'.config('spk.porsi.maksimal')],
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
            'porsi.min' => 'Jumlah porsi minimal '.number_format((int) config('spk.porsi.minimal'), 0, ',', '.').'.',
            'porsi.max' => 'Jumlah porsi maksimal '.number_format((int) config('spk.porsi.maksimal'), 0, ',', '.').'.',
        ];
    }

    #[Computed]
    public function daftarSppg(): Collection
    {
        return app(RekomendasiFormService::class)->cariSppg($this->sppgCari);
    }

    #[Computed]
    public function daftarAkg(): Collection
    {
        return app(RekomendasiFormService::class)->daftarAkgDidukung();
    }

    #[Computed]
    public function sppgTerpilih(): ?Sppg
    {
        return app(RekomendasiFormService::class)->sppg($this->sppgId);
    }

    #[Computed]
    public function rekomendasi(): ?Rekomendasi
    {
        return app(RekomendasiFormService::class)->rekomendasiMilik($this->rekomendasiId, Auth::user());
    }

    #[Computed]
    public function kebutuhanBahan(): Collection
    {
        if ($this->rekomendasi === null) {
            return collect();
        }

        return app(MaterialRequirementService::class)->forRekomendasi($this->rekomendasi);
    }

    public function pilihSppg(int $sppgId): void
    {
        $this->sppgId = $sppgId;
        $this->sppgCari = app(RekomendasiFormService::class)->sppg($sppgId)?->nama ?? '';
        unset($this->daftarSppg);
    }

    public function generate(): void
    {
        $this->pesanError = null;
        $this->pesanSukses = null;
        $this->rekomendasiId = null;

        $this->validate();

        try {
            $rekomendasi = app(RecommendationService::class)->generateUntuk(Auth::user(), $this->sppgId, $this->akgId, $this->porsi);
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

        $this->pesanSukses = null;
        $nama = trim($this->namaMenuPerPeringkat[$indeksPeringkat] ?? '');

        if ($nama === '') {
            $this->pesanError = 'Nama menu wajib diisi sebelum disimpan.';

            return;
        }

        try {
            app(MenuPersistenceService::class)->simpanPeringkat($this->rekomendasi, $indeksPeringkat, $nama);
        } catch (RuntimeException $e) {
            $this->pesanError = $e->getMessage();

            return;
        }

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
