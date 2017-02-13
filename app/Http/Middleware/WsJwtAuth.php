<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;

/**
 * websocket验证中间件
 * Class WsAuth
 * @package App\Http\Middleware
 */
class WsAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty($request->request->get('token'))){
            throw new ApiException('AUTHORIZATION验证失败', 'AUTHORIZATION_INVALID', 401);
        }

        if(! \Jwt::check($request,$request->request->get('token'))){
            throw new ApiException('AUTHORIZATION验证失败', 'AUTHORIZATION_INVALID', 401);
        }

        return $next($request);
    }

}
