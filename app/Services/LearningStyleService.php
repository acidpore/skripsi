<?php

namespace App\Services;

use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Exception;

class LearningStyleService
{
    protected ?PersistentModel $model = null;
    protected string $modelPath;

    public function __construct()
    {
        $this->modelPath = storage_path('ai/vark_model.rbx');
    }

    public function predict(array $answers): string
    {
        $this->loadModel();

        if (!$this->model) {
            return $this->fallbackPrediction($answers);
        }

        $sample = $this->processAnswers($answers);
        $dataset = new Unlabeled([$sample]);
        $predictions = $this->model->predict($dataset);

        return $predictions[0];
    }

    public function processAnswers(array $answers): array
    {
        $vCount = 0;
        $aCount = 0;
        $rCount = 0;
        $kCount = 0;

        foreach ($answers as $answer) {
            match ($answer) {
                'a' => $aCount++,
                'b' => $vCount++,
                'c' => $rCount++,
                'd' => $kCount++,
                default => null,
            };
        }

        return [$vCount, $aCount, $rCount, $kCount];
    }

    public function fallbackPrediction(array $answers): string
    {
        $scores = $this->processAnswers($answers);
        $labels = ['Visual', 'Aural', 'ReadWrite', 'Kinesthetic'];
        $maxIndex = array_search(max($scores), $scores);

        return $labels[$maxIndex];
    }

    protected function loadModel(): void
    {
        if ($this->model !== null) {
            return;
        }

        if (!file_exists($this->modelPath)) {
            return;
        }

        try {
            $this->model = PersistentModel::load(new Filesystem($this->modelPath));
        } catch (Exception $e) {
            $this->model = null;
        }
    }

    public function isModelAvailable(): bool
    {
        return file_exists($this->modelPath);
    }
}
