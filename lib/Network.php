<?php

namespace PHPANN;

class Network
{
    private $startTimestamp;

    public function __construct(array $config)
    {
        $this->startTimestamp = microtime(true);
        Config::setMany($config);
    }

    public function getSpentTimeSecond()
    {
        return microtime(true) - $this->startTimestamp;
    }

    public function train(array $trainData): void
    {
        $this->create();
        
        $epochCount = Config::get(Config::EPOCH_COUNT);
        $betweenReportEpochCount = Config::get(Config::BETWEEN_REPORT_EPOCH_COUNT);

        for ($epochIndex = 1; $epochIndex <= $epochCount; $epochIndex++) {
            /**
             * @var array $inputValues
             * @var array $idealOutputValues
             */
            foreach ($trainData as [$inputValues, $idealOutputValues]) {
                $actualOutputValues = Matrix::calc($inputValues);

                $MSE = Math::calcMSE($actualOutputValues, $idealOutputValues);

                if ($MSE <= Config::get(Config::DESIRED_ERROR)) {
                    continue;
                }

                Matrix::backpropagation($actualOutputValues, $idealOutputValues);
            }

            if ($epochIndex % $betweenReportEpochCount === 0) {
                $spentTimeSecond = $this->getSpentTimeSecond();
                print_r("epoch:{$epochIndex}; error: {$MSE}; time: {$spentTimeSecond}" . PHP_EOL);
            }
        }

        $this->save();
    }

    public function test(array $testData): array
    {
        $this->load();
        
        $result = [];
        
        /**
         * @var array $inputValues
         * @var array $idealOutputValues
         */
        foreach ($testData as $testDataIndex => [$inputValues, $idealOutputValues]) {
            $actualOutputValues = Matrix::calc($inputValues);

            $result[$testDataIndex] = [
                'inputValues' => $inputValues,
                'idealOutputValues' => $idealOutputValues,
                'actualOutputValues' => $actualOutputValues
            ];
        }
        
        return $result;
    }

    public function run($inputValues): array
    {
        $this->load();

        return Matrix::calc($inputValues);
    }

    public function create(): void
    {
        Matrix::create();
    }

    public function save(): void
    {
        Synapse::store();
        Config::saveToFile();
    }

    public function load(): void
    {
        Config::loadFromFile();
        Matrix::create();
        Synapse::load();
    }

}