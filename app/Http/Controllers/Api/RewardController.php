<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Reward;
use App\Models\Api\User;
use App\Models\Api\UserReward;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RewardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input("userId");

        return ['code' => 0, 'msg' => 'SUCCESS', 'data' => convert2Camel(UserReward::getRewardsByUser($userId))];
    }

    public function getReward(Request $request)
    {
        $userId = $request->input("userId");
        $rewardType = $request->input("rewardType");
        $location = $request->input("location");
        $shop = $request->input("shop");

        $locationArr = explode("|", $location);
        $province = data_get($locationArr, 0);
        $city = data_get($locationArr, 1);
        $district = data_get($locationArr, 2);

        $user = User::query()->where("id", $userId)->first();

        if (!$user) {
            return ['code' => 401, 'msg' => '请先完成登录授权操作', 'data' => null];
        }

        $nowDate = Carbon::now()->format("Ymd");
        $rewards = UserReward::getRewardsByUser($userId);
        if (count($rewards)) {
            $lastRewardDate = $rewards[0]->received_at; //最近一次领奖时间
            $gapRewardDate = Carbon::parse($lastRewardDate)->addDay()->format("Ymd");
            $rewardYearDate = Carbon::parse($lastRewardDate)->addDays(365)->format("Y-m-d");


            //24小时内只能领一次
            if ($nowDate < $gapRewardDate) {
                return ['code' => -10021, 'msg' => '您24小时内已经领取过奖励了', 'data' => null];
            }

            //一年内只能领7次
            if ($nowDate < $rewardYearDate && count($rewards) >= 7) {
                return ['code' => -10024, 'msg' => '您一年内领取奖励次数已达上限', 'data' => null];
            }
        }
        $expired = Carbon::parse()->addDays(5)->format("Ymd");
        $reward = Reward::query()->where('reward_type', $rewardType)->first();

        if (empty($reward)) {
            return ['code' => 1, 'msg' => '没有此奖品', 'data' => null];
        }
        $rewardAreas = DB::table("reward_area")->where("province", $province)->where("city", $city)->where("district", $district)->where('state', 1)->first();
        if (empty($rewardAreas)) {
            return ['code' => 1, 'msg' => '抱歉，你所在区域暂不参加此活动', 'data' => null];
        }

        try {
            $code = strtoupper(Str::random(12));
            $h5Uri = env("H5URI");
            $queryParams = http_build_query([
                'code'       => $code,
                'nickname'   => $user->nick_name,
                'reward'     => $reward->reward_name,
                'receivedAt' => $nowDate,
                'expired'    => $expired,
                'location'   => $location,
                "shop"       => $shop,
            ]);
            $qrcodeUri = $h5Uri . "/#/home?" . $queryParams;

            $result = UserReward::query()->insert(['reward_id' => $reward->id, "user_id" => $userId, "r_province" => $province, "r_city" => $city, "r_district" => $district, "r_location" => $location, "expired" => $expired, "received_at" => $nowDate, "code" => $code, "qrcode_uri" => $qrcodeUri]);
            if (!$result) {
                return ['code' => 1, 'msg' => '领取失败', 'data' => null];
            }
        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return ['code' => 1, 'msg' => '奖品领取失败，请检查网络状况或稍后再试', 'data' => null];
            } else {
                return ['code' => 1, 'msg' => '领取失败', 'data' => null];
            }
        }

        return ['code' => 0, 'msg' => 'SUCCESS', 'data' => null];
    }
}
