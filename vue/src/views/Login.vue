<template>
  <div class="Login">
        <a-row>
            <a-col
                :xs="{
                    span: 20,
                    offset: 2
                }"
                :sm="{
                    span: 20,
                    offset: 2
                }"
                :md="{
                    span: 20,
                    offset: 2
                }"
                :lg="{
                    span: 16,
                    offset: 4
                }"
                :xl="{
                    span: 8,
                    offset: 8
                }"
                :xxl="{
                    span: 8,
                    offset: 8
                }"
                >
                <div class="title">
                    <h2>后台管理系统</h2>
                </div>
            </a-col>
        </a-row>
        <a-row>
            <a-col
                :xs="{
                    span: 20,
                    offset: 2
                }"
                :sm="{
                    span: 20,
                    offset: 2
                }"
                :md="{
                    span: 20,
                    offset: 2
                }"
                :lg="{
                    span: 16,
                    offset: 4
                }"
                :xl="{
                    span: 8,
                    offset: 8
                }"
                :xxl="{
                    span: 8,
                    offset: 8
                }"
                >
                <a-form
                    :form="loginForm"
                    class="login-form"
                    >
                        <a-form-item 
                            label="账号"
                            :label-col="formItemLayout.labelCol"
                            :wrapper-col="formItemLayout.wrapperCol"
                        >
                            <a-input
                            v-decorator="[
                                'account',
                                {rules: [{ required: true, message: '请输入账号' }, {min: 5, max: 20, message: '账号长度5-20位'} ]}
                            ]"
                            placeholder="请输入账号"
                            type="text"
                            @pressEnter="login"
                            />
                        </a-form-item>
                        <a-form-item 
                            label="密码"
                            :label-col="formItemLayout.labelCol"
                            :wrapper-col="formItemLayout.wrapperCol"
                        >
                            <a-input
                            v-decorator="[
                                'password',
                                {rules: [{ required: true, message: '请输入密码' }, {min: 6, max: 20, message: '密码长度6-20位'} ]}
                            ]"
                            placeholder="请输入密码"
                            type="password"
                            @pressEnter="login"
                            />
                        </a-form-item>
                        <a-form-item
                            :wrapper-col="{ span: 8, offset: 8 }"
                            >
                            <a-button
                                type="primary"
                                @click="login"
                                :loading="loginLoading"
                            >
                                登陆
                            </a-button>
                        </a-form-item>
                    </a-form>
            </a-col>
        </a-row>
  </div>
</template>

<script>
const formItemLayout = {
  labelCol: { span: 8 },
  wrapperCol: { span: 10 },
};
export default {
  name: 'login',
  components: {
  },
  data() {
    return {
        formItemLayout,

        // 登陆表单
        loginForm: this.$form.createForm(this),
        // 登陆加载框
        loginLoading: false,
        // 
    }
  },
  watch:{
  },
  mounted() {
  },
  methods: {
      login: function() {
          this.loginForm.validateFields((err, values) => {
              if (!err) {
                  this.loginLoading = true;
                  this.$api.user_login({
                      ...values
                  }).then(resp => {
                      this.loginLoading = false;
                      if (resp.code == 200) {
                          this.$message.success(resp.message);
                          this.$store.dispatch('setToken', resp.payload.token);
                          this.$store.dispatch('setUserInfo', resp.payload.user);
                          this.loginForm.resetFields();
                          this.$router.push({
                              name: 'User'
                          });
                      }
                  })
              }
          });
      }
  }
}
</script>

<style lang="less" scoped>
    .Login {
        position: absolute;
        height: 100%;
        width: 100%;
        .title {
            text-align: center;
            padding: 50px 0;
        }
        .login-form {
        }
    }
</style>