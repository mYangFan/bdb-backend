<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\AdminRoleUser;
use App\Models\Admin\AdminUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsersController
{

    public function index(Request $request)
    {

        $users = AdminUser::getAllUsers($request);

        return ['code' => 1, 'msg' => 'SUCCESS', "data" => convert2Camel($users)];
    }

    public function createUser(Request $request)
    {
        $username = $request->input("username");
        $name = $request->input("name");
        $roleId = $request->input("roleId");
        $tel = $request->input("tel");

        $isExistUser = AdminUser::query()->where("username", $username)->exists();
        if ($isExistUser) {
            return ['code' => 0, 'msg' => '用户名已存在', 'data' => null];
        }

        $now = Carbon::now()->format('Y-m-d H:i:s');
        try {
            //插入用户
            $user = new AdminUser();
            $user->username = $username;
            $user->name = $name;
            $user->tel = $tel;
            $user->password = md5(123456);
            $user->created_at = $now;
            $user->updated_at = $now;

            $user->save();

            AdminRoleUser::query()->insert(['user_id' => $user->id, "role_id" => $roleId, 'created_at' => $now, 'updated_at' => $now]);
        } catch (\Exception $e) {
            Log::error("添加后台账户出错： " . $e->getMessage());
            return ['code' => 0, 'msg' => '添加用户失败，请稍后重试', 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }

    public function updateUser(Request $request, $userId)
    {
        $roleId = $request->input("roleId");
        $tel = $request->input("tel");
        $name = $request->input("name");
        $state = $request->input("state");

        $password = $request->input("password");

        $user = AdminUser::getUser($userId);

        $updateFields = [];
        if ($tel) {
            $updateFields = array_merge($updateFields, ['tel' => $tel]);
        }

        if ($name) {
            $updateFields = array_merge($updateFields, ['name' => $name]);
        }

        if ($state) {
            $updateFields = array_merge($updateFields, ['state' => $state]);
        }

        //重置密码
        if ($password) {
            $updateFields = array_merge($updateFields, ['password' => md5(123456)]);
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
            return ['code' => 0, "msg" => "更新用户信息失败", "data" => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }

    public function modifyPassword(Request $request)
    {
        $userId = $request->input("userId");
        $oldPassword = $request->input("oldPassword");
        $password = $request->input("password");

        $user = AdminUser::query()->where('id', $userId)->where('password', md5($oldPassword))->first();

        if (empty($user)) {
            return ['code' => 0, 'msg' => '密码不正确', 'data' => null];
        }

        try {
            $user->update(['password' => md5($password)]);
        } catch (\Exception $e) {
            Log::info("修改后台用户密码失败： " . $e->getMessage());
            return ['code' => 0, 'msg' => '修改密码失败', 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];

    }

    public function deleteUser(Request $request, $id)
    {
        $isAdmin = DB::table("admin_role_users as aru")
            ->leftJoin("admin_roles as ar", "aru.role_id", "=", "ar.id")
            ->where("aru.user_id", $id)
            ->where("ar.name", "超级管理员")
            ->exists();

        if ($isAdmin) {
            return ['code' => 0, 'msg' => '超级管理员禁止删除', 'data' => null];
        }

        $result = AdminUser::deleteUser($id);

        if (!$result) {
            return ['code' => 0, 'msg' => '删除用户失败', 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }
}
