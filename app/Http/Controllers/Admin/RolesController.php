<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRoleMenu;
use App\Models\Admin\AdminRoleUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        return AdminRole::getRoles($request);
    }

    public function addRole(Request $request)
    {
        $name = $request->input("name");
        $menuIds = $request->input("menus");


        $now = Carbon::now()->format('Y-m-d H:i:s');
        $role = new AdminRole();
        $role->name = $name;
        $role->created_at = $now;
        $role->updated_at = $now;

        $role->save();

        $roleMenus = [];
        foreach ($menuIds as $menuId) {
            $roleMenus[] = ["role_id" => $role->id, "menu_id" => $menuId, 'created_at' => $now, 'updated_at' => $now];
        }

        AdminRoleMenu::query()->insert($roleMenus);

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }

    public function deleteRole($id)
    {
        $role = AdminRole::query()->where("id", $id)->first();
        if ($id->name == "超级管理员") {
            return ['code' => 0, 'msg' => '超级管理员禁止删除', 'data' => null];
        }

        $roleUser = AdminRoleUser::query()->where(['role_id' => $id])->first();
        if (!empty($roleUser)) {
            return ['code' => 0, 'msg' => '该角色已存在用户，请先解除所有与改角色的用户绑定关系', 'data' => null];
        }

        try {
            AdminRoleMenu::query()->where("role_id", $id)->delete();
            AdminRole::query()->where("id", $id)->delete();
        } catch (\Exception $e) {
            Log::error("删除角色失败" . $e->getMessage());
            return ['code' => 0, 'msg' => '删除角色失败', 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }

    public function authRole(Request $request, $id)
    {
        $menuIds = $request->input("menuIds", []);
        $role = AdminRole::query()->where("id", $id)->first();
        if (empty($role)) {
            return ['code' => 0, 'msg' => '授权角色不存在，授权失败', 'data' => null];
        }

        DB::beginTransaction();

        try {
            //先把所有的角色绑定关系全部删除
            $deleteResult = AdminRoleMenu::query()->where("role_id", $id)->delete();

            $insertData = [];
            $nowDate = Carbon::now()->format("Y-m-d H:i:s");
            foreach ($menuIds as $menuId) {
                $insertData[] = ['role_id' => $id, 'menu_id' => $menuId, 'created_at' => $nowDate, 'updated_at' => $nowDate];
            }

            //重新添加新的对应关系
            AdminRoleMenu::query()->insert($insertData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("角色授权失败： " . $e->getMessage());
            return ['code' => 0, 'msg' => $e->getMessage(), 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }
}
