<?php
namespace Data;

require_once('Interfaces/GetData.php');

use Interfaces\DataInterface;

function debug($data) 
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
};

abstract class GetData implements DataInterface
{

    public static $data = array();

    public static function getById(int $id): array
    {
        return static::getByKey('id', $id);
    }

    public static function getAllById(int $id): array
    {
        return static::getAllByKey('id', $id);
    }

    public static function getByKey(string $key, string $value): array
    {
        return current(array_filter(static::$data, function($item) use ($key, $value) {return $item[$key]==$value;}));
    }

    public static function getAllByKey(string $key, string $value): array
    {
        return array_filter(static::$data, function($item) use ($key, $value) {return $item[$key]==$value;});
    }

    public static function getAll(): array
    {
        return static::$data;
    }
}

class GetCurrencyCourse extends GetData
{
    public static $data = [
        [
            'title' => 'RUR',
            'value' => 1,
        ],
        [
            'title' => 'USD',
            'value' => 120,
        ],
        [
            'title' => 'EUR',
            'value' => 130
        ],
    ];
}

class GetInks extends GetData 
{
    public static $data = [
        [
            'id' => 1,
            'group' => 'КРАСКА',
            'title' => 'Краска ролевая чёрная',
            'type' => 'ролевая',
            'mainUnit' => 'кг',
            'price' => 1.62,
            'currency' => 'EUR',
        ],
        [
            'id' => 2,
            'group' => 'КРАСКА',
            'title' => 'Краска ролевая голубая',
            'type' => 'ролевая',
            'mainUnit' => 'кг',
            'price' => 2.32,
            'currency' => 'EUR',
        ],
        [
            'id' => 3,
            'group' => 'КРАСКА',
            'title' => 'Краска ролевая желтая',
            'type' => 'ролевая',
            'mainUnit' => 'кг',
            'price' => 2.32,
            'currency' => 'EUR',
        ],
        [
            'id' => 4,
            'group' => 'КРАСКА',
            'title' => 'Краска ролевая пурпурная',
            'type' => 'ролевая',
            'mainUnit' => 'кг',
            'price' => 2.32,
            'currency' => 'EUR',
        ],
        [
            'id' => 5,
            'group' => 'КРАСКА',
            'title' => 'Краска ролевая красная (тест)',
            'type' => 'ролевая',
            'mainUnit' => 'кг',
            'price' => 20.32,
            'currency' => 'EUR',
        ],
    ];
}

class GetSuboperations extends GetData 
{
    public static $data = [
        [
            'id' => 1,
            'configName' => 'NewspaperBlock',
            'type' => 'preparation',
            'title' => 'Приладка 1 формы А3 формат CityLine',
            'machine' => 'CityLine',
            'complexityIndex' => '1',
            'unit' => 'шт.',
            'standardHoursPerPiece' => 0.067,
            'primaryMaterials' => [],
            'materials' => [],
            'workerIDs' => [1,2,3,4],
        ],
        [
            'id' => 2,
            'configName' => 'NewspaperBlock',
            'type' => 'passing',
            'title' => 'Печать А3 с 2-х рулонов цветная CityLine',
            'machine' => 'CityLine',
            'complexityIndex' => '1',
            'unit' => 'листопрогон',
            'standardHoursPerPiece' => 0.000056,
            'primaryMaterials' => [],
            'materials' => [],
            'workerIDs' => [1,2,3,4],
        ],
    ];
};

class GetJobTariffs extends GetData 
{
    public static $data = [
        [
            'grade' => 1,
            'tariff' => 47.575, 
        ],
        [
            'grade' => 2,
            'tariff' => 63.25, 
        ],
        [
            'grade' => 3,
            'tariff' => 75.9, 
        ],
        [
            'grade' => 4,
            'tariff' => 95.15, 
        ],
        [
            'grade' => 5,
            'tariff' => 113.85, 
        ],
        [
            'grade' => 6,
            'tariff' => 126.5
        ],
    ];
};

class GetWorkers extends GetData 
{
    public static $data = [
        [
            'id' => 1,
            'position' => 'Печатник CitiLine 5 разряд',
            'grade' => '5',
        ],
        [
            'id' => 2,
            'position' => 'Печатник CitiLine 4 разряд',
            'grade' => '4',
        ],
        [
            'id' => 3,
            'position' => 'Печатник CitiLine 3 разряд',
            'grade' => '3',
        ],
        [
            'id' => 4,
            'position' => 'Печатник CitiLine 3 разряд',
            'grade' => '3',
        ],
    ];
};

class GetPaper extends GetData 
{

    public static $data = [
        [
            'id' => 1,
            'group' => 'БУМАГА',
            'title' => 'Газетная',
            'type' => 'газетная',
            'mainUnit' => 'кг',
            'price' => 32.25,
            'usageRate' => 1,
            'basicWeight' => 42,
            'currency' => 'RUR',
        ]
    ];
}

class GetForms extends GetData 
{

