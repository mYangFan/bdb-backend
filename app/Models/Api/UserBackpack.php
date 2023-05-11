<?php

namespace App\Models\Api;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserBackpack extends Model
{
    use HasFactory;

    protected $table = "user_backpack";
    protected $fillable = ["item_id", "item_num", "user_id", "state"];

    public static function getBackpackByUser($userId)
    {
        return DB::table("user_backpack as ubp")
            ->leftJoin("backpack_item as bpi", "ubp.item_id", "=", "bpi.id")
            ->where("ubp.user_id", $userId)
            ->select(["bpi.item_id", "ubp.item_num", "ubp.user_id", "ubp.state", "bpi.item_name"])
            ->get()
            ->keyBy("item_id");
    }

    public static function updateItem($userId, $itemId, $count)
    {
        $itemAutoId = Backpack::query()
            ->where(['item_id' => $itemId])
            ->select("id")
            ->first();
        if (empty($itemAutoId)) {
            return false;
        }

        $item = self::query()->where("user_id", $userId)
            ->where("item_id", $itemAutoId->id)
            ->select(["id", "item_num"])
            ->firstOrNew();

        $timeNow = Carbon::now()->format('Y-m-d H:i:s');
        if (empty($item->id) && $count > 0) {
            return $item->insert(["item_id" => $itemAutoId->id, "item_num" => $count, "user_id" => $userId, "created_at" => $timeNow, "updated_at" => $timeNow]);
        }
        $newCount = $item->item_num + $count;
        return $item->update(["item_num" => $newCount, "updated_at" => $timeNow]);
    }
}
