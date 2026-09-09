<?php

declare(strict_types=1);

namespace App\Services\Spk;

class SawRanking implements RankingMethod
{
    private const METHOD_NAME = 'SAW';
    private const BENEFIT_TYPE = 'benefit';
    private const ZERO = 0.0;
    private const FIRST_RANK = 1;

    public function rank(array $decisionMatrix, array $weights, array $criteriaTypes): array
    {
        if ($decisionMatrix === []) {
            return [];
        }

        $criteriaNames = array_keys($weights);
        $normalized = $this->normalize($decisionMatrix, $criteriaNames, $criteriaTypes);

        $scores = [];

        foreach ($normalized as $index => $row) {
            $scores[$index] = $this->weightedSum($row, $weights);
        }

        return $this->buildRanking($scores);
    }

    public function name(): string
    {
        return self::METHOD_NAME;
    }

    private function normalize(array $decisionMatrix, array $criteriaNames, array $criteriaTypes): array
    {
        $normalized = [];

        foreach ($criteriaNames as $criterion) {
            $column = $this->extractColumn($decisionMatrix, $criterion);
            $isBenefit = ($criteriaTypes[$criterion] ?? self::BENEFIT_TYPE) === self::BENEFIT_TYPE;
            $maximum = max($column);
            $minimum = min($column);

            foreach ($column as $index => $value) {
                $normalized[$index][$criterion] = $isBenefit
                    ? $this->normalizeBenefit($value, $maximum)
                    : $this->normalizeCost($value, $minimum);
            }
        }

        return $normalized;
    }

    private function normalizeBenefit(float $value, float $maximum): float
    {
        return $maximum > self::ZERO ? $value / $maximum : self::ZERO;
    }

    private function normalizeCost(float $value, float $minimum): float
    {
        return $value > self::ZERO ? $minimum / $value : self::ZERO;
    }

    private function weightedSum(array $row, array $weights): float
    {
        $score = self::ZERO;

        foreach ($row as $criterion => $value) {
            $score += $value * (float) ($weights[$criterion] ?? self::ZERO);
        }

        return $score;
    }

    private function extractColumn(array $decisionMatrix, string $criterion): array
    {
        $column = [];

        foreach ($decisionMatrix as $index => $row) {
            $column[$index] = (float) ($row[$criterion] ?? self::ZERO);
        }

        return $column;
    }

    private function buildRanking(array $scores): array
    {
        arsort($scores);

        $ranking = [];
        $position = self::FIRST_RANK;

        foreach ($scores as $index => $score) {
            $ranking[] = [
                'index' => (int) $index,
                'score' => (float) $score,
                'rank' => $position,
            ];
            $position++;
        }

        return $ranking;
    }
}
