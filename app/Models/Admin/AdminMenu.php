<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminMenu extends Model
{
    use HasFactory;

    protected $table = "admin_menu";


    public static function getMenuTree($roleId)
    {
        $allMenus = self::all()->toArray();

        $roleMenus = DB::table("admin_role_menu")
            ->where("role_id", $roleId)
            ->select("menu_id")
            ->pluck("menu_id")
            ->toArray();

        $func = function ($allMenus, $parentId = null) use (&$func, $roleMenus) {
            $tree = [];
            foreach ($allMenus as $menu) {
                if (in_array($menu['id'], $roleMenus)) {
                    $menu['isn'] = 1;
                }

                if ($menu['parent_id'] == $parentId) {
                    $children = $func($allMenus, $menu['id']);
                    if ($children) {
                        $menu['children'] = $children;
                    }
                    $tree[] = $menu;
                }
            }

            return $tree;
        };

        return $func($allMenus);
    }
}
