<?php
namespace App\Repositorys;
use App\Models\{
    User
};

class UserRepository
{
    /**
     * 获取用户信息
     * 
     * @param int $user_id 用户ID
     * 
     */
    public static function find(int $user_id)
    {
        return User::where([
            'id' => $user_id
        ])->first();
    }

    /**
     * 获取用户信息
     * 
     * @param string $account 账号
     * 
     */
    public static function findByAccount(string $account)
    {
        return User::with(['groups'])->where([
            'account' => $account
        ])->first();
    }

    /**
     * 创建用户
     * 
     * @param string $account 账号
     * @param string $password 密码
     * @param bool $status 状态
     * 
     */
    public static function create(string $account, string $password, bool $status)
    {
        $user = new User;
        $user->account = $account;
        $user->password = $password;
        $user->status = $status;
        if (!$user->save()) {
            return false;
        }
        return $user;
    }

    /**
     * 修改用户
     * 
     * @param int $user_id 用户ID
     * @param string $password 密码
     * 
     */
    public static function updatePassword(int $user_id, string $password)
    {
        $user = User::where('id', $user_id)->first();
        $user->password = $password;
        if (!$user->save()) {
            return false;
        }
        return $user;
    }

    /**
     * 修改状态
     * 
     * @param int $user_id 用户ID
     * @param bool $status 状态
     * 
     */
    public static function updateStatus(int $user_id, bool $status)
    {
        $user = User::where('id', $user_id)->first();
        $user->status = $status;
        if (!$user->save()) {
            return false;
        }
        return $user;
    }

    /**
     * 获取数量
     * 
     * @param string $account 账号
     * 
     */
    public static function count(string $account = '')
    {
        return User::where(function($query) use($account) {
            if ($account !== '')
            {
                $query->where('account', 'like', "%$account%");
            }
        })->count();
    }

    /**
     * 获取列表
     * 
     * @param int $page 页码
     * @param int $pagesize 每页大小
     * @param string $account 账号
     * 
     */
    public static function paginate(int $page, int $pagesize, string $account = '')
    {
        [$limit, $offset] = \Utils\Util::formatPaginate($page, $pagesize, config('sys.max_pagesize', 50));

        return User::with(['groups' => function($query) {
            // $query->select(['id', 'name']);
        }])->where(function($query) use ($account) {
            if ($account !== '') {
                $query->where('account', 'like', "%$account%");
            }
        })
        ->select(['id', 'account', 'status', 'created_at'])
        ->limit($limit)->offset($offset)->orderBy('created_at', 'desc')->get();
    }

    /**
     * 获取所有列表
     * 
     * @param string $account 账号
     * 
     */
    public static function get(string $account = '')
    {
        return User::where(function($query) use ($account) {
            if ($account !== '') {
                $query->where('account', 'like', "%$account%");
            }
        })->orderBy('created_at', 'desc')->get();
    }
}