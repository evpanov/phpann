<?php

namespace PHPANN;

class Config
{
    public const LAYER_COUNT = 10;
    public const INPUT_LAYER_NEURON_COUNT = 11;
    public const HIDDEN_LAYER_NEURON_COUNT = 12;
    public const OUTPUT_LAYER_NEURON_COUNT = 13;
    public const STEEPNESS = 14;

    public const EPOCH_COUNT = 20;
    public const BETWEEN_REPORT_EPOCH_COUNT = 21;
    public const LEARNING_RATE = 22;
    public const LEARNING_MOMENTUM = 23;
    public const DESIRED_ERROR = 24;

    public const CONFIGURATION_FILE_PATH = 90;
    public const DEBUG_FILE_PATH = 91;

    public const OUTPUT_SYNAPSES = 99;

    private static $params = [];

    private static $saveParamList = [
        self::LAYER_COUNT,
        self::INPUT_LAYER_NEURON_COUNT,
        self::HIDDEN_LAYER_NEURON_COUNT,
        self::OUTPUT_LAYER_NEURON_COUNT,
        self::OUTPUT_SYNAPSES,
        self::STEEPNESS,
    ];

    public static function setMany(array $params): void
    {
        foreach ($params as $param => $value) {
            self::set($param, $value);
        }
    }

    public static function set(int $param, $value): void
    {
        self::$params[$param] = $value;
    }

    public static function get(?int $param = null)
    {
        return $param === null ? self::$params : (self::$params[$param] ?? null);
    }

    public static function saveToFile(): void
    {
        $params = array_intersect_key(self::get(), array_flip(self::$saveParamList));

        file_put_contents(self::get(self::CONFIGURATION_FILE_PATH), json_encode($params));
    }

    public static function loadFromFile(): void
    {
        $params = json_decode(file_get_contents(self::get(self::CONFIGURATION_FILE_PATH)), true);
        
        self::setMany($params);
    }
}