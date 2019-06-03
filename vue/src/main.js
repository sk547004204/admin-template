import Vue from 'vue'
import App from './App.vue'
import router from '@/router'
import store from '@/store'
import * as util from '@/util'
import './registerServiceWorker'

import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/antd.css'
Vue.use(Antd)


Vue.config.productionTip = false

import * as api from '@/request/api';
Vue.prototype.$api = api;
Vue.prototype.$util = util;

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
