<?php

namespace App\Console\Commands;

use App\Services\LearningStyleService;
use Illuminate\Console\Command;

class TestVarkPrediction extends Command
{
    protected $signature = 'ml:test';
    protected $description = 'Test VARK prediction with sample data';

    public function handle(LearningStyleService $service): int
    {
        if (!$service->isModelAvailable()) {
            $this->error('Model not found. Run: php artisan ml:train');
            return self::FAILURE;
        }

        $testCases = [
            ['q1' => 'b', 'q2' => 'b', 'q3' => 'b', 'q4' => 'b', 'q5' => 'b', 'q6' => 'b', 'q7' => 'b', 'q8' => 'b',
             'q9' => 'a', 'q10' => 'c', 'q11' => 'b', 'q12' => 'b', 'q13' => 'b', 'q14' => 'b', 'q15' => 'b', 'q16' => 'b'],
            ['q1' => 'a', 'q2' => 'a', 'q3' => 'a', 'q4' => 'a', 'q5' => 'a', 'q6' => 'a', 'q7' => 'a', 'q8' => 'a',
             'q9' => 'a', 'q10' => 'a', 'q11' => 'b', 'q12' => 'a', 'q13' => 'a', 'q14' => 'a', 'q15' => 'c', 'q16' => 'a'],
            ['q1' => 'c', 'q2' => 'c', 'q3' => 'c', 'q4' => 'c', 'q5' => 'c', 'q6' => 'c', 'q7' => 'c', 'q8' => 'c',
             'q9' => 'c', 'q10' => 'c', 'q11' => 'c', 'q12' => 'a', 'q13' => 'c', 'q14' => 'c', 'q15' => 'c', 'q16' => 'b'],
            ['q1' => 'd', 'q2' => 'd', 'q3' => 'd', 'q4' => 'd', 'q5' => 'd', 'q6' => 'd', 'q7' => 'd', 'q8' => 'd',
             'q9' => 'd', 'q10' => 'd', 'q11' => 'd', 'q12' => 'd', 'q13' => 'a', 'q14' => 'd', 'q15' => 'd', 'q16' => 'c'],
        ];

        $expected = ['Visual', 'Aural', 'ReadWrite', 'Kinesthetic'];

        $this->info('Testing VARK Predictions:');
        $this->newLine();

        foreach ($testCases as $index => $answers) {
            $prediction = $service->predict($answers);
            $scores = $service->processAnswers($answers);
            $status = $prediction === $expected[$index] ? 'PASS' : 'FAIL';

            $this->line("Test " . ($index + 1) . ": V={$scores[0]} A={$scores[1]} R={$scores[2]} K={$scores[3]}");
            $this->line("  Expected: {$expected[$index]} | Predicted: {$prediction} [{$status}]");
            $this->newLine();
        }

        return self::SUCCESS;
    }
}
