<template>
  <div class="admin">
      <a-layout id="components-layout-custom-trigger">
        <a-layout-sider
          :trigger="null"
          collapsible
          v-model="collapsed"
        >
          <div class="logo">后台管理系统</div>
          <a-menu theme="dark" mode="inline" @click="menuClick" :openKeys.sync="openMenuData.subMenu" :selectedKeys="openMenuData.menu">
            <template v-for="item in menu">
              <template v-if="$util.has_permission(item.permission)">
                <a-sub-menu v-if="item.sub" :key="item.key" :disabled="item.disabled">
                  <span slot="title">
                    <a-icon :type="item.icon" v-if="item.icon != ''"/> 
                    <span>{{item.title}}</span>
                  </span>
                  <template v-for="subItem in item.list">
                    <template v-if="$util.has_permission(subItem.permission)">
                      <a-menu-item :key="item.key + '-' + subItem.key" :disabled="subItem.disabled">{{subItem.title}}</a-menu-item>
                    </template>
                  </template>
                </a-sub-menu>
              </template>
            </template>
          </a-menu>
        </a-layout-sider>
        <a-layout>
          <a-layout-header style="background: #fff; padding: 0; display: flex; flex-direction: row; justify-content: space-between;">
            <a-icon
              class="trigger"
              :type="collapsed ? 'menu-unfold' : 'menu-fold'"
              @click="()=> collapsed = !collapsed"
            />
            <div class="display: flex; flex-direction: row;">
              <span class="trigger" style="padding: 0 5px;">{{userinfo.account}}</span>
              <a-icon
                class="trigger"
                type="logout"
                @click="logout"
              />
            </div>
          </a-layout-header>
          <a-layout-content :style="{ margin: '24px 16px', padding: '24px', background: '#fff', minHeight: '280px' }">
            <router-view></router-view>
          </a-layout-content>
        </a-layout>
      </a-layout>
  </div>
</template>
<style lang="less">
    .admin {
        height: 100%;
    }
</style>
<script>
import menu from '@/json/menu.json';
import { mapState } from 'vuex';
export default {
  data(){
    return {
      collapsed: false,
      menu: menu,
      activeRoute: ''
    }
  },
  watch: {
    "$route": function(oldVal, newVal) {
      this.activeRoute = newVal.name;
    }
  },
  computed: {
    ...mapState(['openMenuData', 'userinfo'])
  },
  mounted() {
    this.activeRoute = this.$route.name;
  },
  methods: {
    menuClick: function(item) {
      let pathIndexs = item.key.split('-');
      let subMenuIndex = this.menu.findIndex(m => m.key == pathIndexs[0]);
      let menu = this.menu[subMenuIndex];
      for (let i = 1; i < pathIndexs.length; i++)
      {
        let menuIndex = menu.list.findIndex(m => m.key == pathIndexs[i]);
        menu = menu.list[menuIndex];
      }
      if (typeof(menu) != undefined && menu.link != '') {
        this.$router.push({
          name: menu.link
        })
      }
    },
    logout: function() {
      let that = this;
      this.$confirm({
        title: '您确定要退出账号吗？',
        content: '',
        okText: '确定',
        cancelText: '点错了',
        onOk() {
          that.$api.user_logout().then(resp => {
            if (resp.code == 200) {
              that.$store.dispatch('logout');
              that.$router.replace({
                name: 'Login'
              })
            }
          })
        }
      })

    }
  },
}
</script>
