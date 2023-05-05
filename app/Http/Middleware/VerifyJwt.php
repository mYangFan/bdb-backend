<?php

namespace App\Http\Middleware;

use App\Http\Library\JwtAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class VerifyJwt
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
        $token = $request->header("Authorization");
        if (Str::startsWith($token, 'Bearer ')) {
            $token = Str::substr($token, 7);
        }

        $authData = JwtAuth::verifyJwt($token);

        if (empty($authData)) {
            return Response::json(['code' => 401, 'msg' => 'auth fail', 'data' => null]);
        }

        $redisToken = Redis::get("token:" . $authData);

        if ($redisToken && $redisToken == $token) {
            $request->merge(["userId" => $authData]);
            Redis::setex("token:" . $authData, 3600, $token);
        } else {
            return Response::json(['code' => 401, 'msg' => 'token has been expired', 'data' => null]);
        }

        return $next($request);
    }
}
