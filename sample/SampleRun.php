<?php

use PHPANN\Config;
use PHPANN\Network;

class SampleRun
{
    private $configurationFilePath;
    
    public function __construct()
    {
        $this->configurationFilePath = '/tmp/phpann_sample.conf';
    }

    public function run(): void
    {
        $phpann = new Network([
            Config::CONFIGURATION_FILE_PATH => $this->configurationFilePath
        ]);

        $result = $phpann->run($this->normalize([1, 7]));
        
        print_r($result);
    }

    public function normalize(array $values): array
    {
        array_walk_recursive($values, function (&$value, $key) {
            $value /= 10;
        });

        return $values;
    }
}