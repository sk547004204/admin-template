<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositorys\{
    UserRepository as User,
    UserGroupRepository as UserGroup
};
use App\Exceptions\LogicException;

class UserController extends Controller
{

    /**
     * 创建账号
     *
     **/
    public function create(Request $request)
    {
        $params = $request->only([
            'account', 'password', 'status'
        ]);

        $this->validate($request, [
            'account' => 'required|string',
            'password' => 'required|string',
            'status' => 'required|integer|min:0|max:1'
        ]);

        // 如果账号已存在
        if (User::findByAccount($params['account'])) {
            throw new LogicException(
                '该账号已被占用',
                \Utils\ErrorCode::USER_EXISTS
            );
        }

        $user = User::create(
            $params['account'],
            \Utils\Util::encryptPassword($params['password']),
            $params['status']
        );
        if (!$user) {
            return \Utils\Response::response(
                \Utils\ErrorCode::ERROR,
                '服务器异常，请稍后重试'
            );
        }

        return \Utils\Response::success();
    }

    /**
     * 修改账号密码
     * 
     */
    public function update_password(Request $request)
    {
        $params = $request->only([
            'user_id', 'password'
        ]);

        $this->validate($request, [
            'user_id' => 'required|numeric',
            'password' => 'required|string'
        ]);

        // 查找用户信息
        if (!$user = User::find($params['user_id'])) {
            return \Utils\Response::response(
                \Utils\ErrorCode::USER_NOT_FOUND,
                '没有找到该用户'
            );
        }

        // 超管无法操作
        $super_accounts = explode('|', env('SUPER_ACCOUNT'));
        // 如果被修改的人是超管，那就必须超管来修改
        if (!in_array($request->globalUser->account, $super_accounts, true) && in_array($user->account, $super_accounts, true)) {
            throw new LogicException(
                '您无权修改该账号的信息',
                \Utils\ErrorCode::PERMISSION_NOT_ALLOWED
            );
        }

        // 修改密码
        $result = User::updatePassword(
            $params['user_id'],
            \Utils\Util::encryptPassword($params['password'])
        );
        if (!$result) {
            return \Utils\Response::response(
                \Utils\ErrorCode::ERROR,
                '服务器异常，请稍后重试'
            );
        }

        return \Utils\Response::success();
    }

    /**
     * 修改账号状态
     * 
     */
    public function update_status(Request $request)
    {
        $params = $request->only([
            'user_id', 'status'
        ]);

        $this->validate($request, [
            'user_id' => 'required|numeric',
            'status' => 'required|numeric|min:0|max:1'
        ]);

        // 查找用户信息
        if (!$user = User::find($params['user_id'])) {
            return \Utils\Response::response(
                \Utils\ErrorCode::USER_NOT_FOUND,
                '没有找到该用户'
            );
        }

        // 超管账号无法被操作
        $super_accounts = explode('|', env('SUPER_ACCOUNT'));
        // 如果被修改的人是超管，那就必须超管来修改
        if (in_array($user->account, $super_accounts, true)) {
            throw new LogicException(
                '您无权修改该账号的信息',
                \Utils\ErrorCode::PERMISSION_NOT_ALLOWED
            );
        }
        
        // 修改状态
        $result = User::updateStatus(
            $params['user_id'],
            $params['status']
        );
        if (!$result) {
            return \Utils\Response::response(
                \Utils\ErrorCode::ERROR,
                '服务器异常，请稍后重试'
            );
        }

        return \Utils\Response::success();
    }

    /**
     * 获取账号列表
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

        $count = User::count(
            $params['key'] ?? ''
        );
        $list = [];
        if ($count > 0) {
            $list = User::paginate(
                $params['page'] ?? 1,
                $params['pagesize'] ?? 20,
                $params['key'] ?? ''
            );
        }

        return \Utils\Response::success([
            'list' => $list,
            'count' => $count
        ]);
    }

    /**
     * 为用户绑定群组
     * 
     */
    public function bind_group(Request $request)
    {
        $params = $request->only([
            'user_id', 'group_ids'
        ]);

        $this->validate($request, [
            'user_id' => 'required|numeric',
            'group_ids' => 'required|array'
        ]);

        if (!$user = User::find($params['user_id'])) {
            throw new LogicException(
                '用户信息不存在',
                \Utils\ErrorCode::USER_NOT_FOUND
            );
        }
        
        // 超管账号无法被操作
        $super_accounts = explode('|', env('SUPER_ACCOUNT'));
        // 如果被修改的人是超管，那就必须超管来修改
        if (in_array($user->account, $super_accounts, true)) {
            throw new LogicException(
                '您无权修改该账号的信息',
                \Utils\ErrorCode::PERMISSION_NOT_ALLOWED
            );
        }
        // 修改用户的群组信息
        $result = UserGroup::set(
            $params['user_id'],
            $params['group_ids']
        );
        if (!$result) {
            throw new LogicException('服务器异常');
        }

        return \Utils\Response::success();
    }
}