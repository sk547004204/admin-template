<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositorys\UserRepository;
use App\Exceptions\{
    LogicException
};
use App\Services\{
    Token
};
use function GuzzleHttp\json_decode;

class AuthController extends Controller
{
    /**
     * 登录
     * 
     */
    public function login(Request $request)
    {
        $params = $request->only([
            'account', 'password'
        ]);

        $this->validate($request, [
            'account' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!$user = UserRepository::findByAccount($params['account'])) {
            throw new LogicException(
                '账号或密码错误',
                \Utils\ErrorCode::USER_NOT_FOUND
            );
        }
        
        if ($user->password !== \Utils\Util::encryptPassword($params['password'])) {
            throw new LogicException(
                '账号或密码错误',
                \Utils\ErrorCode::PASSWORD_INVALID
            );
        }

        if ($user->status !== \App\Constants::USER_STATUS_ENABLE) {
            throw new LogicException(
                '用户状态异常，暂时无法登录',
                \Utils\ErrorCode::USER_INVALID
            );
        }

        unset($user->password);
        $permission = [];
        foreach ($user->groups as $group) {
            $permission = array_merge($permission, json_decode($group->permission));
        }
        $permission = array_filter($permission);
        $user->permission = $permission;
        unset($user->groups);
        $userStr = json_encode($user);
        // 生成Token
        $access_token = Token::build($userStr, $user->id);

        return \Utils\Response::success([
            'token' => $access_token,
            'expire' => config('expire.ADMIN_AUTH_TOKEN'),
            'user' => $user
        ]);
    }

    /**
     * 退出登录
     * 
     */
    public function logout(Request $request)
    {
        $user = $request->globalUser;
        Token::destory($user->id);
        return \Utils\Response::success();
    }
}