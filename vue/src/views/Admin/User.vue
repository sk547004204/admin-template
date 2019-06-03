<template>
  <div class="User">
      <a-form
        class="page-search-form"
      >
        <a-row :gutter="24">
          <a-col
            :span="8"
            :xs="24"
            :sm="24"
            :lg="8"
            :md="8"
            :xl="4"
            :xxl="4"
          >
            <a-form-item label="">
              <a-input
                placeholder="账号"
                v-model="key"
                @pressEnter="search"
              />
            </a-form-item>
          </a-col>
        </a-row>
        <a-row>
          <a-col
            :span="24"
            :style="{ textAlign: 'left' }"
          >
            <a-button
              type="primary"
              @click="search"
            >
              搜索
            </a-button>
          </a-col>
        </a-row>
        <a-row v-if="$util.has_permission('user.see')">
          <a-col
            :span="24"
            :style="{ textAlign: 'left' }"
          >
            <a-button
              type="primary"
              @click="createUser"
            >
              添加账号
            </a-button>
          </a-col>
        </a-row>
      </a-form>

      <a-divider orientation="left"></a-divider>
      <a-skeleton :loading="loading" active>
        <a-list
          class="demo-loadmore-list"
          itemLayout="horizontal"
          :dataSource="list"
        >
          <a-list-item slot="renderItem" slot-scope="item, index">
            <a slot="actions" @click="updatePassword(index)" v-if="$util.has_permission('user.update_password')">修改密码</a>
            <a slot="actions" @click="bindGroup(index)" v-if="$util.has_permission('user.bind_group')">授予权限</a>
            <a-list-item-meta>
              <a slot="title" href="javascript: ;">{{item.account}}</a>
              <a-avatar slot="avatar" src="https://zos.alipayobjects.com/rmsportal/ODTLcjxAfvqbxHnVXCYX.png" />
            </a-list-item-meta>
            <div v-if="$util.has_permission('user.edit')">
              <a-switch :value="(item.status ? true : false)" :loading="selectIndex == index && statusLoading" checkedChildren="启用" unCheckedChildren="禁用" @change="updateStatus($event, index)"/>
            </div>
          </a-list-item>
        </a-list>
      </a-skeleton>
      <a-pagination :pageSize.sync="pageSize" :total="count" v-model="page"/>

      <a-modal
        title="修改密码"
        v-model="updatePasswordVisible"
        :maskClosable=false
        @cancel="updatePasswordCancel"
        >
          <a-form
            :form="updatePasswordForm"
          >
            <a-form-item 
              label="新密码"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
              <a-input
                v-decorator="[
                  'password',
                  {rules: [{ required: true, message: '请设置新密码' }, {min: 6, max: 20, message: '密码建议6-20位长度'} ]}
                ]"
                placeholder="长度为6-20位"
                type="password"
                @pressEnter="updatePasswordConfirm"
              />
            </a-form-item>
          </a-form>
          <template slot="footer">
              <a-button key="submit" type="primary" :loading="updatePasswordLoading" @click="updatePasswordConfirm">
                  确认修改
              </a-button>
          </template>
      </a-modal>

      <a-modal
        title="创建账号"
        v-model="createUserVisible"
        :maskClosable=false
        @cancel="createUserCancel"
        >

          <a-form
            :form="createUserForm"
          >
            <a-form-item 
              label="账号"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
              <a-input
                v-decorator="[
                  'account',
                  {rules: [{ required: true, message: '请填写账号' }, {min: 5, max: 20, message: '账号建议5-20位长度'} ]}
                ]"
                placeholder="长度为5-20位"
                type="text"
                @pressEnter="createUserConfirm"
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
                  {rules: [{ required: true, message: '请填写密码' }, {min: 6, max: 20, message: '密码建议5-20位长度'} ]}
                ]"
                placeholder="长度为6-20位"
                type="password"
                @pressEnter="createUserConfirm"
              />
            </a-form-item>
            <a-form-item 
              label="状态"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
              <a-switch v-decorator="['status', { valuePropName: 'checked' }]" />
            </a-form-item>
          </a-form>
          <template slot="footer">
              <a-button key="submit" type="primary" :loading="createUserLoading" @click="createUserConfirm">
                  添加
              </a-button>
          </template>
      </a-modal>

      <a-modal
        title="账号授权"
        v-model="bindGroupVisible"
        @cancel="bindGroupCancel"
        >
          <a-form
            >        
            <a-form-item 
              label="分组列表"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
            <div class="groupList" :style="{ margin: '0px 10px' }">
                <a-checkbox-group :options="groupList" v-model="checkedGroupList" :style="{ padding: '5px 0px' }" />
            </div>
            </a-form-item>
          </a-form>
          <template slot="footer">
              <a-button key="submit" type="primary" :loading="bindGroupLoading" @click="bindGroupConfirm">
                  确认修改
              </a-button>
          </template>
      </a-modal>
  </div>
