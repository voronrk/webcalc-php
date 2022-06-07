<?php

namespace Material;

class Layout {

    public $printSides;
    public $rollsQuantity;
    public $pagesPerSide;
    public $pagesPerSheet;
    public $pagesQuantity;
    public $inksOnPages;
    public $inkMap = [];
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

    private function addInkToSide(int $inkID, int $side) {
        if (empty($this->inkMap[$side])) {
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

    public function __construct(int $pagesQuantity, string $sizeOfPage, array $inksOnPages) {
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