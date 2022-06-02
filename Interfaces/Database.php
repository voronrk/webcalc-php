<?php

namespace Inerfaces;

interface DatabaseInterface
{
    public function db_query($sql=''): array;
    public function db_exec($sql=''): array;
}