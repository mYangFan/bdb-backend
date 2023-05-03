<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinishGuideController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input("userId");

        $updateResult = User::updateUserGuide($userId);
        if (!$updateResult) {
            return ["code" => 1, "msg" => "finish guide error", "data" => null];
        }

        return [
            "code"       => 0,
            "msg"        => "SUCCESS",
            "data"       => null,
            "serverTime" => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }
}
