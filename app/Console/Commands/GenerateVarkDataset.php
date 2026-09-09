<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateVarkDataset extends Command
{
    protected $signature = 'ml:generate 
                            {--samples=1000 : Number of samples to generate}
                            {--export : Export to CSV for thesis appendix}';
    
    protected $description = 'Generate synthetic VARK dataset with realistic distribution';

    protected array $modes = ['Visual', 'Aural', 'ReadWrite', 'Kinesthetic'];

    public function handle(): int
    {
        $samples = (int) $this->option('samples');
        
        $this->info("Generating {$samples} synthetic VARK records...");
        $this->info("Using Fleming (2001) distribution: K(40%), A(25%), R(25%), V(10%)");
        $this->newLine();

        $dataset = $this->generateDataset($samples);
        
        $this->displayDistribution($dataset);
        $this->saveDataset($dataset);

        if ($this->option('export')) {
            $this->exportToCsv($dataset);
        }

        $this->newLine();
        $this->info("Dataset saved to storage/ai/vark_dataset.json");
        $this->info("Run 'php artisan ml:train --from-dataset' to train with this data.");

        return self::SUCCESS;
    }

    protected function generateDataset(int $samples): array
    {
        $dataset = ['samples' => [], 'labels' => []];
        $weights = [0.10, 0.25, 0.25, 0.40]; // V, A, R, K

        for ($i = 0; $i < $samples; $i++) {
            $basePref = $this->weightedRandom($this->modes, $weights);
            $scores = $this->generateScores($basePref);
            $label = $this->determineLabel($scores);

            $dataset['samples'][] = array_values($scores);
            $dataset['labels'][] = $label;
        }

        return $dataset;
    }

    protected function generateScores(string $basePref): array
    {
        $scores = [];

        foreach ($this->modes as $mode) {
            if ($mode === $basePref) {
                $scores[$mode] = $this->clippedNormal(12, 2, 8, 16);
            } else {
                $isMultimodal = (mt_rand(1, 100) <= 60);
                if ($isMultimodal) {
                    $scores[$mode] = $this->clippedNormal(9, 3, 4, 14);
                } else {
                    $scores[$mode] = $this->clippedNormal(4, 2, 0, 8);
                }
            }
        }

        return $scores;
    }

    protected function determineLabel(array $scores): string
    {
        arsort($scores);
        $sorted = array_values($scores);
        
        $primaryScore = $sorted[0];
        $secondaryScore = $sorted[1];
        $diff = $primaryScore - $secondaryScore;

        if ($diff < 2) {
            return 'Multimodal';
        }

        return array_keys($scores)[0];
    }

    protected function weightedRandom(array $items, array $weights): string
    {
        $sum = array_sum($weights);
        $rand = mt_rand(1, (int)($sum * 100)) / 100;
        
        $cumulative = 0;
        foreach ($items as $index => $item) {
            $cumulative += $weights[$index];
            if ($rand <= $cumulative) {
                return $item;
            }
        }

        return end($items);
    }

    protected function clippedNormal(float $mean, float $stdDev, int $min, int $max): int
    {
        $u1 = mt_rand(1, 10000) / 10000;
        $u2 = mt_rand(1, 10000) / 10000;
        $normal = sqrt(-2 * log($u1)) * cos(2 * M_PI * $u2);
        $value = $mean + ($normal * $stdDev);
        
        return (int) max($min, min($max, round($value)));
    }

    protected function displayDistribution(array $dataset): void
    {
        $this->info('=== LABEL DISTRIBUTION ===');
        $this->newLine();

        $counts = array_count_values($dataset['labels']);
        arsort($counts);
        
        $total = count($dataset['labels']);
        $rows = [];

        foreach ($counts as $label => $count) {
            $percentage = number_format(($count / $total) * 100, 1);
            $rows[] = [$label, $count, $percentage . '%'];
        }

        $this->table(['Label', 'Count', 'Percentage'], $rows);

        $this->newLine();
        $this->info('=== SCORE STATISTICS ===');
        $this->newLine();

        $stats = $this->calculateStats($dataset['samples']);
        $this->table(
            ['Dimension', 'Mean', 'Std Dev', 'Min', 'Max'],
            [
                ['Visual', $stats[0]['mean'], $stats[0]['std'], $stats[0]['min'], $stats[0]['max']],
                ['Aural', $stats[1]['mean'], $stats[1]['std'], $stats[1]['min'], $stats[1]['max']],
                ['ReadWrite', $stats[2]['mean'], $stats[2]['std'], $stats[2]['min'], $stats[2]['max']],
                ['Kinesthetic', $stats[3]['mean'], $stats[3]['std'], $stats[3]['min'], $stats[3]['max']],
            ]
        );
    }

    protected function calculateStats(array $samples): array
    {
        $stats = [];
        
        for ($dim = 0; $dim < 4; $dim++) {
            $values = array_column($samples, $dim);
            $mean = array_sum($values) / count($values);
            
            $variance = 0;
            foreach ($values as $val) {
                $variance += pow($val - $mean, 2);
            }
            $std = sqrt($variance / count($values));
            
            $stats[$dim] = [
                'mean' => number_format($mean, 2),
                'std' => number_format($std, 2),
                'min' => min($values),
                'max' => max($values),
            ];
        }

        return $stats;
    }

    protected function saveDataset(array $dataset): void
    {
        $path = storage_path('ai/vark_dataset.json');
        file_put_contents($path, json_encode($dataset, JSON_PRETTY_PRINT));
    }

    protected function exportToCsv(array $dataset): void
    {
        $path = storage_path('ai/vark_dataset.csv');
        $file = fopen($path, 'w');
        
        fputcsv($file, ['V_Score', 'A_Score', 'R_Score', 'K_Score', 'Label']);
        
        foreach ($dataset['samples'] as $index => $sample) {
            $row = array_merge($sample, [$dataset['labels'][$index]]);
            fputcsv($file, $row);
        }
        
        fclose($file);
        $this->info("CSV exported to: {$path}");
    }
}
