<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input("page");
        $pageSize = $request->input("pageSize");
        $state = $request->input("state");
        $search = $request->input("research");
        $code = $request->input("code");

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where("nick_name", "like", "%" . $search . "%");
            })->when($state, function ($query) use ($state) {
                $query->where("state", $state);
            });

        $cloneUsers = clone $users;
        $total = $cloneUsers->count();

        $users = $users->offset(($page - 1) * $pageSize)->limit($pageSize)->get();

        $userIds = $users->pluck("id")->toArray();

        $now = Carbon::now()->format("Ymd");
        $userRewardData = DB::table("user_reward as ur")
            ->leftJoin("reward as r", "ur.reward_id", "=", "r.id")
            ->leftJoin("admin_users as ar", "ur.ful_user_id", "=", "ar.id")
            ->when($code, function ($query) use ($code) {
                $query->where("code", $code);
            })
            ->whereIn("ur.user_id", $userIds)
            ->select(["ur.id", "ur.user_id", "r.reward_type", "r.reward_name", "ur.expired", "ur.qrcode_uri", "ur.code", DB::raw("CASE WHEN {$now} > ur.expired THEN 3 ELSE ur.state END AS reward_state"), 'ar.username as ful_user'])
            ->get()
            ->groupBy("user_id");

        $res = $users->map(function ($item) use ($userRewardData) {
            return array_merge($item->toArray(), ['reward' => data_get($userRewardData, "{$item["id"]}")]);
        })->all();

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => [
            'total' => $total,
            'data'  => convert2Camel($res)
        ]];

    }
}
