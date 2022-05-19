<?php
namespace Material;

class Material {
    /**
     * baseParams   Массив параметров
     * 
     * group            Группа материалов (бумага, краска и пр.)
     * title            Название
     * type             Тип
     * mainUnit         Единица измерения основная
     * usageRate        Норма расхода (по умолчанию - 1 основная единица на 1 единицу продукции)
     * price            Цена за основную единицу
     * currency         Валюта цены
     */

    public $group;          // Группа
    public $title;          // Название
    public $type;           // Тип
    public $mainUnit;       // Единица измерения основная
    public $price;          // Цена за единицу (основную)
    public $usageRate;      // Норма расхода
    public $currency;       // Валюта цены

    public $quantity;       // Количество

    public function calculateTotalCost() {
        return $this->price * $this->quantity;
    }

    public function __construct($baseParams)
    {
        $this->group = $baseParams['group'];
        $this->title = $baseParams['title'];
        $this->type = $baseParams['type'];
        $this->mainUnit = $baseParams['mainUnit'];
        $this->price = $baseParams['price'];
        $this->usageRate = $baseParams['usageRate'];
        $this->currency = $baseParams['currency'];
    }
}

?>