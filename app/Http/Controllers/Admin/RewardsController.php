<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminUser;
use App\Models\Api\UserReward;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RewardsController extends Controller
{
    public function setRewardArea(Request $request)
    {
        $location = $request->input("location");
        if (empty($location)) {
            return ['code' => 0, 'msg' => '请设置领奖范围', 'data' => null];
        }

        $locationArray = explode("|", $location);

        $result = DB::table("reward_area")->insert(['province' => data_get($locationArray, '0'), 'city' => data_get($locationArray, '1'), 'district' => data_get($locationArray, '2')]);
        if (!$result) {
            return ['code' => 0, 'msg' => '设置领奖范围失败', 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }


    public function getRewardAreas(Request $request)
    {
        $page = $request->input("page");
        $pageSize = $request->input("pageSize");

        $areas = DB::table("reward_area");

        $cloneDB = clone $areas;

        $total = $cloneDB->count();

        $data = $areas->offset(($page - 1) * $pageSize)->limit($pageSize)->get();

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => ['total' => $total, 'data' => convert2Camel($data)]];
    }

    public function fulReward(Request $request)
    {
        $code = $request->input("code");
        $username = $request->input("username");
        $password = $request->input("password");
        $location = $request->input("location");
        $locationArr = explode("|", $location);

        $shop = $request->input("shop");

        if (empty($code)) {
            return ['code' => 0, 'msg' => '无效奖券，请核对信息后再进行核销操作', 'data' => null];
        }

        $nowDate = Carbon::now()->format('Ymd');
        $now = Carbon::now()->format('Y-n-d H:i:s');

        $userReward = UserReward::query()->where("code", $code)->first();
        if (empty($userReward) || $userReward->state != 1) {
            return ['code' => 0, 'msg' => '无效奖券，请核对信息后再进行核销操作', 'data' => null];
        }

        if ($userReward->expired < $nowDate) {
            return ['code' => 0, 'msg' => '奖券已过期', 'data' => null];
        }

        $user = AdminUser::query()->where("username", $username)->where("password", md5($password))->first();
        if (empty($user)) {
            return ['code' => 0, 'msg' => '用户名或密码错误', 'data' => null];
        }

        $result = $userReward->update(['state' => 2, 'ful_at' => $nowDate, 'f_province' => data_get($locationArr, '0'), 'f_city' => data_get($locationArr, '1'), 'f_district' => data_get($locationArr, '2'), 'f_shop' => $shop, 'updated_at' => $now]);
        if (!$result) {
            return ['code' => 0, 'msg' => '核销失败，请稍后重试', 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }
}
