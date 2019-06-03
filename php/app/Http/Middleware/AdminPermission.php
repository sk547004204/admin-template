<?php

namespace App\Http\Middleware;

use Illuminate\Support\Arr;
use App\Exceptions\LogicException;

use Closure;

class AdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, string $permission)
    {
        $user = $request->globalUser;

        $super_accounts = array_filter(explode('|', env('SUPER_ACCOUNT')));
        // 判断是否是超管
        if (!in_array($user->account, $super_accounts, true)) {
            // 开始校验权限
            if (!in_array($permission, $user->permission, true)) {
                throw new LogicException(
                    '您没有权限访问该资源',
                    \Utils\ErrorCode::PERMISSION_NOT_ALLOWED
                );
            }
        }

        return $next($request);
    }
}
