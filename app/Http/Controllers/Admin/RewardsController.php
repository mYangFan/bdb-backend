<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    }
}
