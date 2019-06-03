import request from '@/request';

export async function user_login(data) {
    return request('/login', {
        data,
        method: 'post'
    });
}

export async function user_logout(data) {
    return request('/logout', {
        data,
        method: 'post'
    });
}

export async function user_paginate(params) {
    return request('/user/paginate', {
        params,
        method: 'get'
    });
}

export async function user_create(data) {
    return request('/user/create', {
        data,
        method: 'post'
    })
}
export async function user_update_password(data) {
    return request('/user/update/password', {
        data,
        method: 'post'
    })
}
export async function user_update_status(data) {
    return request('/user/update/status', {
        data,
        method: 'post'
    })
}
export async function user_bind_group(data) {
    return request('/user/bind/group', {
        data,
        method: 'post'
    })
}

export async function group_permission_list(params) {
    return request('/group/permission/list', {
        params,
        method: 'get'
    });
}
export async function group_paginate(params) {
    return request('/group/paginate', {
        params,
        method: 'get'
    });
}
export async function group_list(params) {
    return request('/group/list', {
        params,
        method: 'get'
    });
}
export async function group_create(data) {
    return request('/group/create', {
        data,
        method: 'post'
    })
}
export async function group_update(data) {
    return request('/group/update', {
        data,
        method: 'post'
    })
}
export async function group_delete(data) {
    return request('/group/delete', {
        data,
        method: 'post'
    })
}