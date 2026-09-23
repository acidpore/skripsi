<?php

declare(strict_types=1);

return [

    // Energi bukan kriteria keputusan: MenuCandidateGenerator sudah memangkas kandidat ke rentang AKG energi sebagai syarat kelayakan (gate), sehingga di titik ini seluruh kandidat pasti bernilai 1 dan tidak informatif bagi Entropy. Hanya protein/lemak/karbohidrat yang jadi kriteria keputusan (matriks)
    'kriteria' => [
        'kesesuaian_protein' => [
            'label' => 'Kesesuaian Protein',
            'tipe' => 'benefit',
            'kolom_gizi' => 'protein_g',
            'kolom_min' => 'protein_g_min',
            'kolom_max' => 'protein_g_max',
        ],
        'kesesuaian_lemak' => [
            'label' => 'Kesesuaian Lemak',
            'tipe' => 'benefit',
            'kolom_gizi' => 'lemak_g',
            'kolom_min' => 'lemak_g_min',
            'kolom_max' => 'lemak_g_max',
        ],
        'kesesuaian_karbohidrat' => [
            'label' => 'Kesesuaian Karbohidrat',
            'tipe' => 'benefit',
            'kolom_gizi' => 'karbohidrat_g',
            'kolom_min' => 'karbohidrat_g_min',
            'kolom_max' => 'karbohidrat_g_max',
        ],
    ],

    // Peran komponen menu diturunkan dari kelompok TKPI, bukan penilaian pribadi, agar dapat dipertanggungjawabkan
    'peta_komponen' => [
        'karbohidrat' => ['Serealia', 'Umbi'],
        'protein_hewani' => ['Daging', 'Ikan dsb', 'Telur'],
        'protein_nabati' => ['Kacang'],
        'sayur' => ['Sayur'],
        'buah' => ['Buah'],
        'lemak' => ['Lemak'],
        'susu' => ['Susu'],
    ],

    // Nama penukar Juknis yang tidak berawalan sama dengan nama TKPI, dipetakan eksplisit ke nama TKPI
    'penukar_alias' => [
        'Mie kering' => 'Mi kering',
        'Daging ayam' => 'Ayam, daging, segar',
        'Daging sapi' => 'Sapi, daging, lemak sedang, segar',
        'Ikan segar' => 'Ikan oci, kembung, segar',
        'Susu' => 'Susu sapi, segar',
    ],

    // Jumlah satuan penukar per komponen menurut Standar Makan Siang Juknis BGN 401.1/2025, nilai 0 berarti komponen tidak dipakai kelompok sasaran tersebut
    'satuan_penukar' => [
        'Siswa TK/PAUD/TKLB' => [
            'karbohidrat' => 1.25, 'protein_hewani' => 1, 'protein_nabati' => 0.5,
            'sayur' => 0.5, 'buah' => 1, 'lemak' => 1, 'susu' => 0,
        ],
        'Siswa SD/MI/SDLB Kelas 4-6' => [
            'karbohidrat' => 1.5, 'protein_hewani' => 1, 'protein_nabati' => 0.5,
            'sayur' => 0.5, 'buah' => 1, 'lemak' => 1, 'susu' => 0.625,
        ],
        'Siswa SMP/MTs/SMPLB' => [
            'karbohidrat' => 1.75, 'protein_hewani' => 1, 'protein_nabati' => 0.5,
            'sayur' => 0.5, 'buah' => 1, 'lemak' => 1, 'susu' => 1,
        ],
        'Siswa SMA/SMK/MA/SMALB' => [
            'karbohidrat' => 2, 'protein_hewani' => 1.5, 'protein_nabati' => 0.5,
            'sayur' => 1, 'buah' => 1, 'lemak' => 1, 'susu' => 1,
        ],
        'Ibu Hamil' => [
            'karbohidrat' => 2.5, 'protein_hewani' => 2, 'protein_nabati' => 1,
            'sayur' => 1, 'buah' => 1, 'lemak' => 2, 'susu' => 0,
        ],
        'Ibu Menyusui' => [
            'karbohidrat' => 2.5, 'protein_hewani' => 2, 'protein_nabati' => 1,
            'sayur' => 1, 'buah' => 1, 'lemak' => 2, 'susu' => 0,
        ],
    ],

    // Kelompok sasaran tanpa gambar standar sendiri namun rentang AKG-nya identik dengan kelompok lain
    'satuan_penukar_alias' => [
        'Pendidik' => 'Siswa SMA/SMK/MA/SMALB',
        'Tenaga Kependidikan' => 'Siswa SMA/SMK/MA/SMALB',
    ],

    'pembangkitan' => [
        // Katup pengaman bila daftar penukar diperluas, null berarti nonaktif karena daftar Juknis saat ini hanya menghasilkan 7.200 kombinasi
        'maksimal_bahan_per_komponen' => null,
        // Kandidat yang masuk matriks keputusan, menjawab pertanyaan top-N pada PRD Bagian 3.4
        'maksimal_kandidat' => 20,
    ],

];
