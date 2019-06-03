import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);
const store = new Vuex.Store({
    state: {
        selectTab: 0, // 选中的Tab页
        token: '', // Token
        userinfo: {}, // 用户信息
        openMenuData: { // 被设置展开的数组KEY
            subMenu: [],
            menu: []
        }
    },
    mutations: {
        changeTab: function(state, tabIndex) {
            state.selectTab = tabIndex;
        },
        setToken: function(state, token) {
            state.token = token;
        },
        setUserInfo: function(state, userinfo) {
            state.userinfo = userinfo;
        },
        setSubMenu: function(state, keys) {
            state.openMenuData.subMenu = keys;
        },
        setMenu: function(state, keys) {
            state.openMenuData.menu = keys;
        }
    },
    actions: {
        changeTab: function(store, tabIndex) {
            store.commit('changeTab', tabIndex);
        },
        setToken: function(store, token) {
            window.localStorage.setItem('token', token);
            store.commit('setToken', token);
        },
        setUserInfo: function(store, userinfo) {
            window.localStorage.setItem('userinfo', JSON.stringify(userinfo));
            store.commit('setUserInfo', userinfo);
        },
        logout: function(store) {
            store.commit('setToken', '');
            store.commit('setUserInfo', {});
        },
        setSubMenu: function(store, keys) {
            store.commit('setSubMenu', keys);
        },
        setMenu: function(store, keys) {
            store.commit('setMenu', keys);
        },
        reload: function(store) {
            let token = window.localStorage.getItem('token');
            store.commit('setToken', token);
            let userinfo = window.localStorage.getItem('userinfo');
            store.commit('setUserInfo', JSON.parse(userinfo));
        }
    }
})

export default store;