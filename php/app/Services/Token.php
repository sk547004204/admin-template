<?php
namespace App\Services;

use App\Exceptions\LogicException;
use Illuminate\Support\Facades\Redis;

class Token
{
    /**
     * 生成登录Token
     * 
     */
    public static function build(string $userinfo, string $increment)
    {
        $access_token = str_random(18);
        $key = sprintf(\App\Constants::$ADMIN_AUTH_TOKEN, $increment);

        $tokenResult = Redis::setex(
            $access_token,
            config('expire.ADMIN_AUTH_TOKEN'),
            $userinfo
        );
        $userResult = Redis::setex(
            $key,
            config('expire.ADMIN_AUTH_TOKEN'),
            $access_token
        );
        if (!$userResult || !$tokenResult) {
            throw new LogicException(
                '服务器异常',
                \Utils\ErrorCode::REDIS_ERROR
            );
        }

        return $access_token;
    }

    /**
     * 销毁登录Token
     * 
     */
    public static function destory(string $increment)
    {
        $key = sprintf(\App\Constants::$ADMIN_AUTH_TOKEN, $increment);
        $access_token = Redis::get($key);
        if ($access_token) {
            Redis::del($key);
            Redis::del($access_token);
        }
    }

    /**
     * 重置登录Token
     * 
     */
    public static function reset(string $access_token, string $userinfo)
    {
        $result = Redis::get($access_token);
        if ($result) {
            $rs = Redis::set($access_token, $userinfo);
            if (!$rs) {
                throw new LogicException(
                    '服务器异常',
                    \Utils\ErrorCode::REDIS_ERROR
                );
            }
        } else {
            throw new LogicException(
                '服务器异常',
                \Utils\ErrorCode::REDIS_ERROR
            );
        }
    }

    /**
     * 根据Token获取
     * 
     */
    public static function findByToken(string $access_token)
    {
        $result = Redis::get($access_token);
        if (!$result) {
            throw new LogicException(
                '登录凭证无效，请重新登录',
                \Utils\ErrorCode::PERMISSION_DIED
            );
        }
        return json_decode($result);
    }
}