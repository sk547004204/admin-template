<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->post('/login', ['uses' => 'AuthController@login']);

$router->group(['middleware' => 'admin.auth'], function($router) {
    // 退出登录
    $router->post('/logout', ['uses' => 'AuthController@logout']);
    /** 用户管理接口  */
    $router->group(['prefix' => 'user'], function($router) {
        // 添加用户
        $router->post('/create', ['uses' => 'UserController@create', 'middleware' => ['admin.permission:user.edit']]);
        // 修改用户密码
        $router->post('/update/password', ['uses' => 'UserController@update_password', 'middleware' => ['admin.permission:user.update_password']]);
        // 修改用户状态
        $router->post('/update/status', ['uses' => 'UserController@update_status', 'middleware' => ['admin.permission:user.edit']]);
        // 获取用户列表
        $router->get('/paginate', ['uses' => 'UserController@paginate', 'middleware' => ['admin.permission:user.see']]);
        // 为用户设置分组
        $router->post('/bind/group', ['uses' => 'UserController@bind_group', 'middleware' => ['admin.permission:user.bind_group']]);
    });
    /** 分组管理接口  */
    $router->group(['prefix' => 'group'], function($router) {
        // 添加分组
        $router->post('/create', ['uses' => 'GroupController@create', 'middleware' => ['admin.permission:group.edit']]);
        // 修改分组
        $router->post('/update', ['uses' => 'GroupController@update', 'middleware' => ['admin.permission:group.edit']]);
        // 删除分组
        $router->post('/delete', ['uses' => 'GroupController@delete', 'middleware' => ['admin.permission:group.delete']]);
        // 获取分组列表
        $router->get('/paginate', ['uses' => 'GroupController@paginate', 'middleware' => ['admin.permission:group.see']]);
        // 获取所有分组列表
        $router->get('/list', ['uses' => 'GroupController@list', 'middleware' => ['admin.permission:group.see']]);
        // 获取权限列表
        $router->get('/permission/list', ['uses' => 'GroupController@get_permission', 'middleware' => ['admin.permission:group.edit']]);
    });
});
