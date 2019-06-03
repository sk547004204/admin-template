<?php
namespace Utils;

class Response
{

    /**
     * 成功响应
     * 
     */
    public static function success(array $payload = [], int $code = ErrorCode::SUCCESS, $message = '操作成功')
    {
        return self::response(
            $code,
            $message,
            $payload
        );
    }

    public static function response($code, $message = '', $payload = [])
    {
        $type = JSON_UNESCAPED_UNICODE;
        if (count($payload) <= 0)
        {
            $type = JSON_UNESCAPED_UNICODE|JSON_FORCE_OBJECT;
        }
        $json = json_encode([
            'code' => $code,
            'message' => $message,
            'payload' => $payload
        ], $type);

        return response($json)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }
}