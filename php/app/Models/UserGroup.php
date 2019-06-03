<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    /**
     * 数据表名
     * 
     */
    protected $table = 'user_group';

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
     * 用户信息
     * 
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    
    /**
     * 分组信息
     * 
     */
    public function group()
    {
        return $this->hasOne(Group::class, 'id', 'group_id');
    }
}