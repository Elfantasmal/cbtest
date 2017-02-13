<?php

namespace JiaLeo\Jwt;

use Illuminate\Support\Facades\Facade;

class JwtAuthFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'jwt';
    }
}
