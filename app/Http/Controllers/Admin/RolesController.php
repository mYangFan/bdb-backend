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
//        $menuIds = $request->input("menus");


        $now = Carbon::now()->format('Y-m-d H:i:s');
        $roleModel = AdminRole::query()->where('name', $name)->first();
        if (!empty($roleModel)) {
            return ['code' => 0, 'msg' => '角色名不能重复', 'data' => null];
        }

        $roleModel = new AdminRole();
        $roleModel->name = $name;
        $roleModel->created_at = $now;
        $roleModel->updated_at = $now;

        if (!$roleModel->save()) {
            return ['code' => 0, "msg" => "新增角色失败，请稍后再试", 'data' => null];
        }

//        $roleMenus = [];
//        foreach ($menuIds as $menuId) {
//            $roleMenus[] = ["role_id" => $role->id, "menu_id" => $menuId, 'created_at' => $now, 'updated_at' => $now];
//        }
//
//        AdminRoleMenu::query()->insert($roleMenus);

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }

    public function deleteRole($id)
    {
        $role = AdminRole::query()->where("id", $id)->first();
        if ($role->name == "超级管理员") {
            return ['code' => 0, 'msg' => '禁止删除角色<超级管理员>', 'data' => null];
        }

        $roleUser = AdminRoleUser::query()->where(['role_id' => $id])->first();
        if (!empty($roleUser)) {
            return ['code' => 0, 'msg' => '该角色下已存在用户，请先解除所有与改角色的用户绑定关系,再进行删除操作', 'data' => null];
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

    public function updateRole(Request $request, $id)
    {
        $name = $request->input("name");

        $role = AdminRole::query()->where("id", $id)->first();
        if (empty($role)) {
            return ['code' => 0, 'msg' => '角色不存在，修改失败', 'data' => null];
        }

        if ($role->name == "超级管理员") {
            return ['code' => 0, 'msg' => "禁止修改角色<超级管理员>", 'data' => null];
        }

        $role->name = $name;
        if (!$role->save()) {
            return ['code' => 0, 'msg' => '修改失败', 'data' => null];
        }

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => null];
    }
}
