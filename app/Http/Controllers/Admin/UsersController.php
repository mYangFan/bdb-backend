<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\AdminRoleUser;
use App\Models\Admin\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsersController
{

    public function index()
    {
        $users = AdminUser::getAllUsers();

        return ['code' => 0, 'msg' => '', "data" => $users];
    }

    public function updateUser(Request $request)
    {
        $userId = $request->input("userId");
        $roleId = $request->input("roleId");
        $tel = $request->input("tel");
        $name = $request->input("name");

        $user = AdminUser::getUser($userId);

        $updateFields = [];
        if ($tel) {
            $updateFields = array_merge($updateFields, ['tel' => $tel]);
        }

        if ($name) {
            $updateFields = array_merge($updateFields, ['name' => $name]);
        }

        DB::beginTransaction();

        try {
            $user->update($updateFields);
            if ($roleId) {
                AdminRoleUser::query()->where("user_id", $userId)->update(["role_id" => $roleId]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("更新后台用户信息失败：" . $e->getMessage());
            return ['code' => 0, "msg" => "update user failed", "data" => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }
}
