<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Belanja - {{ $rekomendasi->akgSekaliMakan->label() }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=IBM+Plex+Sans:wght@400;500&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: "IBM Plex Sans", system-ui, sans-serif; color: #1B1A17; background: #fff; margin: 0; padding: 2.5rem; max-width: 48rem; }
        .eyebrow { font-family: "IBM Plex Mono", monospace; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: #8A857A; margin: 0; }
        h1 { font-family: Fraunces, Georgia, serif; font-weight: 500; font-size: 1.75rem; margin: .25rem 0 0; letter-spacing: -.01em; }
        .meta { color: #4F4B43; font-size: .875rem; margin: .5rem 0 2rem; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        th { text-align: left; border-bottom: 1px solid #1B1A17; padding: .5rem 0; }
        td { border-bottom: 1px solid #D6D0C2; padding: .6rem 0; }
        .num { font-family: "IBM Plex Mono", monospace; font-variant-numeric: tabular-nums; text-align: right; }
        .note { color: #8A857A; font-size: .8rem; margin-top: 1.5rem; }
        .aksi { margin-bottom: 2rem; }
        .aksi button { font: inherit; font-size: .875rem; padding: .5rem 1rem; background: #1B1A17; color: #F4F1EA; border: 0; border-radius: 2px; cursor: pointer; }
        @media print { .aksi { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="aksi"><button onclick="window.print()">Cetak atau simpan sebagai PDF</button></div>

    <p class="eyebrow">Daftar belanja</p>
    <h1>{{ $rekomendasi->sppg->nama }}</h1>
    <p class="meta">{{ $rekomendasi->akgSekaliMakan->label() }} &middot; {{ $rekomendasi->jumlah_porsi }} porsi &middot; dibuat {{ $rekomendasi->created_at->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th class="eyebrow">Bahan</th>
                <th class="eyebrow num">Berat bersih (kg)</th>
                <th class="eyebrow num">Berat kotor (kg)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kebutuhanBahan as $kebutuhan)
                <tr>
                    <td>{{ $kebutuhan->bahanPangan->nama }}</td>
                    <td class="num">{{ number_format($kebutuhan->berat_bersih_gram / 1000, 2) }}</td>
                    <td class="num">{{ number_format($kebutuhan->beratKotorKilogram(), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="note">Berat kotor = berat bersih dibagi Bagian yang Dapat Dimakan (BDD), dikali 100, dikali jumlah porsi.</p>
</body>
</html>
