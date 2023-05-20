<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminMenu;
use Illuminate\Http\Request;

class MenusController extends Controller
{
    public function menuTree(Request $request)
    {
        $roleId = $request->input("roleId");
        $data = AdminMenu::getMenuTree($roleId);

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => convert2Camel($data)];
    }
}
