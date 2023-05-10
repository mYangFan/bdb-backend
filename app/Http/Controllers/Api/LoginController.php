<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Library\JwtAuth;
use App\Models\Admin\AdminUser;
use App\Models\Api\User;
use App\Models\Api\UserBackpack;
use App\Models\Api\UserReward;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->input("code");

        if (empty($code)) {
            return ["code" => 1, "msg" => "code is required", "data" => null];
        }

//        $params = [
//            'appid' => env("APP_ID"),
//            'secret' => env("APP_SECRET"),
//            'js_code' => $code,
//            'grant_type' => 'authorization_code'
//        ];
//        $uriParams = http_build_query($params);
//        $client = new Client(['verify' => false]);
//        $response = $client->get("https://api.weixin.qq.com/sns/jscode2session?" . $uriParams);
//
//        $body = json_decode($response->getBody());
//
//        $openId = data_get($body, "openId");

        $openId = 'pangpang';
        $user = User::findOrCreateUser($request, $openId);
        $backpack = UserBackpack::getBackpackByUser($user->id);
        $rewards = UserReward::getRewardsByUser($user->id);

        $userData = [
            "role" => $user,
            "backpack" => $backpack,
            "battle" => [
                "lastMaxLevel" => $user->max_level,
            ],
            "token" => JwtAuth::createJwt($user->id, $user->nick_name),
            "reward" => $rewards,
            "serverTime" => date("Y-m-d H:i:s", time())];

        return [
            "code" => 0,
            "msg" => 'SUCCESS',
            "data" => convert2Camel($userData)
        ];
    }

    //授权
    public function authUser(Request $request)
    {
        return User::authUser($request);
    }
}
