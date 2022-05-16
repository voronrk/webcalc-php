<?php

include('data.php');
include ('Material.php');

use Data\getJobTariffs;
use Data\getWorkers;
use Data\getSuboperations;
use Data\getPaperRejectRoll;
use Material\Material;

function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

class Paper extends Material {
    
    public function __construct($title='', $type='', $mainUnit='', $price=0, $usageRate=1, $currency='RUR') {
        parent::__construct($title='', $type='', $mainUnit='', $price=0, $usageRate=1, $currency='RUR');


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

    public function __construct($data, $workersData, $jobTariffs, $quantity=0)
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

class HalfProduct_DEPRECATED {

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

class Layout {

    public $printSides;
    public $rollsQuantity;
    public $pagesPerSide;
    public $pagesPerSheet;
    public $pagesQuantity;
    public $inksOnPages;
    public $inkMap;
    public $formsQuantity;    

    const PAGES_PER_SIDE = [
        'A4' => 8,
        'A3' => 4,
        'A2' => 2
    ];

    const PAGES_PER_SHEET = [
        'A4' => 16,
        'A3' => 8,
        'A2' => 4
    ];

    private function calculateRollsQuantity() {
        return $this->pagesQuantity / $this->pagesPerSheet;
    }

    private function calculatePrintSides() {
        return ceil($this->rollsQuantity) * 2;
    }

    private function calculateBlockSize() {
        return $this->pagesQuantity / ($this->pagesPerSide/2);
    }

    private function getCurrentBlockNumber($pageNumber) {
        return ceil($pageNumber / $this->calculateBlockSize());
    }

    private function calculateNumberOfPageInBlock($pageNumber) {
        return $pageNumber - ($this->calculateBlockSize() * ($this->getCurrentBlockNumber($pageNumber)-1));
    }

    private function getSideOfPage($pageNumber) {
        return $pageNumber <= 2*floor($this->rollsQuantity) ? $pageNumber : $this->calculateBlockSize() - $pageNumber + 1;
    }

    private function generateInkMap() {
        foreach($this->inksOnPages as $key=>$ink) {
            $page = $key+1;
            $side = $this->getSideOfPage($this->calculateNumberOfPageInBlock($page));
            $this->inkMap[$side] = $ink > $this->inkMap[$side] ? $ink : $this->inkMap[$side];
        }
    }

    private function calculateFormQuantity() {
        $formsQuantity = 0;
        foreach($this->inkMap as $quantity) {
            $formsQuantity += $quantity;
        };
        return $formsQuantity;
    }

    public function __construct($pagesQuantity, $sizeOfPage, $inksOnPages) {
        $this->pagesQuantity = $pagesQuantity; 
        $this->inksOnPages = $inksOnPages;
        $this->pagesPerSide = self::PAGES_PER_SIDE[$sizeOfPage];
        $this->pagesPerSheet = self::PAGES_PER_SHEET[$sizeOfPage];
        $this->rollsQuantity = $this->calculateRollsQuantity();
        $this->printSides = $this->calculatePrintSides();
        $this->generateInkMap();
        $this->formsQuantity = $this->calculateFormQuantity();
    }
}

class HalfProduct {

    public $pagesQuantity;
    public $sizeOfPage;
    public $inksOnPages;
    public $paper;
    public $quantity;

    private function calculateQuantity($type) {
        if ($type == 'preparation') {
            return 7;
        };
        if ($type == 'passing') {
            return $this->quantity;
        };
        return false;
    }

    public function __construct($config, $quantity=0, $pagesQuantity=0, $sizeOfPage='', $inksOnPages=[], $paper='') {

        $this->pagesQuantity = $pagesQuantity;
        $this->sizeOfPage = $sizeOfPage;
        $this->inksOnPages = $inksOnPages;
        $this->paper = $paper;
        $this->quantity = $quantity;

        $this->layout = new Layout($this->pagesQuantity, $this->sizeOfPage, $this->inksOnPages);

        $workersData = getWorkers::index($config);
        $suboperationsData = getSuboperations::index($config);

        foreach($suboperationsData as $key=>$suboperationData) {
            $this->suboperations[]=new Suboperation($suboperationData, $workersData, getJobTariffs::index(), $this->calculateQuantity($suboperationData['type']));
        }
    }
}

const NUMBERS = [7, 2283];
const QUANTITY = 2283;
// const PAGES = 12;
const SIZE = 'A3';
const INK = '4+1';

$config = "Newspaper Block";
$inkOnPages = [2,1,1,1,1,1,1,2,2,1,1,1,1,1,1,2];
// $inkOnPages = [4,1,1,1,1,1,1,4,4,1,1,1,1,1,1,4,1,1,1,1,1,1,1,1];
// $inkOnPages = [4,1,1,1,1,1,1,1,1,1,1,4];
$paper = new Paper();

$newspaperBlock = new HalfProduct($config, QUANTITY, count($inkOnPages), SIZE, $inkOnPages, $paper);
debug($newspaperBlock);





// debug(getPaperRejectRoll::index(2, QUANTITY, INK));


?>