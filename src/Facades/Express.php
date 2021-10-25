<?php

namespace Pharaoh\Express\Facades;

use Illuminate\Support\Facades\Facade;

class Express extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        // 回傳 alias 的名稱
        return 'express';
    }
}
