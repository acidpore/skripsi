<?php

declare(strict_types=1);

// Ekspor data contoh perhitungan untuk naskah skripsi, read-only terhadap database. Jalankan: php scripts/export_perhitungan.php

use App\Models\AkgSekaliMakan;
use App\Models\BahanPangan;
use App\Models\Sppg;
use App\Services\Menu\MaterialRequirementService;
use App\Services\Menu\MenuCandidateGenerator;
use App\Services\Spk\DecisionMatrixBuilder;
use App\Services\Spk\EntropyWeighting;
use App\Services\Spk\TopsisRanking;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

const SPPG_ID = 1;
const JUMLAH_PORSI = 3000;
$kasus = [
    'a_sd_kelas_4-6' => ['Siswa SD/MI/SDLB Kelas 4-6', 'Siang'],
    'b_tk_paud' => ['Siswa TK/PAUD/TKLB', null],
];

$keluaran = base_path('export_perhitungan');
@mkdir($keluaran.'/data_acuan', 0777, true);

function tulisCsv(string $path, array $baris): void
{
    $berkas = fopen($path, 'w');
    fwrite($berkas, "\xEF\xBB\xBF");
    if ($baris !== []) {
        fputcsv($berkas, array_keys(reset($baris)));
        foreach ($baris as $isi) {
            fputcsv($berkas, array_map(static fn ($nilai) => is_float($nilai) ? round($nilai, 10) : $nilai, $isi));
        }
    }
    fclose($berkas);
}

function tulisJson(string $path, mixed $data): void
{
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));
}

// 1. Data acuan
foreach (['bahan_pangan', 'akg_sekali_makan', 'akg_harian'] as $tabel) {
    tulisCsv("$keluaran/data_acuan/$tabel.csv", array_map(static fn ($baris) => (array) $baris, DB::table($tabel)->orderBy('id')->get()->all()));
}

$satuanPenukar = [];
foreach (config('spk.satuan_penukar') as $kelompok => $komponen) {
    foreach ($komponen as $nama => $jumlahSp) {
        $satuanPenukar[] = ['kelompok_sasaran' => $kelompok, 'komponen' => $nama, 'jumlah_sp' => $jumlahSp, 'aktif' => $jumlahSp > 0 ? 'ya' : 'tidak'];
    }
}
foreach (config('spk.satuan_penukar_alias') as $alias => $acuan) {
    $satuanPenukar[] = ['kelompok_sasaran' => $alias, 'komponen' => 'alias_dari', 'jumlah_sp' => $acuan, 'aktif' => '-'];
}
tulisCsv("$keluaran/data_acuan/satuan_penukar_per_kelompok_sasaran.csv", $satuanPenukar);

$bahanPenukar = [];
foreach (config('spk.peta_komponen') as $komponen => $kelompokTkpi) {
    $daftar = BahanPangan::query()->whereIn('kelompok', $kelompokTkpi)->whereNotNull('berat_per_sp_gram')->whereNotNull('bdd_persen')->orderBy('nama')->get();
    foreach ($daftar as $bahan) {
        $bahanPenukar[] = [
            'komponen' => $komponen, 'kelompok_tkpi' => $bahan->kelompok, 'bahan_pangan_id' => $bahan->id, 'kode' => $bahan->kode,
            'nama' => $bahan->nama, 'berat_per_sp_gram' => $bahan->berat_per_sp_gram, 'bdd_persen' => $bahan->bdd_persen,
        ];
    }
}
tulisCsv("$keluaran/data_acuan/daftar_bahan_penukar_per_komponen.csv", $bahanPenukar);
tulisJson("$keluaran/data_acuan/config_spk.json", config('spk'));

// 2. Kasus perhitungan, langkah sama dengan RecommendationService::generate tanpa menulis ke database
$generator = app(MenuCandidateGenerator::class);
$pembangunMatriks = app(DecisionMatrixBuilder::class);
$entropy = app(EntropyWeighting::class);
$topsis = app(TopsisRanking::class);
$kebutuhanBahan = app(MaterialRequirementService::class);
$sppg = Sppg::findOrFail(SPPG_ID);
$definisiKriteria = config('spk.kriteria');
$namaKriteria = array_keys($definisiKriteria);