    public static $data = [
        [
            'id' => 1,
            'group' => 'ФОРМЫ',
            'title' => '608х844 CityLine СТР "Kaizen"',
            'type' => 'CityLine',
            'mainUnit' => 'шт.',
            'price' => 1.83,
            'usageRate' => 1,
            'currency' => 'USD',
        ]
    ];
}

// Расход краски при ролевой печати (кг/кв. м)
class GetInkRollNorma extends GetData 
{
    public static $data = [
        [
            'group' => 1,
            'typeOfPaper' => 'газетная',
            'description' => 'Печать текста в одну краску',
            'norma' => 0.000074
        ],
        [
            'group' => 2,
            'typeOfPaper' => 'газетная',
            'description' => 'Печать текста с иллюстрациями (50% текст, 50% иллюстрации) в одну краску',
            'norma' => 0.000222
        ],
        [
            'group' => 3,
            'typeOfPaper' => 'газетная',
            'description' => 'Печать текста с иллюстрациями (50% текст, 50% иллюстрации)',
            'norma' => 0.000111
        ],
        [
            'group' => 4,
            'typeOfPaper' => 'газетная',
            'description' => 'Печать текста с иллюстрациями (более 50% иллюстрации)',
            'norma' => 0.000157
        ],
        [
            'group' => 5,
            'typeOfPaper' => 'офсетная',
            'description' => 'Печать текста в одну краску',
            'norma' => 0.000083
        ],
        [
            'group' => 6,
            'typeOfPaper' => 'офсетная',
            'description' => 'Печать текста с иллюстрациями (50% текст, 50% иллюстрации) в одну краску',
            'norma' => 0.000297
        ],
        [
            'group' => 7,
            'typeOfPaper' => 'офсетная',
            'description' => 'Печать текста с иллюстрациями (50% текст, 50% иллюстрации)',
            'norma' => 0.000167
        ],
        [
            'group' => 8,
            'typeOfPaper' => 'офсетная',
            'description' => 'Печать текста с иллюстрациями (более 50% иллюстрации)',
            'norma' => 0.000241
        ],
    ];
};

class GetPaperRejectRoll 
{
/**  Нормы техотходов бумаги для ролевой печати (на тираж, включая приладку, прогоны, срыв и т.п.)
*   CSV structure:
*   0 - rolls quantity
*   1 - quantity
*   2 - reject persents for 1+1
*   3 - reject persents for 2+1
*   4 - reject persents for 2+2
*   5 - reject persents for 4+1
*   6 - reject persents for 4+2
*   7 - reject persents for 4+4
*/
    static $norma = [];

    private static $referenceRolls;
    private static $referenceQuantity;
    private static $referenceInks;

    private static $previousQuantity = 0;
    private static $nextQuantity = 0;
    private static $previousRejectNorma = 0;
    private static $nextRejectNorma = 0;

    static $rejectNorma;

    const inks = ['','','1+1','2+1','2+2','4+1','4+2','4+4'];

    private static function calculateRejectNorma() 
    {
        return self::$previousRejectNorma - (self::$previousRejectNorma - self::$nextRejectNorma) * ((self::$referenceQuantity - self::$previousQuantity)/(self::$nextQuantity - self::$previousQuantity));
    }

    private static function parseItem($item) 
    {
        $arItem = explode(';',$item);
        for ($i=2;$i<=7;$i++) {
            self::$norma[] = [
                'machine' => 'CitiLine',
                'rolls' => $arItem[0],
                'quantity' => $arItem[1],
                'inks' => self::inks[$i],
                'value' => $arItem[$i]
            ];
            if (($arItem[0] == self::$referenceRolls) && (self::inks[$i] == self::$referenceInks)){
                if ($arItem[1] == self::$referenceQuantity) {
                    self::$previousQuantity = $arItem[1];
                    self::$nextQuantity = $arItem[1];
                    return $arItem[$i];
                };
                if ($arItem[1]<self::$referenceQuantity) {
                    self::$previousQuantity = $arItem[1];
                    self::$previousRejectNorma = $arItem[$i];
                };
                if ((self::$nextQuantity == 0) && ($arItem[1]>self::$referenceQuantity)) {
                    self::$nextQuantity = $arItem[1];
                    self::$nextRejectNorma = $arItem[$i];
                    return self::calculateRejectNorma();
                };
            };
        };
        return false;
    }

    private static function readBase() 
    {
        $baseFile=fopen ("PaperRejectRoll.csv", "r");
		while (!feof($baseFile)){
			$sItem=fgets($baseFile);
            $parseItemResult = self::parseItem($sItem);
            if ($parseItemResult) 
            {
                self::$rejectNorma = $parseItemResult;
                return;
            }
            
		};
		fclose($baseFile);
    }

    public static function index($rolls, $quantity, $inks) 
    {
        self::$referenceRolls = ceil($rolls);
        if ($rolls>=2) {
            self::$referenceQuantity = $quantity * floor($rolls);
        } else {
            self::$referenceQuantity = $quantity;
        };        
        self::$referenceInks = $inks;

        self::readBase();

        return self::$rejectNorma;
    }
};

