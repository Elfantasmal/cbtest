<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;


class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('app.debug') === true && $request->query->get('usertest') && $request->query->get('usertest') > 0) {
            \Jwt::set('admin_info', array(
                'admin_id' => $request->query->get('usertest')
            ));
        }

        if (!(!empty(\Jwt::get('admin_info')) && !empty(\Jwt::get('admin_info.admin_id')))) {
            throw new ApiException('你还没有登录或登录已过期', 'NO LOGIN');
        }

        return $next($request);
    }


}
