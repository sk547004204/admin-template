<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * 连接名称
     * 
     */
    // protected $connection = 'mysql';
    /**
     * 数据表名
     * 
     */
    protected $table = 'group';

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
     * 获取群组的用户信息
     * 
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->using(UserGroup::class);
    }
}