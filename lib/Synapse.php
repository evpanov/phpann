<?php

namespace PHPANN;

class Synapse
{
    public static $outputSynapses = [];
    public static $inputSynapses = [];

    public static function create($fromNeuronIndex, $toNeuronIndex): void
    {
        isset(self::$outputSynapses[$fromNeuronIndex]) || self::$outputSynapses[$fromNeuronIndex] = [];
        self::$outputSynapses[$fromNeuronIndex][$toNeuronIndex] = Math::randomFloat();

        isset(self::$inputSynapses[$toNeuronIndex]) || self::$inputSynapses[$toNeuronIndex] = [];
        self::$inputSynapses[$toNeuronIndex][$fromNeuronIndex] = &self::$outputSynapses[$fromNeuronIndex][$toNeuronIndex];
    }

    public static function backpropagation(array $actualOutputValues, array $idealOutputValues)
    {
        $idealOutputValues = array_combine(
            array_keys($actualOutputValues),
            $idealOutputValues
        );

        $neuronWeightDeltas = [];

        /**
         * @var int   $toNeuronIndex
         * @var array $fromNeurons
         */
        foreach (self::$inputSynapses as $toNeuronIndex => $fromNeurons) {
            $toNeuron = &Neuron::$neurons[$toNeuronIndex];

            if (Neuron::isInOutputLayer($toNeuron) === true) {
                $neuronWeightDeltas[$toNeuronIndex] = Math::outputLayerNeuronWeightDelta(
                    $actualOutputValues[$toNeuronIndex],
                    $idealOutputValues[$toNeuronIndex]
                );
            }

            if (\is_array($fromNeurons) === false) {
                continue;
            }

            foreach ($fromNeurons as $fromNeuronIndex => $synapseWeight) {
                $fromNeuron = &Neuron::$neurons[$fromNeuronIndex];

                if (Neuron::isInInputLayer($fromNeuron) === false) {
                    $neuronWeightDeltas[$fromNeuronIndex] = Math::hiddenLayerNeuronWeightDelta(
                        $synapseWeight,
                        $neuronWeightDeltas[$toNeuronIndex],
                        Neuron::getValue($fromNeuron)
                    );
                }

                $synapseWeightVariation = Math::synapseWeightVariation(
                    Neuron::getValue($fromNeuron),
                    $neuronWeightDeltas[$toNeuronIndex],
                    Neuron::getPreviousDelta($fromNeuron)
                );

                self::$inputSynapses[$toNeuronIndex][$fromNeuronIndex] = $synapseWeight + $synapseWeightVariation;

                if (Neuron::isInInputLayer($fromNeuron) === false) {
                    Neuron::setPreviousDelta($fromNeuron, $synapseWeightVariation);
                }
            }
        }
    }

    public static function store(): void
    {
        Config::set(Config::OUTPUT_SYNAPSES, self::$outputSynapses);
    }

    public static function load(): void
    {
        $outputSynapses = Config::get(Config::OUTPUT_SYNAPSES);

        /**
         * @var int $fromNeuronIndex
         * @var array $toNeurons
         */
        foreach ($outputSynapses as $fromNeuronIndex => $toNeurons) {
            foreach ($toNeurons as $toNeuronIndex => $weight) {
                self::$outputSynapses[$fromNeuronIndex][$toNeuronIndex] = $weight;
            }
        }
    }
}