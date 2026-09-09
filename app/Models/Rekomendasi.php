<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rekomendasi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi';

    protected $fillable = [
        'sppg_id',
        'akg_sekali_makan_id',
        'menu_terpilih_id',
        'komposisi_terpilih',
        'jumlah_porsi',
        'bobot_entropy',
        'hasil_ranking_topsis',
        'hasil_ranking_saw',
        'koefisien_spearman',
    ];

    protected function casts(): array
    {
        return [
            'komposisi_terpilih' => 'array',
            'bobot_entropy' => 'array',
            'hasil_ranking_topsis' => 'array',
            'hasil_ranking_saw' => 'array',
            'koefisien_spearman' => 'float',
            'jumlah_porsi' => 'integer',
        ];
    }

    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }

    public function akgSekaliMakan(): BelongsTo
    {
        return $this->belongsTo(AkgSekaliMakan::class);
    }

    public function menuTerpilih(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_terpilih_id');
    }

    public function kebutuhanBahan(): HasMany
    {
        return $this->hasMany(KebutuhanBahan::class);
    }
}
