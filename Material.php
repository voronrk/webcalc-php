<?php
namespace Material;

class Material {

    public $title;          // Название
    public $type;           // Тип
    public $mainUnit;       // Единица измерения основная
    public $price;          // Цена за единицу (основную)
    public $usageRate;      // Норма расхода
    public $currency;       // Валюта цены

    public $quantity;       // Количество

    public function __construct($title='', $type='', $mainUnit='', $price=0, $usageRate=1, $currency='RUR')
    {
        $this->title = $title;
        $this->type = $type;
        $this->mainUnit = $mainUnit;
        $this->price = $price;
        $this->usageRate = $usageRate;
        $this->currency = $currency;
    }

    private function unit_recalc($unitFrom, $unitTo) {

    }

}

?>