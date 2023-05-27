<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $search = $request->input("search");
        $code = $request->input("code");

        $users = DB::table("user as u")
            ->leftJoin("user_reward as ur", "u.id", "=", "ur.user_id")
            ->when($search, function ($query) use ($search) {
                $query->where("u.nick_name", "like", "%" . $search . "%");
            })->when($state, function ($query) use ($state) {
                $query->where("u.state", $state);
            })->when($code, function ($query) use ($code) {
                $query->where("ur.code", $code);
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

        foreach ($users as $user) {
            $user->reward = data_get($userRewardData, "{$user->id}");
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => [
            'total' => $total,
            'data'  => convert2Camel($users)
        ]];

    }
}
