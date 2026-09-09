<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkgHarian extends Model
{
    use HasFactory;

    protected $table = 'akg_harian';

    protected $fillable = [
        'kelompok',
        'kelompok_umur',
        'berat_badan_kg',
        'tinggi_badan_cm',
        'energi_kkal',
        'protein_g',
        'lemak_total_g',
        'karbohidrat_g',
    ];

    protected function casts(): array
    {
        return [
            'berat_badan_kg' => 'float',
            'tinggi_badan_cm' => 'float',
            'energi_kkal' => 'float',
            'protein_g' => 'float',
            'lemak_total_g' => 'float',
            'karbohidrat_g' => 'float',
        ];
    }
}
