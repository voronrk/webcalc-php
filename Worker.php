<?php

namespace Worker;

use Data\GetJobTariffs;

class Worker 
{

    public $name;       // ФИО
    public $position;   // Должность
    public $grade;      // Разряд
    public $jobTariff;  // Ставка за нормочас, руб.

    public function __construct($data)
    {
        $this->grade = $data['grade'];
        $this->position = $data['position'];
        $this->name = isset($data['name']) ? $data['name'] : '';
        $this->jobTariff = GetJobTariffs::getByKey('grade', $this->grade)['tariff'];
    }
}