<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rubix\ML\Classifiers\GaussianNB;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

class TrainVarkModel extends Command
{
    protected $signature = 'ml:train 
                            {--force : Overwrite existing model}
                            {--from-dataset : Use generated dataset from ml:generate}';
    
    protected $description = 'Train the VARK learning style classification model';

    public function handle(): int
    {
        $modelPath = storage_path('ai/vark_model.rbx');

        if (file_exists($modelPath) && !$this->option('force')) {
            $this->error('Model already exists. Use --force to overwrite.');
            return self::FAILURE;
        }

        $this->ensureDirectoryExists();

        if ($this->option('from-dataset')) {
            [$samples, $labels] = $this->loadFromDataset();
        } else {
            $this->info('Generating default training data...');
            [$samples, $labels] = $this->generateDefaultData();
        }

        $this->info('Training Gaussian Naive Bayes model...');
        $this->info('Samples: ' . count($samples));
        $this->newLine();

        $dataset = new Labeled($samples, $labels);

        $estimator = new PersistentModel(
            new GaussianNB(),
            new Filesystem($modelPath)
        );

        $estimator->train($dataset);
        $estimator->save();

        $this->displayLabelDistribution($labels);

        $this->info('Model saved to: ' . $modelPath);
        $this->info('Run "php artisan ml:evaluate" to see performance metrics.');

        return self::SUCCESS;
    }

    protected function loadFromDataset(): array
    {
        $datasetPath = storage_path('ai/vark_dataset.json');

        if (!file_exists($datasetPath)) {
            $this->error('Dataset not found. Run "php artisan ml:generate" first.');
            exit(1);
        }

        $this->info('Loading dataset from: ' . $datasetPath);

        $data = json_decode(file_get_contents($datasetPath), true);
        
        return [$data['samples'], $data['labels']];
    }

    protected function displayLabelDistribution(array $labels): void
    {
        $this->info('=== TRAINING DATA DISTRIBUTION ===');
        $this->newLine();

        $counts = array_count_values($labels);
        arsort($counts);
        
        $total = count($labels);
        $rows = [];

        foreach ($counts as $label => $count) {
            $percentage = number_format(($count / $total) * 100, 1);
            $rows[] = [$label, $count, $percentage . '%'];
        }

        $this->table(['Label', 'Count', 'Percentage'], $rows);
        $this->newLine();
    }

    protected function ensureDirectoryExists(): void
    {
        $dir = storage_path('ai');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    protected function generateDefaultData(): array
    {
        $samples = [];
        $labels = [];

        $patterns = [
            'Visual' => [
                [12, 2, 1, 1], [10, 3, 2, 1], [11, 2, 2, 1], [9, 3, 2, 2],
                [13, 1, 1, 1], [10, 2, 2, 2], [11, 3, 1, 1], [12, 1, 2, 1],
            ],
            'Aural' => [
                [2, 12, 1, 1], [3, 10, 2, 1], [2, 11, 2, 1], [3, 9, 2, 2],
                [1, 13, 1, 1], [2, 10, 2, 2], [3, 11, 1, 1], [1, 12, 2, 1],
            ],
            'ReadWrite' => [
                [1, 2, 12, 1], [2, 3, 10, 1], [2, 2, 11, 1], [2, 3, 9, 2],
                [1, 1, 13, 1], [2, 2, 10, 2], [1, 3, 11, 1], [2, 1, 12, 1],
            ],
            'Kinesthetic' => [
                [1, 2, 1, 12], [1, 3, 2, 10], [2, 2, 1, 11], [2, 2, 3, 9],
                [1, 1, 1, 13], [2, 2, 2, 10], [1, 1, 3, 11], [1, 2, 1, 12],
            ],
        ];

        foreach ($patterns as $label => $patternSamples) {
            foreach ($patternSamples as $sample) {
                $samples[] = $sample;
                $labels[] = $label;

                for ($i = 0; $i < 5; $i++) {
                    $samples[] = $this->addNoise($sample);
                    $labels[] = $label;
                }
            }
        }

        return [$samples, $labels];
    }

    protected function addNoise(array $sample): array
    {
        $result = [];
        foreach ($sample as $value) {
            $noise = rand(-1, 1);
            $result[] = max(0, min(16, $value + $noise));
        }
        return $result;
    }
}
