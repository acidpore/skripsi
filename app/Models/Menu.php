<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    public const KOMPONEN = [
        'karbohidrat',
        'protein_hewani',
        'protein_nabati',
        'sayur',
        'buah',
        'lemak',
        'susu',
    ];

    protected $table = 'menu';

    protected $fillable = [
        'kode',
        'nama',
        'akg_sekali_makan_id',
        'sppg_id',
        'catatan',
    ];

    public function akgSekaliMakan(): BelongsTo
    {
        return $this->belongsTo(AkgSekaliMakan::class);
    }

    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }

    public function bahan(): HasMany
    {
        return $this->hasMany(MenuBahan::class);
    }

    public function scopeTersediaUntukSppg(Builder $query, ?int $sppgId): Builder
    {
        return $query->where(function (Builder $sub) use ($sppgId) {
            $sub->whereNull('sppg_id')->orWhere('sppg_id', $sppgId);
        });
    }
}
