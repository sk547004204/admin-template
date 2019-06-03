import axios from 'axios';
import store from '@/store';
import Message from 'ant-design-vue/lib/message';

axios.defaults.timeout = 30000;
axios.defaults.baseURL = 'http://localhost:8081';

// 处理错误
function checkStatus(response) {
    if (response.status >= 200 && response.status < 300) {
        return response;
    }
    const errortext = codeMessage[response.status] || response.statusText;
    Message.info(`请求错误 ${response.status}: ${response.url}`);

    const error = new Error(errortext);
    error.name = response.status;
    error.response = response;
    throw error;
}
function checkException(e) {
    Message.error(`网络请求处理异常`);
}

function checkResponse(response){
    if (response.data) {
        // 大于1000 的是业务错误，弹框
        switch (response.data.code) {
            case 200:
                return response.data;
                break;
            case 401:
                // 清空state
                store.dispatch('logout');
                break;
            case 403: 
                // 清空state
                store.dispatch('logout');
                break;
            case 10000:
                let message = '';
                let errors = response.data.payload.errors;
                for(let errorIndex in errors)
                {
                    for (let index in errors[errorIndex])
                    {
                        message += errors[errorIndex][index];
                    }
                }

                Message.info(`[${response.data.code}] ${response.data.message} ${message}`);

                break;
            default:
                Message.error(`[${response.data.code}] ${response.data.message}`);
                break;
        }
        return response.data;
    }
}

export default function request(url, options)
{
    // 判断本地是否存储有Token，自动携带
    if (store.state.token)
    {
        let token = store.state.token;
        let data = options.method.toLowerCase() == 'get' ? 'params' : 'data';
        if (!options[data]) options[data] = {};
        options[data].token = token;
    }

    options.url = url;
    return axios.request(options)
            .then(checkStatus)
            .then(checkResponse)
            .catch(checkException);
}