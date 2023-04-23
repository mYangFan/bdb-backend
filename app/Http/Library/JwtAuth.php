<?php

namespace App\Http\Library;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuth
{
    /**
     * 生成jwt
     * @param $uid
     * @param $userName
     * @return string
     */
    public static function createJwt($uid, $userName)
    {
        $key = config("auth.jwt-key");
        $payload = [
            "uid"      => $uid,
            "userName" => $userName
        ];

        return JWT::encode($payload, $key, "HS256");
    }

    /**
     * 校验jwt
     * @param $jwt
     */
    public static function verifyJwt($jwt)
    {
        $key = config("auth.jwt-key");

        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return 0;
        }

        return $decoded->uid;
    }
}
