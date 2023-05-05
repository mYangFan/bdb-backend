<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminRoleUser extends Model
{
    use HasFactory;

    public static function getUserRole($userId)
    {
        return DB::table("admin_role_users as ru")
            ->leftJoin("admin_roles as r", "ru.role_id", "=", "r.id")
            ->where("ru.user_id", $userId)
            ->select(["r.id", "r.name"])
            ->first();
    }
}
