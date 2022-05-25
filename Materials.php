<?php

namespace Materials;

include ('Material.php');

use Material\Material;
use Data\getPaperRejectRoll;
use Data\getInkRollNorma;

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