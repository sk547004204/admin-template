<?php
namespace Utils;

class Util
{
    /**
     * 生成密码
     * 
     */
    public static function encryptPassword(string $password, string $salt = '')
    {
        return sha1(md5($password) . $salt);
    }

    /**
     * 生成分页
     *
     * @param int $page 页码
     * @param int $pagesize 每页大小
     * @param int $max_pagesize 最大的每页大小
     * 
     **/
    public static function formatPaginate(int &$page, int &$pagesize, int $max_pagesize = 50)
    {
        $pagesize = $pagesize > $max_pagesize ? $max_pagesize : $pagesize;
        $offset = ($page - 1) * $pagesize;
        $limit = $pagesize;
        return [$limit, $offset];
    }

    /**
     * 建立报错数据
     * 
     */
    public static function buildException($e)
    {
        $trace = $e->getTraceAsString();
        $trace = explode("\n", $trace);
        return [
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getcode(),
            'message' => $e->getMessage(),
            'trace' => $trace
        ];
    }

    /**
     * 建立路由数据
     * 
     */
    public static function buildRequest($request)
    {
        return [
            'path' => $request->path(),
            'method' => $request->method(),
            'data' => $request->all(),
            'header' => $request->header()
        ];
    }
}