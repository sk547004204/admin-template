<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->input('token', '');
        if ($token === '') {
            return \Utils\Response::response(
                \Utils\ErrorCode::PERMISSION_LOSED,
                '身份凭证缺失，无法验证请求'
            );
        }
        
        $user = \App\Services\Token::findByToken($token);

        $request->globalUser = $user;
        
        return $next($request);
    }
}
