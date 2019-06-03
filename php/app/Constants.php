<?php
namespace App;

class Constants
{
    public static $ADMIN_AUTH_TOKEN = 'admin:user:%s:token'; // 用户存储的Token值



    const USER_STATUS_ENABLE = 1; // 账号启用
    const USER_STATUS_DISABLE = 2; // 账号禁用

    public static $permissions = [
        [
            'label' => '用户查看',
            'value' => 'user.see',
            'intro' => '查看用户列表',
            'color' => '#108ee9'
        ],
        [
            'label'  => '用户编辑',
            'value'   => 'user.edit',
            'intro' => '包含添加用户、变更用户状态。',
            'color' => '#108ee9'
        ],
        [
            'label'  => '修改密码',
            'value'   => 'user.update_password',
            'intro' => '',
            'color' => '#108ee9'
        ],
        [
            'label'  => '授予权限',
            'value'   => 'user.bind_group',
            'intro' => '为用户授权，超管权限请勿随意指定。',
            'color' => '#108ee9'
        ],
        [
            'label' => '分组查看',
            'value' => 'group.see',
            'intro' => '查看分组列表',
            'color' => '#108ee9'
        ],
        [
            'label'  => '分组编辑',
            'value'   => 'group.edit',
            'intro' => '包含添加/修改分组，更改分组权限',
            'color' => '#108ee9'
        ],
        [
            'label'  => '分组删除',
            'value'   => 'group.delete',
            'intro' => '',
            'color' => '#108ee9'
        ]
    ];
}