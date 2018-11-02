<?php

namespace PHPANN;

class Math
{
    public static function randomFloat(): float
    {
        return mt_rand() / mt_getrandmax();
    }

    public static function sigmoid(float $value): float
    {
        $steepness = Config::get(Config::STEEPNESS);

        return 1 / (1 + exp(-$value * $steepness));
    }

    /**
     * Общая дельта веса для всех входящих в нейрон последнего слоя
     *
     * @param $actualOutputValue
     * @param $idealOutputValue
     * @return float
     */
    public static function outputLayerNeuronWeightDelta($actualOutputValue, $idealOutputValue): float
    {
        return ($idealOutputValue - $actualOutputValue) * ($actualOutputValue * (1 - $actualOutputValue));
    }

    /**
     * Общая дельта веса для всех входящих в нейрон скрытого слоя
     *
     * @param $actualWeight
     * @param $weightDelta
     * @param $actualValue
     * @return float
     */
    public static function hiddenLayerNeuronWeightDelta($actualWeight, $weightDelta, $actualValue): float
    {
        return ($actualWeight * $weightDelta) * ($actualValue * (1 - $actualValue));
    }

    public static function synapseWeightVariation($actualValue, $weightDelta, $previousWeightDelta): float
    {
        $learningRate = Config::get(Config::LEARNING_RATE);
        $learningMomentum = Config::get(Config::LEARNING_MOMENTUM);

        $gradient = $actualValue * $weightDelta;

        return ($gradient * $learningRate) + ($previousWeightDelta * $learningMomentum);
    }

    public static function calcMSE(array $actualOutputValues, array $idealOutputValues): float
    {
        $idealOutputValues = array_combine(
            array_keys($actualOutputValues),
            $idealOutputValues
        );

        $MSE = [];

        foreach ($actualOutputValues as $actualOutputValueIndex => $actualOutputValue) {
            $MSE[] = ($idealOutputValues[$actualOutputValueIndex] - $actualOutputValue) ** 2;
        }

        return array_sum($MSE) / \count($MSE);
    }
}