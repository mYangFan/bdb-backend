<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    protected $table = 'user';

    protected $fillable = ['open_id', 'nick_name', 'avatar_uri', 'guide', 'max_level', 'location'];
    use HasFactory;

    public static function getUserByOpenId($openId)
    {
        return self::query()->where("open_id", $openId)->first();
    }

    public static function findOrCreateUser(Request $request, string $openId)
    {
//        $nickname = $request->input("nickname");
//        $location = $request->input("location");
//        $avatar = $request->input("avatar");
        //是否是新用户
        $guide = self::getUserByOpenId($openId);
        if (!empty($guide)) {
            return $guide;
        } else {
            return self::query()->create(['open_id' => $openId]);
        }
    }

    public static function createUser($openId, $nickName, $avatar, $location)
    {
        return DB::table("user")->insert([
            "open_id"   => $openId,
            "nick_name" => $nickName,
            "avatar"    => $avatar,
            "guide"     => 0,
            "location"  => $location,
        ]);
    }

    public static function updateUserGuide($userId)
    {
        return self::query()->where("id", "$userId")->update(['guide' => 1]);
    }

    public static function authUser(Request $request)
    {
        $nickname = $request->input("nickname");
        $avatar = $request->input("avatar");
        $location = $request->input("location");

        $userId = $request->input("userId");
        $user = self::query()->where('id', $userId)->first();
        if (!$user) {
            return ['code' => 1, 'msg' => '用户不存在', 'data' => null];
        }

        $result = $user->update(['nick_name' => $nickname, 'avatar_uri' => $avatar, 'location' => $location]);

        if ($result) {
            return ['code' => 1, 'mag' => 'SUCCESS', 'data' => null];
        }
    }
}
