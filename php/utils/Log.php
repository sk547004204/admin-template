<?php
namespace Utils;
use GuzzleHttp\{
    Client
};

class Log
{
    private static $kv = [
        'danger' => 5,
        'error' => 4,
        'warning' => 3,
        'info' => 2,
        'debug' => 1
    ];

    public static function __callStatic($name, $arguments)
    {
        if (array_key_exists($name, self::$kv)) {
            if (($num = 4 - count($arguments)) > 0) {
                for ($i = 4 - $num; $i < 4; $i++) {
                    $arguments[$i] = '';
                }
            }
            array_push($arguments, self::$kv[$name]);
            return call_user_func_array('self::request', $arguments);
        } else {
            throw new \Exception('不存在的方法');
        }
    }

    /**
     * @param $msg 错误信息
     * @param $data JSON错误信息
     * @param $mark 备注信息
     * @param $module 模块
     * @param $level 报错等级 1-5依次递增
     * 
     */
    private static function request($msg, $data, $module, $mark, $level)
    {

        if (!is_array($data)) {
            $data = [];
        }

        $params = [
            'msg' => $msg,
            'intro' => json_encode($data),
            'level' => $level,
            'module' => $module,
            'mark' => $mark
        ];
        ksort($params);

        // 开始build字符串
        $str = '';
        foreach ($params as $key => $val) {
            $str .= "{$key}={$val}&";
        }
        $str = substr($str, 0, strlen($str)-1);
        $sign = md5(env('LOG_APPKEY').$str);

        // $sign = md5(env('LOG_APPKEY').http_build_query($params));

        $params['appid'] = env('LOG_APPID');
        $params['sign'] = $sign;

        try {
            $client = new Client([
                'base_uri' => env('LOG_DOMAIN'),
                'timeout' => 3
            ]);
            $result = $client->request('post', '/log/report', [
                'form_params' => $params
            ]);
            return $result->getBody()->getContents();
        } catch(\Exception $e) {
            // dd($e->getMessage());
            return '';
        }

    }
}