foreach ($kasus as $folder => [$kelompok, $waktu]) {
    $akg = AkgSekaliMakan::where('kelompok_sasaran', $kelompok)->when($waktu, fn ($q) => $q->where('waktu_distribusi', $waktu))->firstOrFail();
    $dir = "$keluaran/$folder";
    @mkdir($dir, 0777, true);

    $seed = (int) crc32(sprintf('%d-%d-%d', $sppg->id, $akg->id, JUMLAH_PORSI));
    $kandidat = $generator->generate($akg, [], $seed);
    $hasilMatriks = $pembangunMatriks->build(array_column($kandidat, 'gizi'), $akg, $definisiKriteria);
    $matriks = $hasilMatriks['matriks'];
    $bobot = $entropy->calculate($matriks);
    $ranking = $topsis->rank($matriks, $bobot, $hasilMatriks['tipe_kriteria']);
    $kode = static fn (int $i): string => 'A'.($i + 1);

    // Kandidat
    $barisKandidat = [];
    $barisKomposisi = [];
    foreach ($kandidat as $i => $k) {
        $baris = ['alternatif' => $kode($i)];
        foreach ($k['komposisi'] as $komponen => $item) {
            $baris[$komponen] = $item['nama'];
            $barisKomposisi[] = ['alternatif' => $kode($i), 'komponen' => $komponen, 'kode_bahan' => $item['kode'], 'nama_bahan' => $item['nama'], 'jumlah_sp' => $item['jumlah_sp'], 'berat_bersih_gram' => $item['berat_bersih_gram']];
        }
        foreach (['energi_kal', 'protein_g', 'lemak_g', 'karbohidrat_g'] as $zat) {
            $baris[$zat] = $k['gizi'][$zat];
        }
        $baris['kedekatan_energi'] = $k['kedekatan_energi'];
        $barisKandidat[] = $baris;
    }
    tulisCsv("$dir/01_kandidat_lolos_energi.csv", $barisKandidat);
    tulisCsv("$dir/01_kandidat_komposisi_detail.csv", $barisKomposisi);

    // Matriks keputusan
    tulisCsv("$dir/02_matriks_skor_kesesuaian.csv", array_map(fn ($i) => ['alternatif' => $kode($i)] + $matriks[$i], array_keys($matriks)));

    // Entropy, dihitung ulang dengan rumus Shannon untuk ditampilkan; w_j dibandingkan dengan EntropyWeighting
    $m = count($matriks);
    $k = 1 / log($m);
    $barisP = array_map(fn ($i) => ['alternatif' => $kode($i)], array_keys($matriks));
    $barisEntropy = [];
    $divergensi = [];
    foreach ($namaKriteria as $c) {
        $kolom = array_column($matriks, $c);
        $jumlah = array_sum($kolom);
        $akumulasi = 0.0;
        foreach ($kolom as $i => $x) {
            $p = $jumlah > 0 ? $x / $jumlah : 0.0;
            $barisP[$i]["p_$c"] = $p;
            $barisP[$i]["p_ln_p_$c"] = $p > 0 ? $p * log($p) : 0.0;
            $akumulasi += $barisP[$i]["p_ln_p_$c"];
        }
        $e = $jumlah > 0 ? -$k * $akumulasi : 1.0;
        $divergensi[$c] = 1 - $e;
        $barisEntropy[$c] = ['kriteria' => $c, 'jumlah_kolom' => $jumlah, 'k' => $k, 'sum_p_ln_p' => $akumulasi, 'e_j' => $e, 'd_j' => 1 - $e];
    }
    $totalD = array_sum($divergensi);
    foreach ($barisEntropy as $c => &$baris) {
        $baris['w_j_rumus'] = $totalD > 0 ? $divergensi[$c] / $totalD : 1 / count($namaKriteria);
        $baris['w_j_sistem'] = $bobot[$c];
    }
    unset($baris);
    tulisCsv("$dir/03_entropy_p_ij.csv", $barisP);
    tulisCsv("$dir/03_entropy_bobot.csv", array_values($barisEntropy));

    // TOPSIS, dihitung ulang langkah per langkah; C_i dibandingkan dengan TopsisRanking
    $norma = [];
    foreach ($namaKriteria as $c) {
        $norma[$c] = sqrt(array_sum(array_map(fn ($x) => $x * $x, array_column($matriks, $c))));
    }
    $r = $v = [];
    foreach ($matriks as $i => $baris) {
        foreach ($namaKriteria as $c) {
            $r[$i][$c] = $norma[$c] > 0 ? $baris[$c] / $norma[$c] : 0.0;
            $v[$i][$c] = $r[$i][$c] * $bobot[$c];
        }
    }
    $idealPositif = $idealNegatif = [];
    foreach ($namaKriteria as $c) {
        $kolom = array_column($v, $c);
        $benefit = $hasilMatriks['tipe_kriteria'][$c] === 'benefit';
        $idealPositif[$c] = $benefit ? max($kolom) : min($kolom);
        $idealNegatif[$c] = $benefit ? min($kolom) : max($kolom);
    }
    $peringkatPer = array_column($ranking, null, 'index');
    $barisTopsis = [];
    foreach ($v as $i => $baris) {
        $dPlus = sqrt(array_sum(array_map(fn ($c) => ($baris[$c] - $idealPositif[$c]) ** 2, $namaKriteria)));
        $dMin = sqrt(array_sum(array_map(fn ($c) => ($baris[$c] - $idealNegatif[$c]) ** 2, $namaKriteria)));
        $isi = ['alternatif' => $kode($i)];
        foreach ($namaKriteria as $c) {
            $isi["r_$c"] = $r[$i][$c];
        }
        foreach ($namaKriteria as $c) {
            $isi["v_$c"] = $v[$i][$c];
        }
        $isi += ['d_plus' => $dPlus, 'd_minus' => $dMin, 'c_i_rumus' => ($dPlus + $dMin) > 0 ? $dMin / ($dPlus + $dMin) : 0.5,
            'c_i_sistem' => $peringkatPer[$i]['score'], 'peringkat' => $peringkatPer[$i]['rank']];
        $barisTopsis[] = $isi;
    }
    usort($barisTopsis, fn ($a, $b) => $a['peringkat'] <=> $b['peringkat']);
    tulisCsv("$dir/04_topsis_per_alternatif.csv", $barisTopsis);
    tulisCsv("$dir/04_topsis_norma_dan_ideal.csv", array_map(fn ($c) => [
        'kriteria' => $c, 'tipe' => $hasilMatriks['tipe_kriteria'][$c], 'norma_akar_jumlah_kuadrat' => $norma[$c],
        'bobot' => $bobot[$c], 'a_plus' => $idealPositif[$c], 'a_minus' => $idealNegatif[$c],
    ], $namaKriteria));

    // Kebutuhan bahan peringkat 1
    $teratas = $kandidat[$ranking[0]['index']];
    $bddPer = BahanPangan::whereIn('id', array_column($teratas['komposisi'], 'bahan_pangan_id'))->pluck('bdd_persen', 'id');
    $totalSistem = $kebutuhanBahan->hitung($teratas['komposisi'], JUMLAH_PORSI);
    $barisKebutuhan = [];
    foreach ($teratas['komposisi'] as $komponen => $item) {
        $bdd = (float) $bddPer[$item['bahan_pangan_id']];
        $kotor = $item['berat_bersih_gram'] / ($bdd > 0 ? $bdd : 100) * 100;
        $barisKebutuhan[] = [
            'komponen' => $komponen, 'kode' => $item['kode'], 'nama' => $item['nama'], 'jumlah_sp' => $item['jumlah_sp'],
            'berat_bersih_per_porsi_g' => $item['berat_bersih_gram'], 'bdd_persen' => $bdd, 'berat_kotor_per_porsi_g' => $kotor,
            'jumlah_porsi' => JUMLAH_PORSI, 'total_berat_bersih_g' => $item['berat_bersih_gram'] * JUMLAH_PORSI,
            'total_berat_kotor_g' => $kotor * JUMLAH_PORSI, 'total_berat_kotor_kg' => $kotor * JUMLAH_PORSI / 1000,
            'total_berat_kotor_g_sistem' => $totalSistem[$item['bahan_pangan_id']]['berat_kotor_gram'],
        ];
    }
    tulisCsv("$dir/05_kebutuhan_bahan_peringkat_1.csv", $barisKebutuhan);

    tulisJson("$dir/00_ringkasan.json", [
        'kelompok_sasaran' => $akg->kelompok_sasaran,
        'waktu_distribusi' => $akg->waktu_distribusi,
        'akg_sekali_makan_id' => $akg->id,
        'sppg_id' => $sppg->id,
        'sppg_nama' => $sppg->nama,
        'jumlah_porsi' => JUMLAH_PORSI,
        'rumus_seed' => "crc32('{$sppg->id}-{$akg->id}-".JUMLAH_PORSI."')",
        'seed' => $seed,
        'maksimal_bahan_per_komponen' => config('spk.pembangkitan.maksimal_bahan_per_komponen'),
        'maksimal_kandidat' => config('spk.pembangkitan.maksimal_kandidat'),
        'jumlah_kandidat_masuk_matriks' => count($kandidat),
        'satuan_penukar' => config('spk.satuan_penukar.'.config('spk.satuan_penukar_alias.'.$akg->kelompok_sasaran, $akg->kelompok_sasaran)),
        'rentang_akg' => [
            'energi_kkal' => [$akg->energi_kkal_min, $akg->energi_kkal_max],
            'protein_g' => [$akg->protein_g_min, $akg->protein_g_max],
            'lemak_g' => [$akg->lemak_g_min, $akg->lemak_g_max],
            'karbohidrat_g' => [$akg->karbohidrat_g_min, $akg->karbohidrat_g_max],
        ],
        'bobot_entropy' => $bobot,
        'peringkat_1' => ['alternatif' => $kode($ranking[0]['index']), 'c_i' => $ranking[0]['score'], 'komposisi' => $teratas['komposisi'], 'gizi' => $teratas['gizi']],
        'ranking_topsis_sistem' => array_map(fn ($e) => ['alternatif' => $kode($e['index'])] + $e, $ranking),
        'catatan' => 'Kandidat diberi kode A1..An sesuai urutan setelah pengacakan ber-seed. Kolom *_rumus dihitung ulang manual, *_sistem diambil dari class aplikasi; keduanya harus sama.',
    ]);
}

