<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminRoleMenu;
use App\Models\Admin\AdminRoleUser;
use App\Models\Admin\AdminUser;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input("userId");
        $user = AdminUser::query()->where("id", $userId)->select("avatar")->first();
        $role = AdminRoleUser::getUserRole($userId);
        $menu = AdminRoleMenu::getRoleMenus($role->id);

        $returnData = [
            "avatar" => $user->avatar,
            "data" => $menu->pluck("title")->toArray(),
            "role" => $role->name,
        ];

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => convert2Camel($returnData)];
    }
}
