<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class BlockIp
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
        $blockedIps = [
            '58.229.37.46',
            '45.205.1.8',
            '14.33.117.203',
        ];
        
        $ip = $request->getClientIp();


        // 프록시 대응
        if ($request->header('X-Forwarded-For')) {
            $ip = trim(explode(',', $request->header('X-Forwarded-For'))[0]);
        }

        if (in_array($ip, $blockedIps)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
