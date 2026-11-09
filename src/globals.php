<?php

use yentu\Yentu;

if (!function_exists('begin')) {
    function begin(): \yentu\database\Begin
    {
        return Yentu::begin();
    }
}

if (!function_exists('refschema')) {
    function refschema(string $name): \yentu\database\Schema
    {
        return Yentu::refschema($name);
    }
}

if (!function_exists('reftable')) {
    function reftable(string $name): \yentu\database\Table
    {
        return Yentu::reftable($name);
    }
}

if (!function_exists('uuid7')) {
    function uuid7(?bool $asBinary = null): string
    {
        return \yentu\Uuid::v7($asBinary);
    }
}
