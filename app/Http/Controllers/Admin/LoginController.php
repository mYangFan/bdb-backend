<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Library\JwtAuth;
use App\Models\Admin\AdminUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $username = $request->input("username");
        $password = $request->input("password");

        try {
            $user = AdminUser::authUser($username, $password);
        } catch (\Exception $e) {
            return ['code' => 0, 'msg' => '用户名或密码错误', 'data' => null];
        }

        if ($user->state == 2) {
            return ['code' => 0, 'msg' => '账户被冻结，请联系管理员', 'data' => null];
        }

        $token = JwtAuth::createJwt($user->id, $user->username);

        try {
            Redis::setex("token:" . $user->id, 3600, $token);
            $user->update(['last_login_at' => Carbon::now()->format('Y-m-d H:i:s')]);
        } catch (\Exception $e) {
            Log::error("redis error: " . $e->getMessage());
            return ['code' => 0, 'msg' => '服务器开小差去了。。。', 'data' => null];
        }

        return [
            'code' => 1,
            'msg' => '',
            'data' => [
                'username' => $user->username,
                'token' => $token
            ]];
    }

    public function logout(Request $request)
    {
        $userId = $request->input("userId");
        $result = Redis::del("token:" . $userId);

        if (!$result) {
            return ['code' => 0, 'msg' => '退出登录失败', 'data' => null];
        }

        return ['code' => 1, 'msg' => '退出登录成功，即将跳转至登录页', 'data' => null];
    }
}
