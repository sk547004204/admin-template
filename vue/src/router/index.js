import store from '@/store';
import Vue from 'vue';
import Router from 'vue-router';
import User from '@/views/Admin/User';
import Group from '@/views/Admin/Group';
import Login from '@/views/Login';
import Admin from '@/views/Admin';

Vue.use(Router)

const routes = [
    {
      path: '/',
      name: 'Login',
      component: Login,
      meta: {
      }
    },
    {
      path: '/admin',
      name: 'Admin',
      component: Admin,
      meta: {
        auth: true,
      },
      children: [
        {
          path: '/user',
          name: 'User',
          component: User,
          meta: {
            auth: true
          }
        },
        {
          path: '/group',
          name: 'Group',
          component: Group,
          meta: {
            auth: true
          }
        }
      ]
    },
    
  ];


const router = new Router({
  mode: 'history',
  routes: routes
});

// 页面刷新时，重新赋值token
if (window.localStorage.getItem('token')) {
  store.dispatch('reload');
} else {
  store.dispatch('logout');
}

// 前置拦截
router.beforeEach((to, from, next) => {

  // 登录拦截
  if (to.matched.some(r => r.meta.auth)) {
    if (store.state.token) {
      next();
    } else {
      next({
        name: 'Login'
      })
      return;
    }
  }
  next()
})

router.afterEach(function (to) {
})

export default router