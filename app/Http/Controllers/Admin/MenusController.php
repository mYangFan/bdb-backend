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
        dd(AdminMenu::getMenuTree($roleId));
    }
}
