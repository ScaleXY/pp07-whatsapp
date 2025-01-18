<?php

namespace ScaleXY\Whatsapp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ScaleXY\Whatsapp\Whatsapp
 */
class Whatsapp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ScaleXY\Whatsapp\Whatsapp::class;
    }
}