// 3. Signature method publik
$kelas = [
    App\Livewire\RekomendasiMenu::class, App\Services\Spk\RecommendationService::class, MenuCandidateGenerator::class,
    DecisionMatrixBuilder::class, App\Services\Spk\SuitabilityScorer::class, EntropyWeighting::class, TopsisRanking::class,
    MaterialRequirementService::class, App\Services\Menu\MenuPersistenceService::class,
];
foreach (glob(app_path('Models/*.php')) as $berkas) {
    $kelas[] = 'App\\Models\\'.basename($berkas, '.php');
}
$tipe = static fn (?ReflectionType $t): string => $t === null ? 'mixed' : (string) $t;
$md = "# Signature Method Publik\n\nDihasilkan otomatis dari Reflection, hanya method publik yang dideklarasikan langsung di tiap class.\n";
foreach ($kelas as $nama) {
    $ref = new ReflectionClass($nama);
    $induk = $ref->getParentClass() ? ' extends '.$ref->getParentClass()->getShortName() : '';
    $antarmuka = $ref->getInterfaceNames() !== [] && !$ref->isSubclassOf(Illuminate\Database\Eloquent\Model::class) && !$ref->isSubclassOf(Livewire\Component::class)
        ? ' implements '.implode(', ', array_map(fn ($i) => (new ReflectionClass($i))->getShortName(), $ref->getInterfaceNames())) : '';
    $md .= "\n## {$ref->getName()}{$induk}{$antarmuka}\n\n";
    $properti = array_filter($ref->getProperties(ReflectionProperty::IS_PUBLIC), fn ($p) => $p->getDeclaringClass()->getName() === $nama);
    foreach ($properti as $p) {
        $md .= "- property `".$tipe($p->getType())." \${$p->getName()}`\n";
    }
    $konstanta = array_filter($ref->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC), fn ($c) => $c->getDeclaringClass()->getName() === $nama);
    foreach ($konstanta as $c) {
        $md .= "- const `{$c->getName()} = ".json_encode($c->getValue(), JSON_UNESCAPED_SLASHES)."`\n";
    }
    $method = array_filter($ref->getMethods(ReflectionMethod::IS_PUBLIC), fn ($m) => $m->getDeclaringClass()->getName() === $nama);
    if ($method === []) {
        $md .= "- (tidak ada method publik yang dideklarasikan sendiri)\n";
    }
    foreach ($method as $m) {
        $parameter = array_map(function (ReflectionParameter $p) use ($tipe) {
            $teks = ($p->hasType() ? $tipe($p->getType()).' ' : '').($p->isVariadic() ? '...' : '').'$'.$p->getName();
            if ($p->isDefaultValueAvailable()) {
                $teks .= ' = '.var_export($p->getDefaultValue(), true);
            }
            return str_replace(["array (\n)", "\n"], ['[]', ''], $teks);
        }, $m->getParameters());
        $statis = $m->isStatic() ? 'static ' : '';
        $kembali = $m->hasReturnType() ? ': '.$tipe($m->getReturnType()) : '';
        $md .= "- `{$statis}{$m->getName()}(".implode(', ', $parameter).")$kembali`\n";
    }
}
file_put_contents("$keluaran/signatures.md", $md);

// 4. Zip
$zipPath = base_path('export_perhitungan.zip');
@unlink($zipPath);
$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE);
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($keluaran, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $berkas) {
    $zip->addFile($berkas->getPathname(), 'export_perhitungan/'.str_replace('\\', '/', substr($berkas->getPathname(), strlen($keluaran) + 1)));
}
$zip->close();

echo "Selesai: $zipPath\n";
