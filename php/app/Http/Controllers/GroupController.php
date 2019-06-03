<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositorys\{
    GroupRepository as Group
};
use App\Exceptions\LogicException;

class GroupController extends Controller
{
    /**
     * 创建群组
     * 
     */
    public function create(Request $request)
    {
        $params = $request->only([
            'name', 'intro', 'permission'
        ]);

        $this->validate($request, [
            'name' => 'required|string',
            'intro' => 'string',
            'permission' => 'required|array'
        ]);

        $permission = json_encode(
            $params['permission'] ?? []
        );

        $group = Group::create(
            $params['name'],
            $params['intro'] ?? '',
            $permission
        );

        if (!$group) {
            throw new LogicException(
                '服务器异常'
            );
        }
        
        return \Utils\Response::success();
    }
    
    /**
     * 修改群组
     * 
     */
    public function update(Request $request)
    {
        $params = $request->only([
            'id', 'name', 'intro', 'permission'
        ]);

        $this->validate($request, [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'intro' => 'string',
            'permission' => 'required|array'
        ]);

        $permission = json_encode(
            $params['permission'] ?? []
        );

        $group = Group::find($params['id']);
        if (!$group) {
            throw new LogicException(
                '无法找到分组信息',
                \Utils\ErrorCode::GROUP_NOT_FOUND
            );
        }
    
        $result = Group::update(
            $params['id'],
            $params['name'],
            $params['intro'],
            $permission
        );
        if (!$result) {
            throw new LogicException(
                '服务器异常'
            );
        }
        
        return \Utils\Response::success();
    }

    /**
     * 删除群组
     * 
     */
    public function delete(Request $request)
    {
        $params = $request->only([
            'id'
        ]);

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        $group = Group::find($params['id']);
        if (!$group) {
            throw new LogicException(
                '无法找到分组信息',
                \Utils\ErrorCode::GROUP_NOT_FOUND
            );
        }
        $result = Group::delete($params['id']);
        if (!$result) {
            throw new LogicException(
                '服务器异常'
            );
        }

        return \Utils\Response::success();
    }

    /**
     * 获取群组列表
     * 
     */
    public function paginate(Request $request)
    {
        $params = $request->only([
            'page', 'pagesize', 'key'
        ]);

        $this->validate($request, [
            'page' => 'numeric',
            'pagesize' => 'numeric',
            'key' => 'string'
        ]);

        $page = $params['page'] ?? 1;
        $pagesize = $params['pagesize'] ?? 20;
        $key = $params['key'] ?? '';

        $count = Group::count($key);
        $groups = [];
        if ($count > 0) {
            $groups = Group::paginate($page, $pagesize, $key);
        }

        $list = [];
        foreach ($groups as $group) {
            $formatPermissions = [];
            $permissions = json_decode($group->permission);
            foreach ($permissions as $permission) {
                foreach (\App\Constants::$permissions as $systemPermission) {
                    if ($permission == $systemPermission['value']) {
                        $formatPermissions[] = $systemPermission;
                        break;
                    }
                }
            }
            $list[] = [
                'id' => $group->id,
                'name' => $group->name,
                'permission' => $formatPermissions,
                'time' => $group->created_at->timestamp
            ];
        }

        return \Utils\Response::success([
            'page' => $page,
            'pagesize' => $pagesize,
            'count' => $count,
            'list' => $list
        ]);
    }

    /**
     * 获取所有群组列表
     * 
     */
    public function list(Request $request)
    {
        $params = $request->only([
            'key'
        ]);

        $this->validate($request, [
            'key' => 'string'
        ]);

        $key = $params['key'] ?? '';

        $groups = Group::get($key);

        $list = [];
        foreach ($groups as $group) {
            $list[] = [
                'id' => $group->id,
                'name' => $group->name,
                'intro' => $group->intro,
                'time' => $group->created_at->timestamp
            ];
        }

        return \Utils\Response::success([
            'list' => $list
        ]);
    }

    /**
     * 获取权限分组
     * 
     */
    public function get_permission(Request $request)
    {
        $permissions = \App\Constants::$permissions;
        return \Utils\Response::success([
            'list' => $permissions
        ]);
    }
}