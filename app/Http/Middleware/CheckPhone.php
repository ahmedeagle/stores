<?php

namespace App\Http\Middleware;

use Closure;

class CheckPhone
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
        if ($request->has('phone')){
            $request->phone = intval($request->get('phone'));
        }
        return $next($request);
    }
}
