<?php

include('data.php');
include ('Material.php');

use Data\getJobTariffs;
use Data\getWorkers;
use Data\getSuboperations;
use Data\getPaperRejectRoll;
use Data\getInks;
use Material\Material;

function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

class Paper extends Material {

    public $rollWidth;
    public $basicWeight;
    public $rejectNorma;
    public $rollsQuantity;

    const CUT_LENGTH = 57.8;

    public function calculateSheetSquare() {
        return self::CUT_LENGTH * $this->rollWidth / 10000; // square meter
    }

    public function calculateTotalSquare() {
        return $this->rollsQuantity * $this->quantityOfItems * $this->calculateSheetSquare(); // square meter
    }

    public function calculateWeight() {
        return $this->calculateTotalSquare() * $this->basicWeight / 1000; //kg
    }

    public function calculateTotalPaperWeight() {
        return $this->calculateWeight() * (1 + $this->rejectNorma / 100);
    }

    public function __construct($params) {
        parent::__construct($params);
        $this->rollWidth = $params['rollWidth'];
        $this->basicWeight = $params['basicWeight'];
        $this->rollsQuantity = $params['rollsQuantity'];
        $this->quantityOfItems = $params['quantityOfItem'];
        $this->rejectNorma = getPaperRejectRoll::index($this->rollsQuantity, $this->quantityOfItems, $params['layoutInkMap']);
        $this->quantity = $this->calculateTotalPaperWeight();
        $this->totalCost = $this->calculateTotalCost();
    }
}

class Ink extends Material {

    public function calculateQuantity() {
        
    }
    
    public function __construct($params) {
        parent::__construct($params);
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
    public $layoutInkMap;
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

    private function generateLayoutInkMap() {
        $inks = ['','','1+1','2+1','2+2','4+1','4+2','','4+4'];
        $sumOfInks = 0;
        for ($i=1;$i<count($this->inkMap);$i=$i+2) {
            $temp = count($this->inkMap[$i])+count($this->inkMap[$i+1]);
            if ($sumOfInks<$temp) {
                $sumOfInks = $temp;
            };
        };
        $this->layoutInkMap = $inks[$sumOfInks];
    }

    private function addInkToSide($inkID, $side) {
        if ($this->inkMap[$side]=='') {
            $this->inkMap[$side] = [];
        };
        if (!in_array($inkID, $this->inkMap[$side])) {
            $this->inkMap[$side] = array_merge($this->inkMap[$side], [$inkID]);
        };
    }

    private function generateInkMap() {
        foreach($this->inksOnPages as $key=>$inks) {
            $page = $key+1;
            $side = $this->getSideOfPage($this->calculateNumberOfPageInBlock($page));
            foreach($inks as $inkID) {
                $this->addInkToSide($inkID, $side);
            };            
        }
        $this->generateLayoutInkMap();
    }

    private function calculateFormQuantity() {
        $formsQuantity = 0;
        foreach($this->inkMap as $quantity) {
            $formsQuantity += count($quantity);
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
    public $paper;
    public $quantity;
    public $formsQuantity;
    public $inks;

    private function calculateQuantity($type) {
        if ($type == 'preparation') {
            return $this->formsQuantity;
        };
        if ($type == 'passing') {
            return $this->quantity;
        };
        return false;
    }

    public function __construct($params, $paperParams, $inkGroup) {

        $this->sizeOfPage = $params['sizeOfPage'];
        $this->pagesQuantity = count($params['inksOnPages']);
        $this->quantity = $params['quantity'];

        $this->layout = new Layout($this->pagesQuantity, $this->sizeOfPage, $params['inksOnPages']);
        $this->formsQuantity = $this->layout->formsQuantity;

        $paperParams = array_merge($paperParams, [
            'rollsQuantity' => $this->layout->rollsQuantity,
            'quantityOfItem' => $this->quantity,
            'layoutInkMap' => $this->layout->layoutInkMap
        ]);
        $this->paper = new Paper($paperParams);

        foreach($this->layout->inkMap as $sideInk) {
            foreach($sideInk as $inkID) {
                $this->inks[] = new Ink(getInks::get($inkID));
            }
        }

        $workersData = getWorkers::index($config);
        $suboperationsData = getSuboperations::index($config);

        foreach($suboperationsData as $key=>$suboperationData) {
            $this->suboperations[]=new Suboperation($suboperationData, $workersData, getJobTariffs::index(), $this->calculateQuantity($suboperationData['type']));
        }
    }
}

// const QUANTITY = 25000;
const QUANTITY = 2283;
// const QUANTITY = 2500;
const SIZE = 'A3';
const ROLL_WIDTH = 76;

const PAPER_DATA = [
    'group' => 'БУМАГА',
    'title' => 'Газетная',
    'type' => 'газетная',
    'mainUnit' => 'кг',
    'price' => 32.25,
    'usageRate' => 1,
    'basicWeight' => 42,
    'currency' => 'RUR',
    'rollWidth' => ROLL_WIDTH
];

const INK_DATA = [
    [
        'group' => 'КРАСКА',
        'title' => 'Краска ролевая чёрная',
        'type' => 'ролевая',
        'mainUnit' => 'кг',
        'price' => 1.62,
        'currency' => 'EUR',
    ],
    [
        'group' => 'КРАСКА',
        'title' => 'Краска ролевая голубая',
        'type' => 'ролевая',
        'mainUnit' => 'кг',
        'price' => 2.32,
        'currency' => 'EUR',
    ],
    [
        'group' => 'КРАСКА',
        'title' => 'Краска ролевая желтая',
        'type' => 'ролевая',
        'mainUnit' => 'кг',
        'price' => 2.32,
        'currency' => 'EUR',
    ],
    [
        'group' => 'КРАСКА',
        'title' => 'Краска ролевая пурпурная',
        'type' => 'ролевая',
        'mainUnit' => 'кг',
        'price' => 2.32,
        'currency' => 'EUR',
    ],
];

$configName = "Newspaper Block";
// $inkOnPages = [1,1,1,1,1,1,1,1];                                        // 8
// $inksOnPages = [4,1,1,1,1,1,1,4,4,1,1,1,1,1,1,4];                     // 16
// $inkOnPages = [4,1,1,1,1,1,1,4,4,1,1,1,1,1,1,4,1,1,1,1,1,1,1,1];     // 24
// $inkOnPages = [4,1,1,1,1,1,1,1,1,1,1,4];                             // 12

$inksOnPages = [
    [1,2,3,4],
    [1],
    [1],
    [1],
    [1],
    [1],
    [1],
    [1,2,3,4],
    [1,2,3,4],
    [1],
    [1],
    [1],
    [1],
    [1],
    [1],
    [1,2,3,4],
];
// $inksOnPages = [
//     [1,2,3,4],
//     [1],
//     [1],
//     [1],
//     [1],
//     [1],
//     [1],
//     [1,2,3,4],
//     [1,2,3,4],
//     [1],
//     [1],
//     [1],
//     [1],
//     [1],
//     [1],
//     [1,2,3,4],
// ];

$halfProductParams = [
    'configName' => $configName,
    'quantity' => QUANTITY,
    'sizeOfPage' => SIZE,
    'inksOnPages' => $inksOnPages,
];

$inkGroup = 4;

$newspaperBlock = new HalfProduct($halfProductParams, PAPER_DATA, $inkGroup);
debug($newspaperBlock);

?>