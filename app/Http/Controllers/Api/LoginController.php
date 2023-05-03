<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Library\JwtAuth;
use App\Models\Api\User;
use App\Models\Api\UserBackpack;
use App\Models\Api\UserReward;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->input("code");
        $nickname = $request->input("nickname");
        $avatar = $request->input("avatar");
        $location = $request->input("location");
        if (empty($code)) {
            return ["code" => 1, "msg" => "code is required", "data" => null];
        }

        /*$client = new Client();
        $response = $client->get("https://api.weixin.qq.com/sns/jscode2session",[
            'query'=>[
                'appid' => env("APPID"),
                'secret' => env("APPSECRET"),
                'js_code' => $code,
                'grant_type' => 'authorization_code'
            ]
        ]);

        $body = $response->getBody();

        $openId = data_get($body, "openId");*/

        $openId = 123456789;

        $user = User::findOrCreateUser($request, $openId);
        $backpack = UserBackpack::getBackpackByUser($user->id);
        $rewards = UserReward::getRewardsByUser($user->id);

        $userData = [
            "role"       => $user,
            "backpack"   => $backpack,
            "battle"     => [
                "lastMaxLevel" => $user->max_level,
            ],
            "token"      => JwtAuth::createJwt($user->id, $user->nick_name),
            "reward"     => $rewards,
            "serverTime" => date("Y-m-d H:i:s", time())];

        return [
            "code" => 0,
            "msg"  => 'SUCCESS',
            "data" => convert2Camel($userData)
        ];
    }
}
