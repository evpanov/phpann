<?php

namespace PHPANN;

class Neuron
{
    public const FIELD_LAYER_INDEX = 0;
    public const FIELD_IS_BIAS = 1;
    public const FIELD_SIGMOID_VALUE = 2;
    public const FIELD_INPUT_SYNAPSES = 3;
    public const FIELD_OUTPUT_SYNAPSES = 4;
    public const FIELD_PREVIOUS_WEIGHT_DELTA = 5;

    public const BIAS_VALUE = 1;

    public static $neurons = [];

    public static function create(int $layerIndex, bool $isBias): array
    {
        $neuron = [
            self::FIELD_LAYER_INDEX => $layerIndex,
            self::FIELD_IS_BIAS => $isBias,
            self::FIELD_SIGMOID_VALUE => $isBias ? self::BIAS_VALUE : null,
            self::FIELD_INPUT_SYNAPSES => null,
            self::FIELD_OUTPUT_SYNAPSES => null,
            self::FIELD_PREVIOUS_WEIGHT_DELTA => 0,
        ];

        return $neuron;
    }

    public static function createSynapses(): void
    {
        /**
         * @var int   $neuronIndex
         * @var array $neuron
         */
        foreach (self::$neurons as $neuronIndex => &$neuron) {
            if (self::isInOutputLayer($neuron) === true) {
                $neuron[self::FIELD_INPUT_SYNAPSES] = &Synapse::$inputSynapses[$neuronIndex];
                continue;
            }

            \is_array($neuron[self::FIELD_OUTPUT_SYNAPSES]) || $neuron[self::FIELD_OUTPUT_SYNAPSES] = [];

            /** @var array $nextLayer */
            $nextLayer = Matrix::$matrix[$neuron[self::FIELD_LAYER_INDEX] + 1];

            foreach ($nextLayer as $nextLayerNeuronIndex => $nextLayerNeuron) {
                if (self::isBias($nextLayerNeuron) === true) {
                    continue;
                }

                Synapse::create($neuronIndex, $nextLayerNeuronIndex);
            }

            /**
             * Здесь странность - добавляется пустой элемент в массив Synapse::$inputSynapses
             */
            $neuron[self::FIELD_INPUT_SYNAPSES] = &Synapse::$inputSynapses[$neuronIndex];
            $neuron[self::FIELD_OUTPUT_SYNAPSES] = &Synapse::$outputSynapses[$neuronIndex];
        }

        krsort(Synapse::$inputSynapses);
        ksort(Synapse::$outputSynapses);
    }

    public static function calcValues(): void
    {
        foreach (self::$neurons as $neuronIndex => &$neuron) {
            if (self::isInInputLayer($neuron) === true || self::isBias($neuron) === true) {
                continue;
            }

            /** @var array $inputSynapses */
            $inputSynapses = $neuron[self::FIELD_INPUT_SYNAPSES];

            $value = 0;

            /**
             * @var int   $fromNeuronIndex
             * @var float $synapseWeight
             */
            foreach ($inputSynapses as $fromNeuronIndex => $synapseWeight) {
                $fromNeuronValue = self::getValue(self::$neurons[$fromNeuronIndex]);
                $value += $fromNeuronValue * $synapseWeight;
            }

            self::setValue($neuron, Math::sigmoid($value));
        }
    }

    public static function setValue(array &$neuron, float $value): void
    {
        $neuron[self::FIELD_SIGMOID_VALUE] = $value;
    }

    public static function getValue(array $neuron): ?float
    {
        return $neuron[self::FIELD_SIGMOID_VALUE];
    }

    public static function setPreviousDelta(&$neuron, float $value): void
    {
        $neuron[self::FIELD_PREVIOUS_WEIGHT_DELTA] = $value;
    }

    public static function getPreviousDelta(array $neuron): ?float
    {
        return $neuron[self::FIELD_PREVIOUS_WEIGHT_DELTA];
    }

    public static function isBias(array $neuron): bool
    {
        return $neuron[self::FIELD_IS_BIAS] === true;
    }

    public static function isInInputLayer(array $neuron): bool
    {
        return Layer::isInputLayer($neuron[self::FIELD_LAYER_INDEX]);
    }

    public static function isInOutputLayer(array $neuron): bool
    {
        return Layer::isOutputLayer($neuron[self::FIELD_LAYER_INDEX]);
    }

}