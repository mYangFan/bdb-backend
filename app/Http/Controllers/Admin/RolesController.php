<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRoleMenu;
use App\Models\Admin\AdminRoleUser;
use App\Models\Admin\AdminUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
}
