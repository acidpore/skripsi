<?php

declare(strict_types=1);

namespace App\Services\Spk;

class EntropyWeighting
{
    private const MINIMUM_ALTERNATIVES = 2;
    private const ZERO = 0.0;
    private const ONE = 1.0;

    public function calculate(array $decisionMatrix): array
    {
        $criteriaNames = $this->extractCriteriaNames($decisionMatrix);
        $alternativeCount = count($decisionMatrix);

        if ($criteriaNames === [] || $alternativeCount < self::MINIMUM_ALTERNATIVES) {
            return $this->equalWeights($criteriaNames);
        }

        $diversifications = [];

        foreach ($criteriaNames as $criterion) {
            $column = $this->extractColumn($decisionMatrix, $criterion);
            $entropy = $this->entropyOfColumn($column, $alternativeCount);
            $diversifications[$criterion] = self::ONE - $entropy;
        }

        $diversificationSum = array_sum($diversifications);

        if ($diversificationSum <= self::ZERO) {
            return $this->equalWeights($criteriaNames);
        }

        $weights = [];

        foreach ($diversifications as $criterion => $diversification) {
            $weights[$criterion] = $diversification / $diversificationSum;
        }

        return $weights;
    }

    private function entropyOfColumn(array $column, int $alternativeCount): float
    {
        $columnSum = array_sum($column);

        // A column that sums to zero carries no information, so treat it as maximum entropy.
        if ($columnSum <= self::ZERO) {
            return self::ONE;
        }

        $entropyConstant = self::ONE / log((float) $alternativeCount);
        $accumulated = self::ZERO;

        foreach ($column as $value) {
            $proportion = $value / $columnSum;

            if ($proportion <= self::ZERO) {
                continue;
            }

            $accumulated += $proportion * log($proportion);
        }

        // Shannon entropy: e_j = -k * sum(p_ij * ln(p_ij))
        return -$entropyConstant * $accumulated;
    }

    private function extractColumn(array $decisionMatrix, string $criterion): array
    {
        $column = [];

        foreach ($decisionMatrix as $row) {
            $column[] = (float) ($row[$criterion] ?? self::ZERO);
        }

        return $column;
    }

    private function extractCriteriaNames(array $decisionMatrix): array
    {
        $firstRow = reset($decisionMatrix);

        if (!is_array($firstRow)) {
            return [];
        }

        return array_keys($firstRow);
    }

    private function equalWeights(array $criteriaNames): array
    {
        $criterionCount = count($criteriaNames);

        if ($criterionCount === 0) {
            return [];
        }

        $share = self::ONE / $criterionCount;
        $weights = [];

        foreach ($criteriaNames as $criterion) {
            $weights[$criterion] = $share;
        }

        return $weights;
    }
}
