<?php

namespace PHPANN;

class Matrix
{
    public static $matrix = [];

    /**
     * Create empty matrix
     */
    public static function create(): void
    {
        self::createLayers();
        self::createNeurons();
        self::createSynapses();
    }

    /**
     * 1. Create layers
     */
    private static function createLayers(): void
    {
        self::$matrix = array_fill(0, Layer::getLayerCount(), []);
        Layer::$layers = array_keys(self::$matrix);
    }

    /**
     * 2. Create neurons for each layer
     */
    private static function createNeurons(): void
    {
        $nextNeuronIndex = 0;

        foreach (self::$matrix as $layerIndex => &$layer) {
            $layer = Layer::create($layerIndex, $nextNeuronIndex);
            $nextNeuronIndex += \count($layer);
        }
    }

    /**
     * 3. Create synapses for each neuron
     */
    private static function createSynapses(): void
    {
        Neuron::createSynapses();
    }

    public static function calc(array $inputValues): array
    {
        Layer::setInputValues($inputValues);
        Neuron::calcValues();
        return Layer::getOutputValues();
    }

    public static function backpropagation(array $actualOutputValues, array $idealOutputValues): void
    {
        Synapse::backpropagation($actualOutputValues, $idealOutputValues);
    }
}