<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use App\Models\Api\UserBackpack;
use App\Models\Api\UserReward;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameStartController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input("userId");
        $level = $request->input("level");
        $user = User::query()->where("id", $userId)->first();

        if ($level > $user->max_level + 1) {
            return ['code' => 1, 'msg' => '关卡数异常', 'data' => null];
        }

        $backpack = UserBackpack::getBackpackByUser($userId);
        $rewards = UserReward::getRewardsByUser($userId);

        $returnData = [
            'backpack' => $backpack,
            'reward' => $rewards,
            'battle' => [
                'lastMaxLevel' => $user->max_level,
                'currentLevel' => $level,
                'canGetGift' => 1
            ]
        ];

        if (count($rewards) > 0) {
            $lastRewardDate = $rewards[0]->updated_at; //最近一次领奖时间
            $gapRewardDate = Carbon::parse($lastRewardDate)->addDay()->format("Y-m-d H:i:s");
            $rewardYearDate = Carbon::parse($lastRewardDate)->addDays(365)->format("Y-m-d H:i:s");
            $nowDate = Carbon::now()->format("Y-m-d H:i:s");

            //24小时内只能领一次
            if ($nowDate < $gapRewardDate) {
                $returnData['battle']['canGetGift'] = -10021;
//                return ['code' => -10021, 'msg' => '用户24小时内已经领取过奖励了', 'data' => convert2Camel($returnData)];
            }

            //一年内只能领7次
            if ($nowDate < $rewardYearDate && count($rewards) >= 7) {
                $returnData['battle']['canGetGift'] = -10024;
//                return ['code' => -10024, 'msg' => '用户一年内领取奖励次数已达上限', 'data' => convert2Camel($returnData)];
            }
        }

        return ['code' => 0, 'msg' => 'SUCCESS', 'data' => convert2Camel($returnData)];
    }
}
