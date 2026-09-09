<?php

declare(strict_types=1);

namespace App\Services\Spk;

class SuitabilityScorer
{
    public const SKOR_MAKSIMAL = 1.0;
    public const SKOR_MINIMAL = 0.0;

    // Skor kesesuaian terhadap rentang AKG satu kali makan, kontinu di kedua batas dan selalu berada pada rentang 0 sampai 1
    public function score(float $nilai, float $batasBawah, float $batasAtas): float
    {
        if ($batasBawah <= self::SKOR_MINIMAL || $batasAtas <= self::SKOR_MINIMAL) {
            return self::SKOR_MINIMAL;
        }

        if ($nilai <= self::SKOR_MINIMAL) {
            return self::SKOR_MINIMAL;
        }

        if ($nilai < $batasBawah) {
            return $nilai / $batasBawah;
        }

        if ($nilai > $batasAtas) {
            return $batasAtas / $nilai;
        }

        return self::SKOR_MAKSIMAL;
    }
}
