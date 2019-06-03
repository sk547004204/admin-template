<template>
  <div class="Group">
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
                placeholder="名称"
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
        <a-row v-if="$util.has_permission('group.edit')">
          <a-col
            :span="24"
            :style="{ textAlign: 'left' }"
          >
            <a-button
              type="primary"
              @click="createGroup"
            >
              创建分组
            </a-button>
          </a-col>
        </a-row>
      </a-form>
      <a-divider orientation="left"></a-divider>

      <a-skeleton :loading="loading" active>
        <div class="group-list">
          <a-row :gutter="24">
            <a-col
              :span="8"
              :xs="24"
              :sm="24"
              :lg="12"
              :md="24"
              :xl="12"
              :xxl="8"
              v-for="(item, index) in list" :key="index"
            >
              <a-card :title="item.name" :bordered="true" :style="{ margin: '10px 0'}">
                <a href="#" slot="extra" :style="{margin: '0 10px'}" @click="updateGroup(item, index)" v-if="$util.has_permission('group.edit')">编辑</a>
                <template slot="extra" v-if="$util.has_permission('group.delete')">
                  <a-popconfirm title="您确定删除这个分组吗？" @confirm="deleteConfirm(item, index)" okText="确认" cancelText="算啦">
                    <a href="javascript: ;">删除</a>
                  </a-popconfirm>
                </template>
                <p :style="{height: '80px', overflow: 'hidden'}">
                  <a-tag :style="{ margin: '10px 5px'}" :color="permission.color" v-for="(permission, index) in item.permission" :key="index">{{permission.label}}</a-tag>
                </p>
              </a-card> 
            </a-col>
          </a-row>
        </div>
      </a-skeleton>
      <a-pagination :pageSize.sync="pageSize" :total="count" v-model="page"/>

      <a-modal
        title="创建分组"
        v-model="createGroupVisible"
        :maskClosable=false
        @cancel="createGroupCancel"
        >
          <a-form
            :form="createGroupForm"
          >
            <a-form-item 
              label="分组名称"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
              <a-input
                v-decorator="[
                  'name',
                  {rules: [{ required: true, message: '请填写分组名称' }, {min: 2, max: 20, message: '分组名称建议2-20位长度'} ]}
                ]"
                placeholder="长度为2-20位"
                type="text"
                @pressEnter="createGroupConfirm"
              />
            </a-form-item>
            <a-form-item 
              label="简介"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
              <a-input
                v-decorator="[
                  'intro',
                  {rules: [{min: 1, max: 100, message: '建议1-100字符'} ]}
                ]"
                placeholder="建议长度：100字符以内"
                type="textarea"
                @pressEnter="createGroupConfirm"
              />
            </a-form-item>

            <a-form-item 
              label="权限信息"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
            <div class="permissionList" :style="{ margin: '0px 10px' }">
                <a-checkbox-group :options="permissionList" v-model="checkedList" :style="{ padding: '5px 0px' }" />
            </div>
            </a-form-item>
          </a-form>
          <template slot="footer">
              <a-button key="submit" type="primary" :loading="createGroupLoading" @click="createGroupConfirm">
                  添加
              </a-button>
          </template>
      </a-modal>


      <a-modal
        title="编辑分组"
        v-model="updateGroupVisible"
        :maskClosable=false
        @cancel="updateGroupCancel"
        >
          <a-form
            :form="updateGroupForm"
          >
            <a-form-item 
              label="分组名称"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
              <a-input
                v-decorator="[
                  'name',
                  {
                    rules: [{ required: true, message: '请填写分组名称' }, {min: 2, max: 20, message: '分组名称建议2-20位长度'} ]
                  }
                ]"
                placeholder="长度为2-20位"
                type="text"
                @pressEnter="updateGroupConfirm"
              />
            </a-form-item>
            <a-form-item 
              label="简介"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
              <a-input
                v-decorator="[
                  'intro',
                  {rules: [{min: 1, max: 100, message: '建议1-100字符'} ]}
                ]"
                placeholder="建议长度：100字符以内"
                type="textarea"
                @pressEnter="updateGroupConfirm"
              />
            </a-form-item>

            <a-form-item 
              label="权限信息"
              :label-col="formItemLayout.labelCol"
              :wrapper-col="formItemLayout.wrapperCol"
            >
            <div class="permissionList" :style="{ margin: '0px 10px' }">
                <a-checkbox-group :options="permissionList" v-model="checkedList" :style="{ padding: '5px 0px' }" />
            </div>
            </a-form-item>
          </a-form>
          <template slot="footer">
              <a-button key="submit" type="primary" :loading="updateGroupLoading" @click="updateGroupConfirm">
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
export default {
  name: 'Group',
  data() {
    return {
      formItemLayout,
      page: 1,
      pageSize: 10,
      key: '',
      count: 0,
      list: [],
      loading: false,

      // 权限列表
      permissionList: [],
      // 被选中的权限列表
      checkedList: [],
      // 删除分组加载框
      deleteLoading: false,


      // 创建分组开关
      createGroupVisible: false,
      // 创建分组加载框
      createGroupLoading: false,
      // 创建分组表单
      createGroupForm: this.$form.createForm(this),

      // 修改分组开关
      updateGroupVisible: false,
      // 修改分组加载框
      updateGroupLoading: false,
      // 修改分组表单
      updateGroupForm: this.$form.createForm(this),
      // 选中分组信息
      selectGroup: undefined,

    }
  },
  mounted() {
      this.$store.dispatch('setSubMenu', ['system']);
      this.$store.dispatch('setMenu', ['system-group']);

      this.paginate();
      if (this.$util.has_permission('group.edit')) {
        this.getPermission();
      }
  },
  methods: {
    // 创建分组
    createGroup: function() {
      this.createGroupVisible = true;
    },
    // 创建分组请求
    createGroupConfirm: function() {
      this.createGroupForm.validateFields((err, values) => {
        if (!err) {
          this.createGroupLoading = true;
          this.$api.group_create({
            ...values,
            permission: this.checkedList
          }).then(resp => {
            this.createGroupLoading = false;
            if (resp.code == 200) {
              this.$message.success(resp.message);
              this.list.unshift(values);
              this.count++;
              this.createGroupCancel();
            }
          });
        }
      });
    },
    // 取消创建分组
    createGroupCancel: function() {
      this.createGroupVisible = false;
      this.createGroupForm.resetFields();
      this.checkedList = [];
      this.paginate();
    },

    // 修改分组
    updateGroup: function(group, index) {
      this.selectGroup = group;
      this.updateGroupVisible = true;
      // 初始化表单
      this.updateGroupForm.getFieldDecorator('name', {
        initialValue: this.selectGroup.name || ''
      });
      this.updateGroupForm.getFieldDecorator('intro', {
        initialValue: this.selectGroup.intro || ''
      });
      this.checkedList = this.selectGroup.permission.map((permission, index) => {
        return permission.value;
      });
    },
    // 修改分组请求
    updateGroupConfirm: function() {
      this.updateGroupForm.validateFields((err, values) => {
        if (!err) {
          this.updateGroupLoading = false;
          this.$api.group_update({
            ...values,
            id: this.selectGroup.id,
            permission: this.checkedList
          }).then(resp => {
            this.updateGroupLoading = false;
            if (resp.code == 200) {
              this.$message.success(resp.message);
              this.selectGroup = undefined;
              this.updateGroupCancel();
            }
          });
        }
      })
    },
    // 取消修改分组
    updateGroupCancel: function() {
      this.selectGroup = undefined;
      this.updateGroupVisible = false;
      this.checkedList = [];
      this.paginate();
    },

    deleteConfirm: function(item, index) {
      this.deleteLoading = true;
      this.$api.group_delete({
        id: item.id
      }).then(resp => {
        this.deleteLoading = false;
        if (resp.code == 200) {
          this.$message.success(resp.message);
          this.list.splice(index, 1);
        }
      });
    },

    // 获取分组信息
    paginate: function() {
      this.loading = true;
      this.$api.group_paginate({
        page: this.page,
        pagesize: this.pageSize,
        key: this.key
      }).then(resp => {
        this.loading = false;
        if (resp.code == 200) {
          this.list = resp.payload.list;
          this.count = resp.payload.count;
        }
      });
    },

    // 搜索
    search: function() {
      this.page = 1;
      this.paginate();
    },

    // 获取权限列表
    getPermission: function() {
      this.$api.group_permission_list({}).then(resp => {
        if (resp.code == 200) {
          this.permissionList = resp.payload.list;
        }
      })
    }
  },
}
</script>