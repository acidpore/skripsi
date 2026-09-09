<?php

declare(strict_types=1);

namespace App\Services\Spk;

interface RankingMethod
{
    public function rank(array $decisionMatrix, array $weights, array $criteriaTypes): array;

    public function name(): string;
}
