<?php

namespace HalfProduct;

include ('Data.php');
include ('Layout.php');
include ('Materials.php');
include ('Suboperation.php');

use Layout\Layout;
use Data\getPaper;
use Materials\Paper;
use Materials\Ink;
use Materials\Form;
use Data\getInks;
use Suboperation\Suboperation;
use Data\getForms;
use Data\getSuboperations;

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
        $suboperationsData = getSuboperations::get($params['configName']);

        $this->layout = new Layout($this->pagesQuantity, $this->sizeOfPage, $params['inksOnPages']);
        $this->formsQuantity = $this->layout->formsQuantity;

        $params['paperData'] = array_merge(getPaper::get(1), [
            'rollsQuantity' => $this->layout->rollsQuantity,
            'quantityOfItems' => $this->quantity,
            'layoutInkMap' => $this->layout->layoutInkMap
        ]);
        $this->paper = new Paper($params['paperData']);

        $this->totalCost += $this->paper->totalCost;

        foreach($this->layout->inkMap as $sideInks) {
            foreach($sideInks as $key=>$inkID) {
                $this->inks[$key] = new Ink(array_merge(getInks::get($inkID), ['inkGroup' => $params['inkGroup']]));
                $this->inks[$key]->calculateQuantity($this->paper->calculateSheetSquare());
                $this->inks[$key]->calculateTotalCost();
                $this->inksTotalCost += $this->inks[$key]->calculateTotalCost();
            }
        };
        $this->inksTotalCost = $this->inksTotalCost * $this->quantity * (1 + $this->paper->rejectNorma / 100);

        $this->totalCost += $this->inksTotalCost;

        foreach($suboperationsData as $key=>$suboperationData) {
            $this->suboperations[$key] = new Suboperation($suboperationData, $this->calculateQuantity($suboperationData['type']));
            $this->totalCost += $this->suboperations[$key]->totalJobCost;
        }

        $form = new Form(getForms::get(1));
        $this->formsTotalCost = $this->formsQuantity * $form->priceRUR;

        $this->totalCost += $this->formsTotalCost;
    }
}