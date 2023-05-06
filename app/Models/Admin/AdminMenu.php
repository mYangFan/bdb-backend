<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    use HasFactory;

    protected $table = "admin_menu";


    public static function getMenuTree()
    {
        $allMenus = self::all()->toArray();
        $func = function ($allMenus, $parentId = null) use (&$func) {
            $tree = [];
            foreach ($allMenus as $menu) {
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
