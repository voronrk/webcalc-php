<?php

include('data.php');
include ('Material.php');
include ('Suboperation.php');
include ('Layout.php');


use Data\getSuboperations;
use Data\getPaperRejectRoll;
use Data\getInks;
use Data\getInkRollNorma;
use Data\getPaper;
use Data\getForms;
use Material\Material;
use Suboperation\Suboperation;
use Layout\Layout;


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

        $this->rejectNorma = getPaperRejectRoll::index($this->rollsQuantity, $this->quantityOfItems, $params['layoutInkMap']);
        $this->quantity = $this->calculateTotalPaperWeight();
        $this->totalCost = $this->calculateTotalCost();
    }
}

class Ink extends Material {

    public function calculateQuantity($S) {
        $this->quantity = $S * $this->usageRate;
    }

    public function __construct($params) {
        parent::__construct($params);
        if ($params['title']=='Краска ролевая чёрная') {
            $params['inkGroup'] = 2;
        };
        $this->usageRate = getInkRollNorma::getNorma($params['inkGroup']);
    }
}

class Form extends Material {
    public function __construct($params) {
        parent::__construct($params);
    }
}

class HalfProduct {

    public $pagesQuantity;
    public $sizeOfPage;
    public $paper;
    public $quantity;
    public $formsQuantity;
    public $inks;
    public $inksTotalCost=0;

    public $formsTotalCost;

    public $totalCost=0;

    private function calculateQuantity($type) {
        if ($type == 'preparation') {
            return $this->formsQuantity;
        };
        if ($type == 'passing') {
            return $this->quantity;
        };
        return false;
    }

    public function __construct($params) {

        $this->sizeOfPage = $params['sizeOfPage'];
        $this->pagesQuantity = count($params['inksOnPages']);
        $this->quantity = $params['quantity'];

        $this->layout = new Layout($this->pagesQuantity, $this->sizeOfPage, $params['inksOnPages']);
        $this->formsQuantity = $this->layout->formsQuantity;

        $params['paperParams'] = array_merge($params['paperParams'], [
            'rollsQuantity' => $this->layout->rollsQuantity,
            'quantityOfItems' => $this->quantity,
            'layoutInkMap' => $this->layout->layoutInkMap
        ]);
        $this->paper = new Paper($params['paperParams']);

        $this->totalCost += $this->paper->totalCost;

        foreach($this->layout->inkMap as $sideInk) {
            foreach($sideInk as $key=>$inkID) {
                $this->inks[$key] = new Ink(array_merge(getInks::get($inkID), ['inkGroup' => $params['inkGroup']]));
                $this->inks[$key]->calculateQuantity($this->paper->calculateSheetSquare());
                $this->inks[$key]->calculateTotalCost();
                $this->inksTotalCost += $this->inks[$key]->calculateTotalCost();
            }
        };
        $this->inksTotalCost = $this->inksTotalCost * $this->quantity * (1 + $this->paper->rejectNorma / 100);

        $suboperationsData = getSuboperations::index($config);

        foreach($suboperationsData as $key=>$suboperationData) {
            $this->suboperations[]=new Suboperation($suboperationData, $this->calculateQuantity($suboperationData['type']));
        }

        $form = new Form(getForms::get(1));
        $this->formsTotalCost = $this->formsQuantity * $form->priceRUR;
    }
}

// const QUANTITY = 2500;

const QUANTITY = 2283;
const SIZE = 'A3';

$configName = "Newspaper Block";

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

$halfProductParams = [
    'configName' => $configName,
    'quantity' => QUANTITY,
    'sizeOfPage' => SIZE,
    'inksOnPages' => $inksOnPages,
    'inkGroup' => 4,
    'paperParams' => getPaper::get(1),
];

$newspaperBlock = new HalfProduct($halfProductParams);

echo "Бумага - " . round($newspaperBlock->paper->totalCost,2) . " руб.<br>";
echo "Краска - " . round($newspaperBlock->inksTotalCost,2) . " руб.<br>";
echo "Формы - " . round($newspaperBlock->formsTotalCost,2) . " руб.<br>";
echo "Трудозатраты - " . (round($newspaperBlock->suboperations[0]->totalJobCost,2) + round($newspaperBlock->suboperations[1]->totalJobCost,2)) . " руб.<br>";

debug($newspaperBlock);

?>