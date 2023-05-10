<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    public static function getRoles($request)
    {
        $page = $request->input('page', 0);
        $pageSize = $request->input('pageSize', 10);

        $model = self::query();

        if (empty($page)) {
            return $model->get();
        }

        return $model->offset(($page - 1) * $pageSize)->limit($pageSize)->get();
    }
}
