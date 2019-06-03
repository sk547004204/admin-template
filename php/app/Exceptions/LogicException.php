<?php

namespace App\Exceptions;

class LogicException extends \Exception
{
    /**
     * 构造函数
     * 
     */
    public function __construct(string $message = '', int $code = 500, array $payload = []) 
    {
        // 确保所有变量都被正确赋值
        parent::__construct($message, $code);
        $this->payload = $payload;
    }
}