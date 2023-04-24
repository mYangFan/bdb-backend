<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;

class UsersController
{
    public function index()
    {
        $users = AdminUser::getAllUsers();

        return ['code' => 0, 'msg' => '', "data" => $users];
    }
}
