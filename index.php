<?php

include ('Halfproduct.php');

use Halfproduct\HalfProduct;


function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
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

$halfProductData = [
    'configName' => $configName,
    'quantity' => QUANTITY,
    'sizeOfPage' => SIZE,
    'inksOnPages' => $inksOnPages,
    'inkGroup' => 4,
];

$newspaperBlock = new HalfProduct($halfProductData);

echo "Бумага - " . round($newspaperBlock->paper->totalCost,2) . " руб.<br>";
echo "Краска - " . round($newspaperBlock->inksTotalCost,2) . " руб.<br>";
echo "Формы - " . round($newspaperBlock->formsTotalCost,2) . " руб.<br>";
echo "Трудозатраты - " . (round($newspaperBlock->suboperations[0]->totalJobCost,2) + round($newspaperBlock->suboperations[1]->totalJobCost,2)) . " руб.<br>";
echo "Всего - " . (round($newspaperBlock->totalCost,2)) . " руб.<br>";

debug($newspaperBlock);

?>