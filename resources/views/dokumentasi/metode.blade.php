<x-layouts.app title="Cara hitung">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.11/katex.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.11/katex.min.js"></script>
    <style>
        .katex { font-size: 1.05em; }
        @media (max-width: 480px) { .katex { font-size: 0.85em; } }
        .math-block { max-width: 100%; overflow-x: auto; }
        .math-block .katex-display { margin: 0; }
        .table-scroll { max-width: 100%; overflow-x: auto; }
    </style>

    <div class="grid min-w-0 gap-8 lg:grid-cols-12 lg:gap-10">
        <aside class="min-w-0 lg:col-span-3">
            <div class="lg:sticky lg:top-8">
                <p class="eyebrow">Isi</p>
                <ol class="mt-3 flex min-w-0 max-w-full gap-x-4 gap-y-2 overflow-x-auto whitespace-nowrap text-sm sm:flex-wrap lg:flex-col lg:whitespace-normal lg:overflow-visible">
                    <li><a href="#skor" class="text-ink-2 hover:text-ink"><span class="num mr-2 text-ink-3">01</span>Skor kesesuaian</a></li>
                    <li><a href="#entropy" class="text-ink-2 hover:text-ink"><span class="num mr-2 text-ink-3">02</span>Bobot Entropy</a></li>
                    <li><a href="#topsis" class="text-ink-2 hover:text-ink"><span class="num mr-2 text-ink-3">03</span>TOPSIS</a></li>
                    <li><a href="#bdd" class="text-ink-2 hover:text-ink"><span class="num mr-2 text-ink-3">04</span>Berat kotor</a></li>
                </ol>
            </div>
        </aside>

        <div class="min-w-0 space-y-12 lg:col-span-9 lg:max-w-2xl">
            <div>
                <p class="eyebrow">Dokumentasi</p>
                <h1 class="font-display mt-2 text-3xl font-medium tracking-tight sm:text-4xl">Cara sistem menghitung menu</h1>
                <p class="mt-4 leading-relaxed text-ink-2">Dari nilai gizi mentah sampai urutan peringkat. Tidak perlu latar statistik untuk mengikutinya. Alurnya: bangkitkan kandidat, beri skor kesesuaian gizi, timbang dengan Entropy, lalu urutkan dengan TOPSIS.</p>
            </div>

            <section id="skor" class="rule-strong pt-5">
                <p class="num text-sm text-ink-3">01</p>
                <h2 class="font-display mt-1 text-2xl font-medium tracking-tight">Skor kesesuaian gizi</h2>
                <p class="mt-3 leading-relaxed text-ink-2">Tiap kandidat dibandingkan dengan rentang AKG sekali makan kelompok sasaran. Untuk protein, lemak, dan karbohidrat, nilai gizi <span class="math-inline" data-math="x"></span> diubah menjadi skor 0 sampai 1, dengan <span class="math-inline" data-math="L"></span> batas bawah dan <span class="math-inline" data-math="U"></span> batas atas.</p>
                <div class="mt-4 border border-line bg-paper-2 px-3.5 py-4 sm:px-5 sm:py-5">
                    <div class="math-block" data-math="\text{skor} = \begin{cases} 1 & L \le x \le U \\[4pt] \dfrac{x}{L} & x < L \\[6pt] \dfrac{U}{x} & x > U \end{cases}"></div>
                </div>
                <div class="table-scroll mt-4">
                    <table class="w-full min-w-[360px] text-sm">
                        <thead><tr class="border-b border-ink text-left"><th class="eyebrow py-2 font-normal">Kondisi</th><th class="eyebrow py-2 font-normal">Artinya</th></tr></thead>
                        <tbody class="divide-y divide-line">
                            <tr><td class="py-2.5"><span class="math-inline" data-math="L \le x \le U"></span></td><td class="py-2.5 text-ink-2">Pas di dalam rentang, skor 1</td></tr>
                            <tr><td class="py-2.5"><span class="math-inline" data-math="x < L"></span></td><td class="py-2.5 text-ink-2">Kurang, makin jauh makin rendah</td></tr>
                            <tr><td class="py-2.5"><span class="math-inline" data-math="x > U"></span></td><td class="py-2.5 text-ink-2">Lebih, makin jauh makin rendah</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-ink-2">Skor tidak pernah negatif dan menghukum kekurangan maupun kelebihan secara setara. Kesesuaian energi dihitung dengan rumus yang sama tetapi dipakai sebagai syarat lolos saat kandidat dibangkitkan, bukan sebagai kriteria di tabel keputusan.</p>
            </section>

            <section id="entropy" class="rule-strong pt-5">
                <p class="num text-sm text-ink-3">02</p>
                <h2 class="font-display mt-1 text-2xl font-medium tracking-tight">Bobot Entropy</h2>
                <p class="mt-3 leading-relaxed text-ink-2">Entropy mengukur seberapa besar satu kriteria membedakan kandidat. Jika skor protein hampir sama di semua kandidat, protein tidak membantu memilih, jadi bobotnya kecil. Kriteria yang skornya bervariasi lebar mendapat bobot besar.</p>
                <div class="mt-4 space-y-4 border border-line bg-paper-2 px-3.5 py-4 sm:px-5 sm:py-5">
                    <div class="math-block" data-math="e_j = -k \sum_{i=1}^{n} p_{ij} \ln(p_{ij})"></div>
                    <div class="math-block" data-math="w_j = \dfrac{1 - e_j}{\sum_{j} (1 - e_j)}"></div>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-ink-2"><span class="math-inline" data-math="p_{ij}"></span> adalah proporsi skor kandidat ke-<span class="math-inline" data-math="i"></span> pada kriteria ke-<span class="math-inline" data-math="j"></span> terhadap total kolomnya, dan <span class="math-inline" data-math="k = 1/\ln(n)"></span> dengan <span class="math-inline" data-math="n"></span> jumlah kandidat.</p>
            </section>

            <section id="topsis" class="rule-strong pt-5">
                <p class="num text-sm text-ink-3">03</p>
                <h2 class="font-display mt-1 text-2xl font-medium tracking-tight">Perankingan TOPSIS</h2>
                <p class="mt-3 leading-relaxed text-ink-2">Setelah tiap kriteria dikalikan bobotnya, TOPSIS mengukur jarak tiap kandidat ke solusi ideal (skor tertinggi di semua kriteria) dan ke solusi terburuk. Kandidat yang paling dekat ke ideal sekaligus paling jauh dari terburuk mendapat peringkat 1. Ini ranking utama yang ditampilkan.</p>
                <div class="mt-4 border border-line bg-paper-2 px-3.5 py-4 sm:px-5 sm:py-5">
                    <div class="math-block" data-math="C_i = \dfrac{D_i^-}{D_i^+ + D_i^-}"></div>
                </div>
                <p class="mt-3 text-sm leading-relaxed text-ink-2"><span class="math-inline" data-math="D_i^+"></span> jarak kandidat ke solusi ideal, <span class="math-inline" data-math="D_i^-"></span> jarak ke solusi terburuk. Kandidat dengan <span class="math-inline" data-math="C_i"></span> tertinggi menempati peringkat 1.</p>
            </section>

            <section id="bdd" class="rule-strong pt-5">
                <p class="num text-sm text-ink-3">04</p>
                <h2 class="font-display mt-1 text-2xl font-medium tracking-tight">Berat bersih ke berat kotor</h2>
                <p class="mt-3 leading-relaxed text-ink-2">Kebutuhan bahan mentah dihitung dari Bagian yang Dapat Dimakan (BDD) tiap bahan, supaya jumlah yang dibeli sudah memperhitungkan kulit, tulang, biji, dan bagian lain yang terbuang.</p>
                <div class="mt-4 border border-line bg-paper-2 px-3.5 py-4 sm:px-5 sm:py-5">
                    <div class="math-block" data-math="\text{berat kotor} = \dfrac{\text{berat bersih}}{\text{BDD}} \times 100 \times \text{jumlah porsi}"></div>
                </div>
            </section>

            <p class="rule pt-5 text-sm text-ink-3">Sumber: Tabel Komposisi Pangan Indonesia (TKPI), AKG Permenkes 28/2019, Juknis Penyelenggaraan Program MBG BGN Nomor 401.1 Tahun 2025.</p>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-math]').forEach(function (el) {
            katex.render(el.dataset.math, el, { throwOnError: false, displayMode: el.classList.contains('math-block') });
        });
    </script>
</x-layouts.app>
