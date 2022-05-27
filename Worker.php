<?php

namespace Worker;

use Data\getJobTariffs;

class Worker {

    public $name;       // ФИО
    public $position;   // Должность
    public $grade;      // Разряд

    public function __construct($data)
    {
        $this->grade = $data['grade'];
        $this->position = $data['position'];
        $this->name = isset($data['name']) ? $data['name'] : '';
        $this->jobTariff = getJobTariffs::getByKey('grade', $this->grade)['tariff'];
    }
}