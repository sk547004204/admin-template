<template>
  <div id="app">
      <router-view></router-view>
  </div>
</template>

<style lang="less">
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  height: 100%;
  #components-layout-custom-trigger {
    height: 100%;
    .trigger {
      font-size: 18px;
      line-height: 64px;
      padding: 0 24px;
      cursor: pointer;
      transition: color .3s;
      height: 100%;
    }
    .trigger:hover {
      color: #1890ff;
    }
    .logo {
      height: 32px;
      background: rgba(255,255,255,.2);
      margin: 16px;
      line-height: 32px;
      color: white;
      text-align: center;
    }
  }
  .ant-pagination {
    margin: 20px 0px;
  }
  .page-search-form {
    padding: 24px;
    background: rgba(250, 250, 250, .4);
    border: 1px solid rgba(240, 240, 240, .8);
    border-radius: 6px;
    .ant-row {
      margin: 10px 0;
    }
    .ant-form-item {
      display: flex;
    }
    .ant-form-item-control-wrapper {
      flex: 1;
    }
  }
}

</style>
