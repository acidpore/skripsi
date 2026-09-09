<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanPangan extends Model
{
    use HasFactory;

    public const BASIS_GIZI_GRAM = 100;
    public const BDD_PENUH_PERSEN = 100;

    protected $table = 'bahan_pangan';

    protected $fillable = [
        'kode',
        'nama',
        'jenis',
        'kelompok',
        'energi_kal',
        'protein_g',
        'lemak_g',
        'karbohidrat_g',
        'kalsium_mg',
        'besi_mg',
        'vitamin_c_mg',
        'bdd_persen',
        'berat_per_sp_gram',
    ];

    protected function casts(): array
    {
        return [
            'energi_kal' => 'float',
            'protein_g' => 'float',
            'lemak_g' => 'float',
            'karbohidrat_g' => 'float',
            'kalsium_mg' => 'float',
            'besi_mg' => 'float',
            'vitamin_c_mg' => 'float',
            'bdd_persen' => 'float',
            'berat_per_sp_gram' => 'float',
        ];
    }

    public function scopeKelompok(Builder $query, ?string $kelompok): Builder
    {
        return $kelompok === null ? $query : $query->where('kelompok', $kelompok);
    }

    public function scopeCariNama(Builder $query, ?string $kataKunci): Builder
    {
        return $kataKunci === null || $kataKunci === ''
            ? $query
            : $query->where('nama', 'like', '%'.$kataKunci.'%');
    }
}
