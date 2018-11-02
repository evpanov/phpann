<?php

namespace PHPANN;

class Layer
{
    /** @var array */
    public static $layers = [];

    /**
     * @return int
     */
    public static function getLayerCount(): int
    {
        return Config::get(Config::LAYER_COUNT);
    }

    /**
     * @return int
     */
    public static function getInputLayerNeuronCount(): int
    {
        return Config::get(Config::INPUT_LAYER_NEURON_COUNT);
    }

    /**
     * @return int
     */
    public static function getHiddenLayerNeuronCount(): int
    {
        return Config::get(Config::HIDDEN_LAYER_NEURON_COUNT);
    }

    /**
     * @return int
     */
    public static function getOutputLayerNeuronCount(): int
    {
        return Config::get(Config::OUTPUT_LAYER_NEURON_COUNT);
    }

    /**
     * @param $layerIndex
     * @param $nextNeuronIndex
     * @return array
     */
    public static function create($layerIndex, $nextNeuronIndex): array
    {
        if (self::isInputLayer($layerIndex)) {
            return self::createNeurons($layerIndex, self::getInputLayerNeuronCount(), $nextNeuronIndex, true);
        }

        if (self::isOutputLayer($layerIndex)) {
            return self::createNeurons($layerIndex, self::getOutputLayerNeuronCount(), $nextNeuronIndex, false);
        }

        return self::createNeurons($layerIndex, self::getHiddenLayerNeuronCount(), $nextNeuronIndex, true);
    }

    /**
     * @param int  $layerIndex
     * @param int  $count
     * @param int  $startNeuronIndex
     * @param bool $needBias
     * @return array
     */
    public static function createNeurons(int $layerIndex, int $count, int $startNeuronIndex, bool $needBias): array
    {
        $neurons = array_combine(
            array_keys(array_fill($startNeuronIndex, $count, null)),
            array_fill(0, $count, Neuron::create($layerIndex, false))
        );

        if ($needBias === true) {
            $neurons += [$startNeuronIndex + $count => Neuron::create($layerIndex, true)];
        }

        foreach ($neurons as $neuronIndex => &$neuron) {
            Neuron::$neurons[$neuronIndex] = &$neuron;
        }

        return $neurons;
    }

    public static function setInputValues($inputValues): void
    {
        /** @var array $inputLayer */
        $inputLayer = Matrix::$matrix[self::getFirstLayerIndex()];

        foreach ($inputLayer as $neuronIndex => &$neuron) {
            if (Neuron::isBias($neuron) === true) {
                continue;
            }

            Neuron::setValue($neuron, $inputValues[$neuronIndex]);
        }
    }

    public static function getOutputValues(): array
    {
        /** @var array $outputLayer */
        $outputLayer = Matrix::$matrix[self::getLastLayerIndex()];

        $outputValues = [];

        foreach ($outputLayer as $neuronIndex => $neuron) {
            $outputValues[$neuronIndex] = Neuron::getValue($neuron);
        }
        
        return $outputValues;
    }

    private static function getFirstLayerIndex()
    {
        return array_values(\array_slice(self::$layers, 0, 1))[0];
    }

    private static function getLastLayerIndex()
    {
        return array_values(\array_slice(self::$layers, -1, 1))[0];
    }

    public static function isInputLayer(int $layerIndex): bool
    {
        return $layerIndex === self::getFirstLayerIndex();
    }

    public static function isOutputLayer(int $layerIndex): bool
    {
        return $layerIndex === self::getLastLayerIndex();
    }
}