<?php

namespace App\Http\Middleware;

use App\Http\Library\JwtAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class VerifyApiJwt
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
//        $token = $request->input("token", "");
//        $authData = JwtAuth::verifyJwt($token);
//
//        if (empty($authData)) {
//            return Response::json(['code' => -404, 'msg' => 'token is invalid', 'data' => null]);
//        }
//
//        $request->merge(["userId" => $authData]);
        return $next($request);
    }
}
