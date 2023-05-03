<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserReward extends Model
{
    use HasFactory;

    protected $table = "user_reward";

    public static function getRewardsByUser($userId)
    {
        return DB::table("user_reward as urd")
            ->leftJoin("reward as rd", "urd.reward_id", "=", "rd.id")
            ->where("urd.user_id", $userId)
            ->select(["rd.reward_name", "rd.reward_type", "rd.reward_state", "rd.reward_life_time", "urd.qrcode_uri", "urd.updated_at"])
            ->orderBy("urd.id", "desc")
            ->get();
    }
}
