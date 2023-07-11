<?php

namespace Valinteca\Msegat\Facades;

use Illuminate\Support\Facades\Facade;

class Msegat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'msegat';
    }
}