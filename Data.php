<?php
namespace Data;

class getInks {
    const inks = [
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

    public static function get($id) {
        return current(array_filter(self::inks, function($item) use ($id) {return $item['id']==$id;}));
    }

    public static function getAll() {
        return self::inks;
    }
}

class getSuboperations {
    const suboperations = [
        [
            'id' => 1,
            'type' => 'preparation',
            'title' => 'Приладка 1 формы А3 формат CityLine',
            'machine' => 'CityLine',
            'complexityIndex' => '1',
            'unit' => 'шт.',
            'standardHoursPerPiece' => 0.067,
            'materials' => [],
            'workers' => [1,2,3,4],
        ],
        [
            'id' => 2,
            'type' => 'passing',
            'title' => 'Печать А3 с 2-х рулонов цветная CityLine',
            'machine' => 'CityLine',
            'complexityIndex' => '1',
            'unit' => 'листопрогон',
            'standardHoursPerPiece' => 0.000056,
            'materials' => [],
            'workers' => [1,2,3,4],
        ],
    ];

    public static function index() {
        return self::suboperations;
    }
};

class getJobTariffs {
    const jobTariffs = [
        '1' => 47.575, 
        '2' => 63.25, 
        '3' => 75.9, 
        '4' => 95.15, 
        '5' => 113.85, 
        '6' => 126.5
    ];

    public static function index() {
        return self::jobTariffs;
    }
};

class getWorkers {
    const workers = [
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

    public static function index() {
        return self::workers;
    }
};

// Расход краски при ролевой печати (кг/кв. м)
class getInkRollNorma {
    const norma = [
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

    public static function getNorma($group) {
        return current(array_filter(self::norma, function($item) use ($group) {return $item['group']==$group;}))['norma'];
    }

    public static function index() {
        return self::norma;
    }
};

class getPaperRejectRoll {
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

    private static function calculateRejectNorma() {
        self::$rejectNorma = self::$previousRejectNorma - (self::$previousRejectNorma - self::$nextRejectNorma) * ((self::$referenceQuantity - self::$previousQuantity)/(self::$nextQuantity - self::$previousQuantity));
    }

    private static function parseItem($item) {
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
                    self::$rejectNorma = $arItem[$i];
                    self::$previousQuantity = $arItem[1];
                    self::$nextQuantity = $arItem[1];
                    return;
                };
                if ($arItem[1]<self::$referenceQuantity) {
                    self::$previousQuantity = $arItem[1];
                    self::$previousRejectNorma = $arItem[$i];
                };
                if ((self::$nextQuantity == 0) && ($arItem[1]>self::$referenceQuantity)) {
                    self::$nextQuantity = $arItem[1];
                    self::$nextRejectNorma = $arItem[$i];
                    self::calculateRejectNorma();
                    return;
                };
            };
        };
    }

    private static function readBase() {
        $baseFile=fopen ("PaperRejectRoll.csv", "r");
		while (!feof($baseFile)){
			$sItem=fgets($baseFile);
            self::parseItem($sItem);
		};
		fclose($baseFile);
    }

    public static function index($rolls, $quantity, $inks) {
        self::$referenceRolls = $rolls;
        self::$referenceQuantity = $quantity * ceil($rolls);
        self::$referenceInks = $inks;

        self::readBase();

        return self::$rejectNorma;
    }
};

class getPaper {

    const papers = [
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
            'rollWidth' => 76
        ]
    ];

    public static function get($id) {
        return current(array_filter(self::papers, function($item) use ($id) {return $item['id']==$id;}));
    }

    public static function index() {
        return self::paper;
    }
}

class getForms {

    const forms = [
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

    public static function get($id) {
        return current(array_filter(self::forms, function($item) use ($id) {return $item['id']==$id;}));
    }

    public static function index() {
        return self::forms;
    }
}