<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class AdminUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * 校验用户名密码
     * @param $username
     * @param $password
     * @return \Illuminate\Database\Eloquent\Builder|Model|object
     */
    public static function authUser($username, $password)
    {
        $user = self::query()->where("username", $username)->where("password", md5($password))->first();
        if (empty($user)) {
            throw new ModelNotFoundException("username or password error");
        }

        return $user;
    }


    public static function getAllUsers($search = '')
    {
        return DB::table("admin_users as u")
            ->leftJoin("admin_role_users as ru", "u.id", "=", "ru.user_id")
            ->leftJoin("admin_roles as r", "ru.role_id", "=", "r.id")
            ->select(['u.id', 'u.username', 'u.name', 'u.tel', 'u.last_login_at', 'u.state', 'r.name as roleName'])
            ->when($search, function ($query) use ($search) {
                $query->where('u.username', 'like', '%' . $search . '%');
            })
            ->get();
    }

    public static function getUser($userId)
    {
        return self::query()->where("id", $userId)->first();
    }

    public static function deleteUser($userId)
    {
        return self::query()->where("id", $userId)->delete();
    }

    public function roles()
    {

    }
}
