<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Sso extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\SsoService::class;
    }
}
