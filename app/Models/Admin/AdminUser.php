<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminUser extends Model
{
    use HasFactory;

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


    public static function getAllUsers()
    {
        $users = self::query()->select(["id, username, "]);

        return $users;
    }

    public function roles()
    {
        
    }
}
