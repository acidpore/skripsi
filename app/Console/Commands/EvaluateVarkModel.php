<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rubix\ML\Classifiers\GaussianNB;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;
use Rubix\ML\CrossValidation\Reports\MulticlassBreakdown;
use Rubix\ML\Datasets\Labeled;

class EvaluateVarkModel extends Command
{
    protected $signature = 'ml:evaluate 
                            {--split=0.2 : Test set ratio}
                            {--from-dataset : Use generated dataset from ml:generate}';
    
    protected $description = 'Evaluate VARK model with Train/Test Split and Confusion Matrix';

    public function handle(): int
    {
        if ($this->option('from-dataset')) {
            [$samples, $labels] = $this->loadFromDataset();
        } else {
            $this->info('Using default evaluation data...');
            [$samples, $labels] = $this->generateDefaultData();
        }

        $dataset = Labeled::build($samples, $labels)->randomize();

        $split = (float) $this->option('split');
        $testCount = (int) floor($dataset->numSamples() * $split);

        $testing = $dataset->take($testCount);
        $training = $dataset;

        $this->info('Training samples: ' . $training->numSamples());
        $this->info('Testing samples: ' . $testing->numSamples());
        $this->newLine();

        $estimator = new GaussianNB();
        $estimator->train($training);

        $predictions = $estimator->predict($testing);
        $actuals = $testing->labels();

        $this->displayConfusionMatrix($actuals, $predictions);
        $this->displayMetrics($actuals, $predictions);

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
        $this->newLine();

        $data = json_decode(file_get_contents($datasetPath), true);
        
        return [$data['samples'], $data['labels']];
    }

    protected function displayConfusionMatrix(array $labels, array $predictions): void
    {
        $this->info('=== CONFUSION MATRIX ===');
        $this->newLine();

        $report = new ConfusionMatrix();
        $result = $report->generate($predictions, $labels);

        $classes = array_unique(array_merge($labels, $predictions));
        sort($classes);

        $header = array_merge(['Actual \\ Predicted'], $classes);
        $rows = [];

        foreach ($classes as $actual) {
            $row = [$actual];
            foreach ($classes as $predicted) {
                $row[] = $result[$actual][$predicted] ?? 0;
            }
            $rows[] = $row;
        }

        $this->table($header, $rows);
        $this->newLine();
    }

    protected function displayMetrics(array $labels, array $predictions): void
    {
        $this->info('=== CLASSIFICATION METRICS ===');
        $this->newLine();

        $report = new MulticlassBreakdown();
        $result = $report->generate($predictions, $labels);

        $overall = $result['overall'];

        $this->table(
            ['Metric', 'Value'],
            [
                ['Accuracy', number_format($overall['accuracy'] * 100, 2) . '%'],
                ['Precision (Macro)', number_format($overall['precision'] * 100, 2) . '%'],
                ['Recall (Macro)', number_format($overall['recall'] * 100, 2) . '%'],
                ['F1-Score (Macro)', number_format($overall['f1 score'] * 100, 2) . '%'],
            ]
        );

        $this->newLine();
        $this->info('=== PER-CLASS METRICS ===');
        $this->newLine();

        $classes = array_keys($result['classes']);
        sort($classes);
        $rows = [];

        foreach ($classes as $class) {
            $metrics = $result['classes'][$class];
            $rows[] = [
                $class,
                number_format($metrics['precision'] * 100, 2) . '%',
                number_format($metrics['recall'] * 100, 2) . '%',
                number_format($metrics['f1 score'] * 100, 2) . '%',
            ];
        }

        $this->table(['Class', 'Precision', 'Recall', 'F1-Score'], $rows);

        $this->newLine();
        $this->info('Note: Copy these metrics to Bab 4 of your thesis.');
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

                for ($i = 0; $i < 4; $i++) {
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
