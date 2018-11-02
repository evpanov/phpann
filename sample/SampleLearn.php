<?php

use PHPANN\Config;
use PHPANN\Network;

class SampleLearn
{
    private $configurationFilePath;
    
    public function __construct()
    {
        $this->configurationFilePath = '/tmp/phpann_sample.conf';
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function learn(): void
    {
        $phpann = new Network([
            Config::LAYER_COUNT => 4,
            Config::INPUT_LAYER_NEURON_COUNT => 2,
            Config::HIDDEN_LAYER_NEURON_COUNT => 5,
            Config::OUTPUT_LAYER_NEURON_COUNT => 1,
            Config::STEEPNESS => 1,

            Config::EPOCH_COUNT => 10000,
            Config::BETWEEN_REPORT_EPOCH_COUNT => 1000,
            Config::LEARNING_RATE => 0.1,
            Config::LEARNING_MOMENTUM => 0.1,
            Config::DESIRED_ERROR => 0.0001,

            Config::CONFIGURATION_FILE_PATH => $this->configurationFilePath
        ]);

        $phpann->train($this->normalize($this->getTrainData()));
    }

    private function getTrainData(): array
    {
        return [
            [[0, 0], [0]],
            [[0, 1], [0]],
            [[0, 2], [0]],
            [[0, 3], [0]],
            [[0, 4], [0]],
            [[0, 5], [0]],
            [[0, 6], [0]],
            [[0, 7], [0]],
            [[0, 8], [0]],
            [[0, 9], [0]],
            [[1, 0], [0]],
            [[1, 1], [1]],
            [[1, 2], [2]],
            [[1, 3], [3]],
            [[1, 4], [4]],
            [[1, 5], [5]],
            [[1, 6], [6]],
            [[1, 7], [7]],
            [[1, 8], [8]],
            [[1, 9], [9]],
        ];
    }

    public function normalize(array $values): array
    {
        array_walk_recursive($values, function (&$value, $key) {
            $value /= 10;
        });

        return $values;
    }
}