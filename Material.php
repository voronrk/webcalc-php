<?php
namespace Material;

require_once('Interfaces/MaterialInterface.php');

use Interfaces\MaterialInterface;

abstract class Material implements MaterialInterface
{
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

    private function getCurrencyCourse($currencyName):float {
        $currencyCourse = [
            'RUR' => 1,
            'USD' => 120,
            'EUR' => 130
        ];
        return $currencyCourse[$currencyName];
    }

    public function calculateTotalCost():float {
        return $this->priceRUR * $this->quantity;
    }

    private function calculatePriceRUR():float {
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