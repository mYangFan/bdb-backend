<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use App\Models\Api\UserReward;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    //todo 奖品列表
    public function index()
    {

    }

    public function getReward(Request $request)
    {
        $userId = $request->input("userId");
        $user = User::query()->where("id", $userId)->first();

        if (!$user) {
            return ['code' => 401, 'msg' => '请先完成登录授权操作', 'data' => null];
        }

        $rewards = UserReward::getRewardsByUser($userId);
        if (count($rewards)) {
            $lastRewardDate = $rewards[0]->updated_at; //最近一次领奖时间
            $gapRewardDate = Carbon::parse($lastRewardDate)->addDay()->format("Y-m-d H:i:s");
            $rewardYearDate = Carbon::parse($lastRewardDate)->addDays(365)->format("Y-m-d H:i:s");
            $nowDate = Carbon::now()->format("Y-m-d H:i:s");

            //24小时内只能领一次
            if ($nowDate < $gapRewardDate) {
                return ['code' => -10021, 'msg' => '您24小时内已经领取过奖励了', 'data' => null];
            }

            //一年内只能领7次
            if ($nowDate < $rewardYearDate && count($rewards) >= 7) {
                return ['code' => -10024, 'msg' => '您一年内领取奖励次数已达上限', 'data' => null];
            }
        }
    }

    public function fulReward(Request $request)
    {
        $userId = $request->input("userId");
        $rewardId = $request->input("rewardId");

        $userReward = UserReward::query();
    }
}
