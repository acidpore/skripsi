<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'file_path',
        'external_url',
        'content_type',
        'vark_tags',
    ];

    protected function casts(): array
    {
        return [
            'vark_tags' => 'array',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }
}
