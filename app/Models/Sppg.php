<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sppg extends Model
{
    use HasFactory;

    protected $table = 'sppg';

    protected $fillable = [
        'nama',
        'provinsi',
        'kab_kota',
        'kecamatan',
        'kelurahan_desa',
        'alamat',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function rekomendasi(): HasMany
    {
        return $this->hasMany(Rekomendasi::class);
    }

    public function hasKoordinat(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }
}
