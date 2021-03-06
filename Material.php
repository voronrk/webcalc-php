<?php
namespace Material;

class Material {
    /**
     * params   Массив параметров
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
    public $price;          // Цена за основную единицу
    public $usageRate;      // Норма расхода
    public $currency;       // Валюта цены

    public $quantity;       // Количество
    public $priceRUR;       // Цена (руб.)
    public $totalCost;      // Сумма (руб.)

    public function getCurrencyCourse($currencyName) {
        $currencyCourse = [
            'RUR' => 1,
            'USD' => 120,
            'EUR' => 130
        ];
        return $currencyCourse[$currencyName];
    }

    public function calculateTotalCost() {
        return $this->priceRUR * $this->quantity;
    }

    public function calculatePriceRUR() {
        return $this->price * $this->getCurrencyCourse($this->currency);
    }

    public function __construct($params) {
        foreach($params as $key=>$value) {
            $this->$key = $value;
        };

        $this->priceRUR = $this->calculatePriceRUR();
    }
}

?>