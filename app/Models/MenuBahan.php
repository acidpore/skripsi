<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuBahan extends Model
{
    use HasFactory;

    protected $table = 'menu_bahan';

    protected $fillable = [
        'menu_id',
        'bahan_pangan_id',
        'komponen',
        'berat_bersih_gram',
    ];

    protected function casts(): array
    {
        return [
            'berat_bersih_gram' => 'float',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function bahanPangan(): BelongsTo
    {
        return $this->belongsTo(BahanPangan::class);
    }
}
