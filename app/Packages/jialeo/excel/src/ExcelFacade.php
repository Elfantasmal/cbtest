<?php

namespace JiaLeo\Excel;

use Illuminate\Support\Facades\Facade;

class ExcelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'excel';
    }
}
