<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * 数据表名
     * 
     */
    protected $table = 'user';

    /**
     * 该模型的主键名称
     *
     * @var string
     **/
    protected $primaryKey = 'id';

    /**
     * 该模型的主键类型是否为自增的Int值
     *
     * @var string
     **/
    public $incrementing = true;

    /**
     * 自动维护时间戳
     * 
     */
    public $timestamps = true;

    /**
     * 为属性设置默认值
     * 
     */
    protected $attributes = [
    ];

    /**
     * 获取用户的群组信息
     * 
     */
    public function groups()
    {
        // return $this->belong(Group::class)->using(UserGroup::class);
        return $this->belongsToMany(Group::class, UserGroup::class);
    }
}