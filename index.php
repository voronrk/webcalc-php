<?php

include('data.php');
use Data\getJobTariffs;
use Data\getWorkers;
use Data\getSuboperations;
use Data\getPaperRejectRoll;

function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";

}

/*
*   Формат: А3
*   Страниц: 16
*   4+1 (1, 8, 9, 16)
*   2283 экз.
*/

const NUMBERS = [7, 2283];

class Material {

    public $title;          // Название
    public $type;           // Тип
    public $main_unit;      // Единица измерения основная
    public $price;          // Цена за единицу (основную)
    public $usage_rate;     // Норма расхода
    public $currency;       // Валюта цены

    public $count;          // Количество

    public function __construct($title='', $type='', $mainUnit='', $price=0, $usageRate=1, $currency='RUR')
    {
        $this->title = $title;
        $this->type = $type;
        $this->main_unit = $main_unit;
        $this->price = $price;
        $this->usage_rate = $usage_rate;
        $this->currency = $currency;
    }

    private function unit_recalc($unit_from, $unit_to) {

    }

}

class Worker {

    public $name;       // ФИО
    public $position;   // Должность
    public $grade;      // Разряд

    public function __construct($data)
    {
        $this->grade = $data['grade'];
        $this->position = $data['position'];
        $this->name = isset($data['name']) ? $data['name'] : '';
    }

}

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

    private function CalculateElapsedTime() {
        $this->elapsedTime = $this->quantity * $this->standardHoursPerPiece;
    }

    private function CalculateTotalJobCost() {
        $this->totalJobCost = 0;
        foreach($this->workers as $worker) {
            $this->totalJobCost += $this->jobTariffs[$worker->grade] * $this->elapsedTime * 3;
        }
    }

    public function __construct($data, $workersData, $quantity=0, $jobTariffs)
    {
        $this->title = $data['title'];
        $this->machine = $data['machine'];
        $this->complexityIndex = $data['complexityIndex'];
        $this->unit = $data['unit'];
        $this->standardHoursPerPiece = $data['standardHoursPerPiece'];
        $this->wastePersent = $data['wastePersent'];
        $this->wasteNumber = $data['wasteNumber'];

        $this->jobTariffs = $jobTariffs;

        $this->quantity = $quantity;

        foreach($workersData as $worker) {
            $this->workers[]=new Worker($worker);
        };
        $this->CalculateElapsedTime();
        $this->CalculateTotalJobCost();
    }
}

class Product {

    public $suboperations;
    public $materials;

    public $quantity;

    public function __construct($quantity=0, $suboperations = [], $materials = [])
    {
        $this->suboperations = $suboperations;

        foreach($this->suboperations as $suboperation) {
            $this->quantity += $suboperation->quantity;
        }
    }

    public function render()
    {
        
    }

}

$jobTariffs = getJobTariffs::index();

$workersData = getWorkers::index();

$suboperationsData = getSuboperations::index();

$suboperations = [];
foreach($suboperationsData as $key=>$suboperationData) {
    $suboperations[]=new Suboperation($suboperationData, $workersData, NUMBERS[$key], $jobTariffs);
}
// debug($suboperations);

debug(getPaperRejectRoll::index(2, 2283, '2+1'));
// debug(getPaperRejectRoll::index(2, 120000, '2+1'));

// $operation = new Product($quantity, $suboperations=[], $materials);

?>