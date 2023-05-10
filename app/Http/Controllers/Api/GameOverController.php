<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use App\Models\Api\UserBackpack;
use App\Models\Api\UserReward;
use Illuminate\Http\Request;

class GameOverController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input("userId");
        $level = $request->input("level");

        $user = User::query()->where("id", $userId)->first();
        if ($level > $user->max_level) {
            $user->update(['max_level' => $level]);
        }

        $backpack = UserBackpack::getBackpackByUser($userId);
        $rewards = UserReward::getRewardsByUser($userId);

        $returnData = [
            'backpack' => $backpack,
            'reward' => $rewards,
            'battle' => [
                'lastMaxLevel' => $user->max_level,
                'currentLevel' => $level,
            ]
        ];

        return ['code' => 0, 'msg' => 'SUCCESS', 'data' => convert2Camel($returnData)];
    }
}
