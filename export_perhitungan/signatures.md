# Signature Method Publik

Dihasilkan otomatis dari Reflection, hanya method publik yang dideklarasikan langsung di tiap class.

## App\Livewire\RekomendasiMenu extends Component

- property `string $sppgCari`
- property `?int $sppgId`
- property `?int $akgId`
- property `?int $porsi`
- property `?int $rekomendasiId`
- property `?string $pesanError`
- property `?string $pesanSukses`
- property `array $namaMenuPerPeringkat`
- property `bool $sppgTerkunci`
- `mount(): void`
- `rules(): array`
- `messages(): array`
- `daftarSppg(): Illuminate\Support\Collection`
- `daftarAkg(): Illuminate\Support\Collection`
- `sppgTerpilih(): ?App\Models\Sppg`
- `rekomendasi(): ?App\Models\Rekomendasi`
- `kebutuhanBahan(): Illuminate\Support\Collection`
- `pilihSppg(int $sppgId): void`
- `generate(): void`
- `simpan(int $indeksPeringkat): void`
- `render()`

## App\Services\Spk\RecommendationService

- `__construct(App\Services\Menu\MenuCandidateGenerator $candidateGenerator, App\Services\Spk\DecisionMatrixBuilder $matrixBuilder, App\Services\Spk\EntropyWeighting $entropyWeighting, App\Services\Spk\TopsisRanking $topsisRanking, App\Services\Menu\MaterialRequirementService $materialRequirementService)`
- `generateUntuk(App\Models\User $pengguna, int $sppgId, int $akgId, int $jumlahPorsi): App\Models\Rekomendasi`
- `generate(App\Models\Sppg $sppg, App\Models\AkgSekaliMakan $akg, int $jumlahPorsi, array $bahanTersediaPerKomponen = []): App\Models\Rekomendasi`

## App\Services\Menu\MenuCandidateGenerator

- `generate(App\Models\AkgSekaliMakan $akg, array $bahanTersediaPerKomponen = [], ?int $seed = NULL): array`
- `didukung(App\Models\AkgSekaliMakan $akg): bool`

## App\Services\Spk\DecisionMatrixBuilder

- `__construct(App\Services\Spk\SuitabilityScorer $suitabilityScorer)`
- `build(array $daftarGizi, App\Models\AkgSekaliMakan $akg, array $definisiKriteria): array`

## App\Services\Spk\SuitabilityScorer

- const `SKOR_MAKSIMAL = 1`
- const `SKOR_MINIMAL = 0`
- `score(float $nilai, float $batasBawah, float $batasAtas): float`

## App\Services\Spk\EntropyWeighting

- `calculate(array $decisionMatrix): array`

## App\Services\Spk\TopsisRanking implements RankingMethod

- `rank(array $decisionMatrix, array $weights, array $criteriaTypes): array`
- `name(): string`

## App\Services\Menu\MaterialRequirementService

- `forRekomendasi(App\Models\Rekomendasi $rekomendasi): Illuminate\Support\Collection`
- `calculateForRekomendasi(App\Models\Rekomendasi $rekomendasi): Illuminate\Support\Collection`
- `hitung(array $komposisi, int $jumlahPorsi): array`

## App\Services\Menu\MenuPersistenceService

- `__construct(App\Services\Menu\MaterialRequirementService $materialRequirementService)`
- `simpanPeringkat(App\Models\Rekomendasi $rekomendasi, int $indeksPeringkat, string $nama): App\Models\Menu`
- `simpanDariRekomendasi(App\Models\Rekomendasi $rekomendasi, string $nama, ?array $komposisi = NULL): App\Models\Menu`

## App\Models\AkgHarian extends Model

- `static factory($count = NULL, $state = [])`

## App\Models\AkgSekaliMakan extends Model

- `menus(): Illuminate\Database\Eloquent\Relations\HasMany`
- `rekomendasi(): Illuminate\Database\Eloquent\Relations\HasMany`
- `scopeBerurutan(Illuminate\Database\Eloquent\Builder $query): Illuminate\Database\Eloquent\Builder`
- `titikTengah(string $zatGizi): float`
- `label(): string`
- `static factory($count = NULL, $state = [])`

## App\Models\BahanPangan extends Model

- const `BASIS_GIZI_GRAM = 100`
- const `BDD_PENUH_PERSEN = 100`
- `scopeKelompok(Illuminate\Database\Eloquent\Builder $query, ?string $kelompok): Illuminate\Database\Eloquent\Builder`
- `scopeCariNama(Illuminate\Database\Eloquent\Builder $query, ?string $kataKunci): Illuminate\Database\Eloquent\Builder`
- `static factory($count = NULL, $state = [])`

## App\Models\KebutuhanBahan extends Model

- const `GRAM_PER_KILOGRAM = 1000`
- `rekomendasi(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `bahanPangan(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `beratKotorKilogram(): float`
- `static factory($count = NULL, $state = [])`

## App\Models\Menu extends Model

- const `KOMPONEN = ["karbohidrat","protein_hewani","protein_nabati","sayur","buah","lemak","susu"]`
- `akgSekaliMakan(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `sppg(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `bahan(): Illuminate\Database\Eloquent\Relations\HasMany`
- `scopeTersediaUntukSppg(Illuminate\Database\Eloquent\Builder $query, ?int $sppgId): Illuminate\Database\Eloquent\Builder`
- `static factory($count = NULL, $state = [])`

## App\Models\MenuBahan extends Model

- `menu(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `bahanPangan(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `static factory($count = NULL, $state = [])`

## App\Models\Rekomendasi extends Model

- `sppg(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `akgSekaliMakan(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `menuTerpilih(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `kebutuhanBahan(): Illuminate\Database\Eloquent\Relations\HasMany`
- `static factory($count = NULL, $state = [])`

## App\Models\Sppg extends Model

- `users(): Illuminate\Database\Eloquent\Relations\HasMany`
- `menus(): Illuminate\Database\Eloquent\Relations\HasMany`
- `rekomendasi(): Illuminate\Database\Eloquent\Relations\HasMany`
- `scopeCariNama(Illuminate\Database\Eloquent\Builder $query, string $kataKunci): Illuminate\Database\Eloquent\Builder`
- `hasKoordinat(): bool`
- `static factory($count = NULL, $state = [])`

## App\Models\User extends User

- const `ROLE_ADMIN = "admin"`
- const `ROLE_OPERATOR = "operator"`
- `sppg(): Illuminate\Database\Eloquent\Relations\BelongsTo`
- `isAdmin(): bool`
- `isOperator(): bool`
- `dapatMengaksesSppg(int $sppgId): bool`
- `static factory($count = NULL, $state = [])`
- `notifications()`
- `readNotifications()`
- `unreadNotifications()`
- `notify($instance)`
- `notifyNow($instance, ?array $channels = NULL)`
- `routeNotificationFor($driver, $notification = NULL)`
