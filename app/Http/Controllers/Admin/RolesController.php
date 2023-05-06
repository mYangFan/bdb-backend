<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminRoleMenu;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index()
    {
        $roles = AdminRole::all();

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => convert2Camel($roles)];
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
}
