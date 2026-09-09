<?php

declare(strict_types=1);

namespace App\Services\Spk;

class SpearmanCorrelation
{
    private const MINIMUM_ITEMS = 2;
    private const ZERO = 0.0;
    private const NO_CORRELATION = 0.0;

    public function calculate(array $rankingA, array $rankingB): float
    {
        $ranksByIndexA = $this->mapRanksByIndex($rankingA);
        $ranksByIndexB = $this->mapRanksByIndex($rankingB);
        $sharedIndexes = array_intersect(array_keys($ranksByIndexA), array_keys($ranksByIndexB));

        if (count($sharedIndexes) < self::MINIMUM_ITEMS) {
            return self::NO_CORRELATION;
        }

        $ranksA = [];
        $ranksB = [];

        foreach ($sharedIndexes as $index) {
            $ranksA[] = $ranksByIndexA[$index];
            $ranksB[] = $ranksByIndexB[$index];
        }

        // Pearson correlation of ranks is used instead of the d-squared shortcut because tied scores are possible.
        return $this->pearsonOfRanks($ranksA, $ranksB);
    }

    private function pearsonOfRanks(array $ranksA, array $ranksB): float
    {
        $itemCount = count($ranksA);
        $meanA = array_sum($ranksA) / $itemCount;
        $meanB = array_sum($ranksB) / $itemCount;

        $covariance = self::ZERO;
        $varianceA = self::ZERO;
        $varianceB = self::ZERO;

        for ($position = 0; $position < $itemCount; $position++) {
            $deviationA = $ranksA[$position] - $meanA;
            $deviationB = $ranksB[$position] - $meanB;
            $covariance += $deviationA * $deviationB;
            $varianceA += $deviationA * $deviationA;
            $varianceB += $deviationB * $deviationB;
        }

        if ($varianceA <= self::ZERO || $varianceB <= self::ZERO) {
            return self::NO_CORRELATION;
        }

        return $covariance / sqrt($varianceA * $varianceB);
    }

    private function mapRanksByIndex(array $ranking): array
    {
        $ranksByIndex = [];

        foreach ($ranking as $entry) {
            if (!isset($entry['index'], $entry['rank'])) {
                continue;
            }

            $ranksByIndex[(int) $entry['index']] = (float) $entry['rank'];
        }

        return $ranksByIndex;
    }
}
