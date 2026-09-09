<?php

declare(strict_types=1);

namespace App\Services\Spk;

class TopsisRanking implements RankingMethod
{
    private const METHOD_NAME = 'TOPSIS';
    private const BENEFIT_TYPE = 'benefit';
    private const ZERO = 0.0;
    private const NEUTRAL_SCORE = 0.5;
    private const FIRST_RANK = 1;

    public function rank(array $decisionMatrix, array $weights, array $criteriaTypes): array
    {
        if ($decisionMatrix === []) {
            return [];
        }

        $criteriaNames = array_keys($weights);
        $normalized = $this->normalizeByVectorLength($decisionMatrix, $criteriaNames);
        $weighted = $this->applyWeights($normalized, $weights);
        $positiveIdeal = $this->idealSolution($weighted, $criteriaNames, $criteriaTypes, true);
        $negativeIdeal = $this->idealSolution($weighted, $criteriaNames, $criteriaTypes, false);

        $scores = [];

        foreach ($weighted as $index => $row) {
            $positiveDistance = $this->euclideanDistance($row, $positiveIdeal, $criteriaNames);
            $negativeDistance = $this->euclideanDistance($row, $negativeIdeal, $criteriaNames);
            $scores[$index] = $this->preferenceScore($positiveDistance, $negativeDistance);
        }

        return $this->buildRanking($scores);
    }

    public function name(): string
    {
        return self::METHOD_NAME;
    }

    private function normalizeByVectorLength(array $decisionMatrix, array $criteriaNames): array
    {
        $norms = [];

        foreach ($criteriaNames as $criterion) {
            $sumOfSquares = self::ZERO;

            foreach ($decisionMatrix as $row) {
                $value = (float) ($row[$criterion] ?? self::ZERO);
                $sumOfSquares += $value * $value;
            }

            $norms[$criterion] = sqrt($sumOfSquares);
        }

        $normalized = [];

        foreach ($decisionMatrix as $index => $row) {
            foreach ($criteriaNames as $criterion) {
                $value = (float) ($row[$criterion] ?? self::ZERO);
                $norm = $norms[$criterion];
                $normalized[$index][$criterion] = $norm > self::ZERO ? $value / $norm : self::ZERO;
            }
        }

        return $normalized;
    }

    private function applyWeights(array $normalized, array $weights): array
    {
        $weighted = [];

        foreach ($normalized as $index => $row) {
            foreach ($row as $criterion => $value) {
                $weighted[$index][$criterion] = $value * (float) ($weights[$criterion] ?? self::ZERO);
            }
        }

        return $weighted;
    }

    private function idealSolution(array $weighted, array $criteriaNames, array $criteriaTypes, bool $positive): array
    {
        $ideal = [];

        foreach ($criteriaNames as $criterion) {
            $column = array_column($weighted, $criterion);

            if ($column === []) {
                $ideal[$criterion] = self::ZERO;
                continue;
            }

            $isBenefit = ($criteriaTypes[$criterion] ?? self::BENEFIT_TYPE) === self::BENEFIT_TYPE;
            $takeMaximum = $isBenefit === $positive;
            $ideal[$criterion] = $takeMaximum ? max($column) : min($column);
        }

        return $ideal;
    }

    private function euclideanDistance(array $row, array $reference, array $criteriaNames): float
    {
        $sumOfSquares = self::ZERO;

        foreach ($criteriaNames as $criterion) {
            $difference = ($row[$criterion] ?? self::ZERO) - ($reference[$criterion] ?? self::ZERO);
            $sumOfSquares += $difference * $difference;
        }

        return sqrt($sumOfSquares);
    }

    private function preferenceScore(float $positiveDistance, float $negativeDistance): float
    {
        $total = $positiveDistance + $negativeDistance;

        // All alternatives identical means every distance is zero, so no alternative is preferred.
        if ($total <= self::ZERO) {
            return self::NEUTRAL_SCORE;
        }

        return $negativeDistance / $total;
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
