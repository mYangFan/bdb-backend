<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminRoleMenu extends Model
{
    use HasFactory;

    public static function getRoleMenus($roleId)
    {
        return DB::table("admin_role_menu as rm")
            ->leftJoin("admin_menu as m", "rm.menu_id", "=", "m.id")
            ->where("rm.role_id", $roleId)
            ->select(["m.id", "m.parent_id", "m.title"])
            ->get();
    }
}
