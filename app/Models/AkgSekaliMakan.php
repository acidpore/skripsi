<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AkgSekaliMakan extends Model
{
    use HasFactory;

    protected $table = 'akg_sekali_makan';

    protected $fillable = [
        'kelompok_sasaran',
        'waktu_distribusi',
        'persentase_akg',
        'energi_kkal_min',
        'energi_kkal_max',
        'protein_g_min',
        'protein_g_max',
        'lemak_g_min',
        'lemak_g_max',
        'karbohidrat_g_min',
        'karbohidrat_g_max',
    ];

    protected function casts(): array
    {
        return [
            'energi_kkal_min' => 'float',
            'energi_kkal_max' => 'float',
            'protein_g_min' => 'float',
            'protein_g_max' => 'float',
            'lemak_g_min' => 'float',
            'lemak_g_max' => 'float',
            'karbohidrat_g_min' => 'float',
            'karbohidrat_g_max' => 'float',
        ];
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function rekomendasi(): HasMany
    {
        return $this->hasMany(Rekomendasi::class);
    }

    public function titikTengah(string $zatGizi): float
    {
        return ($this->{$zatGizi.'_min'} + $this->{$zatGizi.'_max'}) / 2;
    }

    public function label(): string
    {
        return $this->kelompok_sasaran.' - '.$this->waktu_distribusi;
    }
}