</template>

<script>
const formItemLayout = {
  labelCol: { span: 4 },
  wrapperCol: { span: 16 },
};
const STATUS = ['正常', '冻结'];
export default {
  name: 'home',
  components: {
  },
  data() {
    return {
      formItemLayout,

      loading: true,
      key: '',
      list: [],
      statusText: STATUS,
      count: 0,
      pageSize: 10,
      page: 1,
      selectIndex: undefined,

      // 分组列表
      groupList: [],
      // 选中分组列表
      checkedGroupList: [],
      // 修改状态加载框
      statusLoading: false,

      // 修改密码模态框开关
      updatePasswordVisible: false,
      // 修改密码加载效果
      updatePasswordLoading: false,
      // 修改密码表单
      updatePasswordForm: this.$form.createForm(this),

      // 设置权限模态框开关
      bindGroupVisible: false, 
      // 设置权限加载效果
      bindGroupLoading: false, 


      // 创建账号开关
      createUserVisible: false,
      // 创建账号加载效果
      createUserLoading: false,
      // 创建账号表单
      createUserForm: this.$form.createForm(this),
    }
  },
  watch:{
  },
  mounted() {
    this.$store.dispatch('setSubMenu', ['system']);
    this.$store.dispatch('setMenu', ['system-user']);
    this.paginate();
    if (this.$util.has_permission('user.bind_group')) {
      this.getGroup();
    }
  },
  methods: {
    paginate: function() {
      this.$api.user_paginate({
        page: this.page,
        pagesize: this.pagesize,
        key: this.key
      }).then(resp => {
        if (resp.code == 200) {
          this.list = resp.payload.list;
          this.count = resp.payload.count;
          this.loading = false;
        }
      });
    },
    search: function() {
      this.page = 1;
      this.paginate();
    },
    getGroup: function() {
      this.$api.group_list({}).then(resp => {
        if (resp.code == 200) {
          this.groupList = resp.payload.list.map((val, index) => {
            return {
              label: val.name,
              value: val.id
            }
          });
        }
      })
    },
    updateStatus: function(checked, index) {
      this.selectIndex = index;
      this.statusLoading = true;
      this.$api.user_update_status({
        user_id: this.list[index].id,
        status: checked ? 1 : 0
      }).then(resp => {
        if (resp.code == 200) {
          this.statusLoading = false;
        }
      })
    },

    // 修改密码
    updatePassword: function(index) {
      this.selectIndex = index;
      this.updatePasswordVisible = true;
    },
    // 执行修改密码操作
    updatePasswordConfirm: function() {
      this.updatePasswordForm.validateFields((err, values) => {
        if (!err) {
            this.updatePasswordLoading = true;
            let item = this.list[this.selectIndex];
            this.$api.user_update_password({
              user_id: item.id,
              ...values
            }).then(resp => {
              this.updatePasswordLoading = false;
              if (resp.code == 200) {
                this.$message.success(resp.message);
                this.updatePasswordCancel();
              }
            });
        }
      })
    },
    // 关闭修改密码的框
    updatePasswordCancel: function() {
      this.selectIndex = undefined;
      this.updatePasswordVisible = false;
      this.updateupdatePasswordForm.resetFields();
    },

    // 分组授权
    bindGroup: function(index) {
      this.selectIndex = index;
      this.bindGroupVisible = true;
      this.checkedGroupList = this.list[index].groups.map((val, index) => {
        return val.id;
      });
    },
    bindGroupConfirm: function() {
      this.bindGroupLoading = true;
      this.$api.user_bind_group({
        user_id: this.list[this.selectIndex].id,
        group_ids: this.checkedGroupList
      }).then(resp => {
        if (resp.code == 200) {
          this.$message.success(resp.message);
          this.bindGroupCancel();
        }
      })
    },
    // 关闭分组授权
    bindGroupCancel: function() {
      this.selectIndex = undefined;
      this.bindGroupVisible = false;
      this.bindGroupLoading = false;
      this.checkedGroupList = [];
      this.paginate();
    },

    // 创建账号
    createUser: function() {
      this.createUserVisible = true;
    },
    // 执行创建账号
    createUserConfirm: function() {
      this.createUserForm.validateFields((err, values) => {
        if (!err) {
          this.createUserLoading = true;
          values.status = values.status ? 1 : 0;
          this.$api.user_create({
            ...values
          }).then(resp => {
            this.createUserLoading = false;
            if (resp.code == 200) {
              this.$message.success(resp.message);
              this.list.unshift(values);
              this.count++;
              this.createUserCancel();
            }
          });
        }
      });
    },
    // 关闭创建账号窗口
    createUserCancel: function() {
      this.createUserVisible = false;
      this.createUserForm.resetFields();
    },
  },
}
</script>

<style lang="less" scoped>
</style>