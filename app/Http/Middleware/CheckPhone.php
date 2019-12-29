<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Closure;

class CheckPhone
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = $request->input('lang');

        if ($request->has('phone')) {

            $start1 = Str::startsWith($request->get('phone'), '5');
            $start2 = Str::startsWith($request->get('phone'), '05');

            if ($start1 || $start2) {
                $request->phone = intval($request->get('phone'));
            } else {
                if ($lang == 'ar') {
                    $msg = "صيغة الهاتف غير صحيحة لابد أن تبدأ ب 05 أو 5";
                } else {
                    $msg = "Phone number format invalid it must start with 05 or 5";
                }

                return response()->json(['status' => false, 'errNum' => 100, 'msg' => $msg]);
            }

        }
        return $next($request);
    }
}
