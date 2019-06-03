import store from '@/store';
// 判断是否有权限
export function has_permission(permissions) {
    if (store.state.userinfo.account == 'admin') {
        return true;
    }
    if (typeof permissions == 'string') {
        permissions = [permissions];
    }
    for (let i in permissions) {
        for (let j in store.state.userinfo.permission) {
            if (permissions[i] == store.state.userinfo.permission[j]) {
                return true;
            }
        }
    }
    return false;
}