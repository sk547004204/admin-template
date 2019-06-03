<?php
namespace App\Repositorys;
use Illuminate\Support\Facades\DB;
use App\Models\UserGroup;
use Illuminate\Support\Carbon;

class UserGroupRepository
{
    /**
     * 创建
     * 
     */
    public static function set(int $user_id, array $group_ids)
    {
        DB::beginTransaction();

        $result = UserGroup::where('user_id', $user_id)->delete();
        if ($result < 0) {
            DB::rollBack();
            return false;
        }

        $inserts = [];
        foreach ($group_ids as $group_id)
        {
            $inserts[] = [
                'user_id' => $user_id,
                'group_id' => $group_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }

        $result = UserGroup::insert(
            $inserts
        );
        if (!$result) {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }
}