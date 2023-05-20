<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminRole extends Model
{
    use HasFactory;

    public static function getRoles($request)
    {
        $page = $request->input('page', 0);
        $pageSize = $request->input('pageSize', 10);
        $search = $request->input("search");

        $model = self::query();

        $result = [];
        if (empty($page)) {
            return $model->get();
        }

        $cloneModel = clone $model;
        $total = $cloneModel->count();
        $roles = $model->offset(($page - 1) * $pageSize)->limit($pageSize)->get();

        $roleIds = $roles->pluck("id")->toArray();

        $menuData = DB::table("admin_role_menu as arm")
            ->leftJoin("admin_menu as am", "arm.menu_id", "=", "am.id")
            ->whereIn("arm.role_id", $roleIds)
            ->get()
            ->groupBy("role_id");

        $res = $roles->map(function ($item) use ($roles, $menuData) {
            return array_merge($item->toArray(), ['menus' => data_get($menuData, "{$item["id"]}")]);
        })->all();

        return ['code' => 1, 'msg' => 'SUCCESS', 'data' => ['total' => $total, 'data' => convert2Camel($res)]];
    }

    public function menus()
    {
        return $this->hasMany('App\Models\Admin\AdminRoleMenu', 'role_id', 'id')->select(["id", "title"]);
    }
}
