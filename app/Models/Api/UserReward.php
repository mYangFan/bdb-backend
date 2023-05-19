<?php

namespace App\Models\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserReward extends Model
{
    use HasFactory;

    protected $table = "user_reward";
    protected $guarded = [];

    public static function getRewardsByUser($userId)
    {
        return DB::table("user_reward as urd")
            ->leftJoin("reward as rd", "urd.reward_id", "=", "rd.id")
            ->where("urd.user_id", $userId)
            ->select(["rd.reward_name", "rd.reward_type", "rd.reward_state", "urd.expired", "urd.qrcode_uri", "urd.received_at"])
            ->orderBy("urd.id", "desc")
            ->get();
    }

    public static function addReward($request)
    {
        $rewardId = $request->input("rewardId");
        $userId = $request->input("userId");
        $location = $request->input("location");

        $locationArray = explode("|", $location);

        $area = DB::table("reward_area")->where("province", data_get($locationArray, "0"))
            ->where("city", data_get($locationArray, "1"))
            ->where("district", data_get($locationArray, "2"))
            ->first();

        if (empty($area)) {
            return ['code' => 1, 'msg' => '抱歉，您所在区域不在领奖范围', 'data' => null];
        }

        $reward = Reward::query()->where("reward_id", $rewardId)->first();
        if (empty($reward)) {
            return ['code' => 1, 'msg' => '奖品已领完', 'data' => null];
        }

        $expireTime = Carbon::parse()->addDays(5)->format("Y-m-d H:i:s");

        $result = self::query()->insert(['user_id' => $userId, "reward_id" => $reward->id, "expired" => $expireTime]);

    }
}
