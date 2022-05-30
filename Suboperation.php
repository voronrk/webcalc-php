<?php

namespace Suboperation;

include ('Worker.php');
use Worker\Worker;
use Data\getWorkers;

class Suboperation {

    public $title;                  // Название
    public $machine;                // Машина
    public $complexityIndex;        // Группа сложности
    public $unit;                   // Единица измерения
    public $standardHoursPerPiece;  // Норма времени (ед./ч.)
    public $wastePersent;           // % техотходов
    public $wasteNumber;            // количество техотходов
    public $workers;                // работники

    public $quantity;               // Количество

    public $elapsedTime;            // Затраченное время

    public $totalJobCost;           // ФОТ с налогами и коэффициентами

    public $primaryMaterials = [];  // основные материалы
    public $workerIDs = [];         // ID работников
    public $materials = [];         // Материалы

    private function CalculateElapsedTime() {
        $this->elapsedTime = $this->quantity * $this->standardHoursPerPiece;
    }

    private function CalculateTotalJobCost() {
        $this->totalJobCost = 0;
        foreach($this->workers as $worker) {
            $this->totalJobCost += $worker->jobTariff * $this->elapsedTime * 3;
        }
    }

    public function __construct($params, $quantity=0)
    {
        foreach($params as $key=>$value) {
            $this->$key = $value;
        };

        $this->quantity = $quantity;

        foreach($this->workerIDs as $worker) {
            $this->workers[]=new Worker(getWorkers::getById($worker));
        };

        $this->CalculateElapsedTime();
        $this->CalculateTotalJobCost();
    }
}