<?php
namespace App\Repositorys;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Group,
    UserGroup
};

class GroupRepository
{
    /**
     * 创建分组
     * 
     * @param string $name 分组名
     * @param string $intro 分组简介
     * @param string $permission 权限
     * 
     */
    public static function create(string $name, string $intro, string $permission)
    {
        $group = new Group;
        $group->name = $name;
        $group->intro = $intro;
        $group->permission = $permission;
        if (!$group->save()) {
            return false;
        }
        return $group;
    }

    /**
     * 修改分组
     * 
     * @param int $group_id 分组ID
     * @param string $name 分组名
     * @param string $intro 分组简介
     * @param string $permission 权限
     * 
     */
    public static function update(int $group_id, string $name, string $intro, string $permission)
    {
        $group = Group::where('id', $group_id)->first();
        $group->name = $name;
        $group->intro = $intro;
        $group->permission = $permission;
        if (!$group->save()) {
            return false;
        }
        return $group;
    }

    /**
     * 获取数量
     * 
     * @param string $key 搜索关键词
     * 
     */
    public static function count(string $key = '')
    {
        return Group::where(function($query) use($key) {
            if ($key !== '')
            {
                $query->where('name', 'like', "%$key%")
                        ->where('intro', 'like', "%$key%");
            }
        })->count();
    }

    /**
     * 获取列表
     * 
     * @param int $page 页码
     * @param int $pagesize 每页大小
     * @param string $key 关键词
     * 
     */
    public static function paginate(int $page, int $pagesize, string $key = '')
    {
        [$limit, $offset] = \Utils\Util::formatPaginate($page, $pagesize, config('sys.max_pagesize', 50));

        return Group::where(function($query) use ($key) {
            if ($key !== '')
            {
                $query->where('name', 'like', "%$key%")
                        ->where('intro', 'like', "%$key%");
            }
        })->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();
    }

    /**
     * 获取所有列表
     * 
     * @param string $key 关键词
     * 
     */
    public static function get(string $key = '')
    {
        return Group::where(function($query) use ($key) {
            if ($key !== '')
            {
                $query->where('name', 'like', "%$key%")
                        ->where('intro', 'like', "%$key%");
            }
        })->orderBy('created_at', 'desc')->get();
    }

    /**
     * 删除分组
     * 
     */
    public static function delete(int $group_id)
    {
        DB::beginTransaction();
        $group_result = Group::where('id', $group_id)->delete();
        $user_group_result = UserGroup::where('group_id', $group_id)->delete();

        if ($group_result > -1 && $user_group_result > -1) {
            DB::commit();
            return true;
        }

        DB::rollBack();
        return false;
    }

    /**
     * 获取分组信息
     * 
     */
    public static function find(int $group_id)
    {
        return Group::where('id', $group_id)->first();
    }
}