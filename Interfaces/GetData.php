<?php

namespace Interfaces;

interface DataInterface
{
    public static function getByKey(string $key, string $value): array;
    public static function getAllByKey(string $key, string $value): array;
    public static function getAll(): array;
}