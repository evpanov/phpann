<?php

use PHPANN\Config;
use PHPANN\Network;

class SampleTest
{
    private $configurationFilePath;
    
    public function __construct()
    {
        $this->configurationFilePath = '/tmp/phpann_sample.conf';
    }

    public function test(): void
    {
        $phpann = new Network([
            Config::CONFIGURATION_FILE_PATH => $this->configurationFilePath
        ]);

        $result = $phpann->test($this->normalize($this->getTrainData()));
        
        print_r($result);
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