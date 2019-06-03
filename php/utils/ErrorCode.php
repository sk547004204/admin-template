<?php
namespace Utils;

class ErrorCode
{
    const SUCCESS = 200;
    const PERMISSION_LOSED = 401; // 没有令牌
    const PERMISSION_DIED = 403; // 令牌无效
    const ERROR = 500; // 普通错误
    const PLATFORM_ERROR = 1001; // 第三方错误
    const REDIS_ERROR = 1002; // 缓存错误
    const VALIDATOR_ERROR = 1003; // validator校验器错误
    const DEBUG_ERROR = 1004; // DEBUG的错误信息
    const DB_ERROR = 1005; // DB异常
    const PERMISSION_NOT_ALLOWED = 1006;


    const USER_NOT_FOUND = 10001; // 没有找到用户
    const GROUP_NOT_FOUND = 10002; // 没有找到群组


    const USER_EXISTS = 20001; // 用户已存在
    

    const PASSWORD_INVALID = 30001; // 密码不正确
}