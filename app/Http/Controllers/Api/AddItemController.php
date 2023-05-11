<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Backpack;
use App\Models\Api\UserBackpack;
use Illuminate\Http\Request;

class AddItemController extends Controller
{
    public function index(Request $request)
    {
        $itemId = $request->input("itemId");
        $count = $request->input("count");
        $userId = $request->input(("userId"));

        $result = UserBackpack::updateItem($userId, $itemId, $count);
        if (empty($result)) {
            return [
                "code" => 1,
                "msg" => "道具领取失败",
                "data" => null
            ];
        }

        return ["code" => 0, "msg" => "SUCCESS", "data" => ["backpack" => convert2Camel(UserBackpack::getBackpackByUser($userId))]];
    }
}
