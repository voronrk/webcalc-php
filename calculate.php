<?php

include ('Halfproduct.php');

use Halfproduct\HalfProduct;


function write($filename, $data) {
    $log = fopen($filename, 'w');
    fwrite($log, print_r($data, true) . PHP_EOL);
    fclose($log);
};

function debug($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

$params=json_decode(file_get_contents('php://input'),true);

$quantity = $params['quantity'];
$size = $params['size'];
$inksOnPages = $params['inks'];
$rollWidth = $params['rollWidth'];

$configName = "Newspaper Block";

$halfProductData = [
    'configName' => $configName,
    'quantity' => $quantity,
    'sizeOfPage' => $size,
    'inksOnPages' => $inksOnPages,
    'inkGroup' => 4,
    'rollWidth' => $rollWidth,
];

$newspaperBlock = new HalfProduct($halfProductData);
write('result.log', $newspaperBlock);

$result = [
    'formQuantity' => [
        'title' => 'Количество форм',
        'value' => $newspaperBlock->formsQuantity],
    'rollsQuantity' => [
        'title' => 'Количество ролей',
        'value' => $newspaperBlock->layout->rollsQuantity],
    'layoutInkMap' => [
        'title' => 'Красочность издания',
        'value' => $newspaperBlock->layout->layoutInkMap],
    'paperCost' => [
        'title' => 'Стоимость бумаги',
        'value' => round($newspaperBlock->paper->totalCost,2)],
    'inksCost' => [
        'title' => 'Стоимость краски',
        'value' => round($newspaperBlock->inksTotalCost,2)],
    'formsCost' => [
        'title' => 'Стоимость форм',
        'value' => round($newspaperBlock->formsTotalCost,2)],
    'jobCost' => [
        'title' => 'Трудозатраты (с налогами)',
        'value' => round($newspaperBlock->suboperations[0]->totalJobCost,2) + round($newspaperBlock->suboperations[1]->totalJobCost,2)],
    'totalCost' => [
        'title' => 'Стоимость',
        'value' => round($newspaperBlock->totalCost,2)],
];
echo json_encode($result);

write('result.log', $newspaperBlock);

?>