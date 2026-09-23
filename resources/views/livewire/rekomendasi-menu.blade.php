<div class="space-y-12">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="eyebrow">Lembar kerja</p>
            <h1 class="font-display mt-2 text-3xl font-medium tracking-tight sm:text-4xl">Rekomendasi menu</h1>
        </div>
        <p class="max-w-md text-sm leading-relaxed text-ink-2">Tiga isian di bawah cukup. Sistem menyusun kandidat menu dari basis TKPI, menyaring yang energinya masuk rentang AKG, lalu mengurutkannya.</p>
    </div>

    @if ($pesanError)
        <div class="flex gap-4 border-l-2 border-ember bg-ember-light px-4 py-3 text-sm">
            <span class="eyebrow mt-0.5 !text-ember">Galat</span>
            <span class="text-ink">{{ $pesanError }}</span>
        </div>
    @endif

    @if ($pesanSukses)
        <div class="flex gap-4 border-l-2 border-leaf bg-leaf-light px-4 py-3 text-sm">
            <span class="eyebrow mt-0.5 !text-leaf">Tersimpan</span>
            <span class="text-ink">{{ $pesanSukses }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-12 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
        <form wire:submit="generate" class="rule-strong lg:sticky lg:top-8 lg:self-start">
            <div class="grid grid-cols-[3rem_1fr] gap-x-3 py-6 rule first:border-t-0">
                <span class="flex h-9 w-9 items-center justify-center border border-line-strong bg-white text-leaf"><svg class="ico" viewBox="0 0 24 24"><path d="M3 21h18M5 21V8l7-4 7 4v13M9 21v-6h6v6M12 11h.01"/></svg></span>
                <div>
                    <label class="block font-medium">Satuan pelayanan</label>
                    <p class="mb-3 text-sm text-ink-2">SPPG yang akan memasak menu ini.</p>
                    @if ($sppgTerkunci)
                        <p class="field bg-paper-2 text-ink-2">{{ $sppgCari }}</p>
                    @else
                        <input type="text" wire:model.live.debounce.400ms="sppgCari" placeholder="Ketik nama SPPG" class="field">
                        @if ($this->sppgTerpilih)
                            <p class="mt-2 text-sm text-leaf">Terpilih: {{ $this->sppgTerpilih->nama }}</p>
                        @endif
                        @if ($this->daftarSppg->isNotEmpty())
                            <ul class="mt-2 divide-y divide-line border border-line-strong bg-white text-sm">
                                @foreach ($this->daftarSppg as $sppg)
                                    <li wire:click="pilihSppg({{ $sppg->id }})" class="cursor-pointer px-3 py-2 hover:bg-leaf-light">
                                        <span class="font-medium">{{ $sppg->nama }}</span>
                                        <span class="block text-ink-3">{{ $sppg->kab_kota }}, {{ $sppg->provinsi }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                    @error('sppgId') <p class="mt-2 text-sm text-ember">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-[3rem_1fr] gap-x-3 py-6 rule">
                <span class="flex h-9 w-9 items-center justify-center border border-line-strong bg-white text-leaf"><svg class="ico" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></svg></span>
                <div>
                    <label for="akgId" class="block font-medium">Kelompok sasaran dan waktu makan</label>
                    <p class="mb-3 text-sm text-ink-2">Menentukan rentang AKG sekali makan yang dipakai.</p>
                    <select id="akgId" wire:model="akgId" class="field">
                        <option value="">Pilih kelompok sasaran</option>
                        @foreach ($this->daftarAkg as $akg)
                            <option value="{{ $akg->id }}">{{ $akg->label() }}</option>
                        @endforeach
                    </select>
                    @error('akgId') <p class="mt-2 text-sm text-ember">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-[3rem_1fr] gap-x-3 py-6 rule">
                <span class="flex h-9 w-9 items-center justify-center border border-line-strong bg-white text-leaf"><svg class="ico" viewBox="0 0 24 24"><path d="M3 10h18M3 10a9 9 0 0 0 18 0M12 10V4M9 4h6"/></svg></span>
                <div>
                    <label for="porsi" class="block font-medium">Jumlah porsi</label>
                    <p class="mb-3 text-sm text-ink-2">Dipakai untuk menghitung berat belanja.</p>
                    <div class="flex items-center gap-3">
                        <input id="porsi" type="number" wire:model="porsi" min="1" class="field num max-w-[9rem]">
                        <span class="text-sm text-ink-3">porsi</span>
                    </div>
                    @error('porsi') <p class="mt-2 text-sm text-ember">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="rule pt-6">
                <button type="submit" class="btn btn-ink w-full py-3" wire:loading.attr="disabled" wire:target="generate">
                    <span wire:loading.remove wire:target="generate" class="flex items-center gap-2"><svg class="ico" viewBox="0 0 24 24"><path d="M4 20V4M4 16l5-5 4 4 7-8M20 7v4h-4"/></svg>Susun rekomendasi</span>
                    <span wire:loading wire:target="generate" class="sweep w-full">Menghitung kandidat menu</span>
                </button>
            </div>
        </form>

        <div>
            @if ($this->rekomendasi)
                @php
                    $akg = $this->rekomendasi->akgSekaliMakan;
                    $daftarZat = [
                        'energi_kal' => ['Energi', 'kkal', $akg->energi_kkal_min, $akg->energi_kkal_max],
                        'protein_g' => ['Protein', 'g', $akg->protein_g_min, $akg->protein_g_max],
                        'lemak_g' => ['Lemak', 'g', $akg->lemak_g_min, $akg->lemak_g_max],
                        'karbohidrat_g' => ['Karbohidrat', 'g', $akg->karbohidrat_g_min, $akg->karbohidrat_g_max],
                    ];
                    $labelBobot = ['kesesuaian_protein' => 'Protein', 'kesesuaian_lemak' => 'Lemak', 'kesesuaian_karbohidrat' => 'Karbohidrat'];
                @endphp

                <div class="rule-strong pt-5">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <h2 class="font-display text-2xl font-medium tracking-tight">Lima kandidat teratas</h2>
                        <span class="eyebrow">{{ $akg->label() }} &middot; {{ $this->rekomendasi->jumlah_porsi }} porsi</span>
                    </div>
                    <p class="mt-1 text-sm text-ink-2">Urutan menurut TOPSIS. Beri nama lalu simpan menu yang akan dipakai.</p>
                </div>

                <div class="mt-4 space-y-4">
                    @foreach (array_slice($this->rekomendasi->hasil_ranking_topsis, 0, 5) as $indeks => $baris)
                        <article class="border border-line bg-white {{ $baris['rank'] === 1 ? 'border-l-4 border-l-leaf' : '' }}">
                            <div class="flex items-start gap-4 px-5 pt-5">
                                <span class="num text-2xl leading-none {{ $baris['rank'] === 1 ? 'text-leaf' : 'text-ink-3' }}">{{ str_pad((string) $baris['rank'], 2, '0', STR_PAD_LEFT) }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-display text-lg leading-snug">{{ implode(', ', array_column($baris['komposisi'], 'nama')) }}</p>
                                    <p class="mt-1 text-sm text-ink-3">Skor kedekatan <span class="num text-ink">{{ number_format($baris['score'], 3) }}</span></p>
                                </div>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-px border-y border-line bg-line sm:grid-cols-4">
                                @foreach ($daftarZat as $kunci => [$label, $satuan, $min, $max])
                                    @php $nilai = $baris['gizi'][$kunci] ?? 0; $pas = $nilai >= $min && $nilai <= $max; @endphp
                                    <div class="bg-white px-5 py-3">
                                        <dt class="eyebrow">{{ $label }}</dt>
                                        <dd class="num mt-1 text-xl {{ $pas ? 'text-ink' : 'text-ember' }}">{{ number_format($nilai, 1) }} <span class="text-sm text-ink-3">{{ $satuan }}</span></dd>
                                        <dd class="num mt-0.5 flex items-center gap-1 text-sm text-ink-3">
                                            @if ($pas)<svg class="ico h-3.5 w-3.5 text-leaf" viewBox="0 0 24 24"><path d="M5 12l5 5 9-10"/></svg>@else<svg class="ico h-3.5 w-3.5 text-ember" viewBox="0 0 24 24"><path d="M12 8v5M12 16h.01M3 20h18L12 4z"/></svg>@endif
                                            {{ number_format($min, 0) }}&ndash;{{ number_format($max, 0) }}
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>

                            <details class="group">
                                <summary class="flex cursor-pointer items-center gap-2 px-5 py-2.5 text-sm text-ink-2 hover:text-ink">
                                    <span class="chev inline-block transition">&rsaquo;</span>
                                    Gramasi per porsi
                                </summary>
                                <table class="w-full border-t border-line text-sm">
                                    <tbody class="divide-y divide-line">
                                        @foreach ($baris['komposisi'] as $komponen => $bahan)
                                            <tr>
                                                <td class="eyebrow px-5 py-2">{{ str_replace('_', ' ', $komponen) }}</td>
                                                <td class="px-2 py-2">{{ $bahan['nama'] }}</td>
                                                <td class="num px-5 py-2 text-right">{{ number_format($bahan['berat_bersih_gram'], 0) }} g</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </details>

                            <div class="flex flex-col gap-2 border-t border-line bg-paper px-5 py-3 sm:flex-row">
                                <input type="text" wire:model="namaMenuPerPeringkat.{{ $indeks }}" placeholder="Nama menu, misal Nasi Ayam Bayam" class="field flex-1 text-sm">
                                <button type="button" wire:click="simpan({{ $indeks }})" class="btn btn-ink"><svg class="ico" viewBox="0 0 24 24"><path d="M5 3h11l3 3v15H5zM8 3v5h7V3M8 21v-7h8v7"/></svg>Simpan menu ini</button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <details class="rule mt-8 pt-4">
                    <summary class="flex cursor-pointer items-center gap-2 text-sm font-medium hover:text-leaf">
                        <span class="chev inline-block transition">&rsaquo;</span>
                        Mengapa urutannya begini
                    </summary>
                    <div class="mt-4 grid gap-6 text-sm text-ink-2 sm:grid-cols-2">
                        <div>
                            <p class="eyebrow mb-3">Bobot Entropy</p>
                            <table class="w-full">
                                <tbody class="divide-y divide-line">
                                    @foreach ($this->rekomendasi->bobot_entropy as $kriteria => $bobot)
                                        <tr>
                                            <td class="py-1.5 text-ink">{{ $labelBobot[$kriteria] ?? $kriteria }}</td>
                                            <td class="w-1/2 py-1.5"><div class="h-1.5 bg-leaf" style="width: {{ round($bobot * 100) }}%"></div></td>
                                            <td class="num py-1.5 pl-3 text-right">{{ number_format($bobot, 3) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="space-y-3">
                            <p>Bobot lebih besar berarti zat gizi itu paling membedakan antar kandidat. Kesesuaian energi tidak dibobot karena sudah dipakai sebagai syarat lolos saat kandidat dibangkitkan.</p>
                            <a href="{{ route('dokumentasi.metode') }}" class="inline-block font-medium text-leaf underline decoration-leaf/40 underline-offset-4 hover:decoration-leaf">Baca rumus lengkapnya</a>
                        </div>
                    </div>
                </details>
            @else
                <div class="dotted-bg flex min-h-[320px] flex-col justify-end border border-line p-6">
                    <svg class="ico mb-4 h-10 w-10 text-line-strong" viewBox="0 0 24 24"><path d="M4 11h16M5 11a7 7 0 0 0 14 0M9 3.5v3M12 3v3.5M15 3.5v3M8 21h8"/></svg>
                    <p class="eyebrow">Hasil</p>
                    <p class="mt-2 max-w-xs text-sm leading-relaxed text-ink-2">Kandidat menu, nilai gizinya terhadap rentang AKG, dan daftar belanja akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>

    @if ($this->rekomendasi)
        <section class="rule-strong pt-5">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="font-display text-2xl font-medium tracking-tight">Daftar belanja</h2>
                    <p class="mt-1 max-w-xl text-sm text-ink-2">Mengikuti menu yang terakhir disimpan. Sebelum ada yang disimpan, dipakai kandidat peringkat 1. Berat kotor sudah menghitung bagian yang terbuang (BDD).</p>
                </div>
                @if ($this->kebutuhanBahan->isNotEmpty())
                    <div class="flex gap-2">
                        <a href="{{ route('rekomendasi.csv', $this->rekomendasi) }}" class="btn btn-line"><svg class="ico" viewBox="0 0 24 24"><path d="M12 4v11M8 11l4 4 4-4M4 19h16"/></svg>Unduh CSV</a>
                        <a href="{{ route('rekomendasi.cetak', $this->rekomendasi) }}" target="_blank" class="btn btn-line"><svg class="ico" viewBox="0 0 24 24"><path d="M7 8V3h10v5M5 8h14a2 2 0 0 1 2 2v6h-4v-3H7v3H3v-6a2 2 0 0 1 2-2zM7 14h10v7H7z"/></svg>Cetak</a>
                    </div>
                @endif
            </div>

            @if ($this->kebutuhanBahan->isEmpty())
                <p class="mt-4 text-sm text-ink-3">Belum ada data.</p>
            @else
                <div class="mt-5 overflow-x-auto">
                    <table class="w-full min-w-[480px] text-sm">
                        <thead>
                            <tr class="border-b border-ink text-left">
                                <th class="eyebrow py-2 pr-4 font-normal">Bahan</th>
                                <th class="eyebrow py-2 pr-4 text-right font-normal">Berat bersih</th>
                                <th class="eyebrow py-2 text-right font-normal">Berat kotor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($this->kebutuhanBahan as $kebutuhan)
                                <tr>
                                    <td class="py-3 pr-4">{{ $kebutuhan->bahanPangan->nama }}</td>
                                    <td class="num py-3 pr-4 text-right text-ink-2">{{ number_format($kebutuhan->berat_bersih_gram / 1000, 2) }} kg</td>
                                    <td class="num py-3 text-right text-base">{{ number_format($kebutuhan->beratKotorKilogram(), 2) }} kg</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
</div>
