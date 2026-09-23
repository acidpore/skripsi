<x-layouts.app>
    <div class="grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <p class="eyebrow">Sistem pendukung keputusan</p>
            <h1 class="font-display mt-3 text-4xl font-medium leading-[1.05] tracking-tight sm:text-6xl">Menu MBG yang <em class="font-normal italic text-leaf">terukur</em>, bukan dikira-kira.</h1>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-ink-2">Alat bantu operator SPPG untuk menyusun menu Makan Bergizi Gratis dari basis pangan TKPI, mengujinya terhadap AKG sekali makan, dan menghitung belanja bahan mentah lengkap dengan bagian yang terbuang.</p>
            <div class="mt-8 flex flex-wrap items-center gap-4">
                <a href="{{ auth()->check() ? route('rekomendasi.index') : route('login') }}" class="btn btn-ink px-6 py-3">Mulai menyusun menu</a>
                @auth
                    <a href="{{ route('dokumentasi.metode') }}" class="text-sm font-medium underline decoration-line-strong underline-offset-4 hover:decoration-ink">Lihat cara hitungnya</a>
                @endauth
            </div>
        </div>
        <dl class="rule-strong self-start lg:col-span-5">
            <div class="grid grid-cols-[3rem_1fr] gap-x-3 py-5">
                <dt class="flex h-9 w-9 items-center justify-center border border-line-strong bg-white text-leaf"><svg class="ico" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h10"/></svg></dt>
                <dd><span class="block font-medium">Kandidat dari data, bukan daftar tetap</span><span class="mt-1 block text-sm text-ink-2">Satu bahan per komponen, gramasi dari satuan penukar Juknis BGN.</span></dd>
            </div>
            <div class="grid grid-cols-[3rem_1fr] gap-x-3 py-5 rule">
                <dt class="flex h-9 w-9 items-center justify-center border border-line-strong bg-white text-leaf"><svg class="ico" viewBox="0 0 24 24"><path d="M12 3v3M6 8l6-2 6 2M6 8l-3 7h6zM18 8l-3 7h6zM12 6v15M8 21h8"/></svg></dt>
                <dd><span class="block font-medium">Bobot objektif, ranking TOPSIS</span><span class="mt-1 block text-sm text-ink-2">Entropy menimbang kriteria. TOPSIS mengurutkan kandidat menu berdasarkan bobot itu.</span></dd>
            </div>
            <div class="grid grid-cols-[3rem_1fr] gap-x-3 py-5 rule">
                <dt class="flex h-9 w-9 items-center justify-center border border-line-strong bg-white text-leaf"><svg class="ico" viewBox="0 0 24 24"><path d="M3 4h2l2.5 12h11L21 7H6.5M9 20h.01M17 20h.01"/></svg></dt>
                <dd><span class="block font-medium">Belanja dengan berat kotor</span><span class="mt-1 block text-sm text-ink-2">Berat bersih dibagi BDD, dikali porsi. Siap cetak atau unduh CSV.</span></dd>
            </div>
        </dl>
    </div>
</x-layouts.app>
