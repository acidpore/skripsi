<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KebutuhanBahan extends Model
{
    use HasFactory;

    public const GRAM_PER_KILOGRAM = 1000;

    protected $table = 'kebutuhan_bahan';

    protected $fillable = [
        'rekomendasi_id',
        'bahan_pangan_id',
        'berat_bersih_gram',
        'berat_kotor_gram',
    ];

    protected function casts(): array
    {
        return [
            'berat_bersih_gram' => 'float',
            'berat_kotor_gram' => 'float',
        ];
    }

    public function rekomendasi(): BelongsTo
    {
        return $this->belongsTo(Rekomendasi::class);
    }

    public function bahanPangan(): BelongsTo
    {
        return $this->belongsTo(BahanPangan::class);
    }

    public function beratKotorKilogram(): float
    {
        return $this->berat_kotor_gram / self::GRAM_PER_KILOGRAM;
    }
}
