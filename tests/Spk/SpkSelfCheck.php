<?php

declare(strict_types=1);

namespace Tests\Spk;

use App\Services\Spk\EntropyWeighting;
use App\Services\Spk\TopsisRanking;

class SpkSelfCheck
{
    private const EPSILON = 0.000001;
    private const NEAR_ZERO_WEIGHT = 0.001;

    public static function run(): void
    {
        self::checkEntropyWeightsSumToOne();
        self::checkConstantCriterionGetsNearZeroWeight();
        self::checkDominantAlternativeRanksFirst();
        self::checkCostCriterionInvertsPreference();
    }

    private static function checkEntropyWeightsSumToOne(): void
    {
        $weights = (new EntropyWeighting())->calculate(self::sampleMatrix());

        assert(count($weights) === 3);
        assert(self::isClose(array_sum($weights), 1.0));
    }

    private static function checkConstantCriterionGetsNearZeroWeight(): void
    {
        $matrix = [
            ['protein' => 10.0, 'price' => 5.0, 'constant' => 7.0],
            ['protein' => 40.0, 'price' => 9.0, 'constant' => 7.0],
            ['protein' => 25.0, 'price' => 2.0, 'constant' => 7.0],
        ];

        $weights = (new EntropyWeighting())->calculate($matrix);

        assert($weights['constant'] < self::NEAR_ZERO_WEIGHT);
        assert($weights['protein'] > $weights['constant']);
    }

    private static function checkDominantAlternativeRanksFirst(): void
    {
        $matrix = [
            ['protein' => 10.0, 'calorie' => 10.0, 'price' => 9.0],
            ['protein' => 90.0, 'calorie' => 90.0, 'price' => 1.0],
            ['protein' => 20.0, 'calorie' => 20.0, 'price' => 8.0],
        ];
        $weights = ['protein' => 0.4, 'calorie' => 0.3, 'price' => 0.3];
        $types = ['protein' => 'benefit', 'calorie' => 'benefit', 'price' => 'cost'];

        $topsis = (new TopsisRanking())->rank($matrix, $weights, $types);

        assert($topsis[0]['index'] === 1);
        assert($topsis[0]['rank'] === 1);
        assert(count($topsis) === 3);
    }

    private static function checkCostCriterionInvertsPreference(): void
    {
        $matrix = [
            ['price' => 10.0],
            ['price' => 50.0],
        ];
        $weights = ['price' => 1.0];

        $asBenefit = (new TopsisRanking())->rank($matrix, $weights, ['price' => 'benefit']);
        $asCost = (new TopsisRanking())->rank($matrix, $weights, ['price' => 'cost']);

        assert($asBenefit[0]['index'] === 1);
        assert($asCost[0]['index'] === 0);
    }

    private static function sampleMatrix(): array
    {
        return [
            ['protein' => 10.0, 'calorie' => 300.0, 'price' => 5000.0],
            ['protein' => 25.0, 'calorie' => 450.0, 'price' => 7000.0],
            ['protein' => 18.0, 'calorie' => 380.0, 'price' => 6200.0],
        ];
    }

    private static function isClose(float $actual, float $expected): bool
    {
        return abs($actual - $expected) < self::EPSILON;
    }
}
