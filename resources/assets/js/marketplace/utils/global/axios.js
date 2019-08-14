import axios from 'axios'

//请求拦截器
// http request 拦截器

axios.defaults.timeout = 20000
axios.defaults.headers.post['Content-Type'] = 'application/json'
axios.defaults.headers.get['Content-Type'] = 'application/json'
// axios.defaults.baseURL = '/api'
axios.interceptors.request.use(
  config => {
    let Authorization=window.localStorage.getItem('token')
    if (Authorization) {  // 判断是否存在token，如果存在的话，则每个http header都加上token
      config.headers.Authorization =Authorization;
    }
    return config;
  },
  err => {
    return Promise.reject(err);
  });
//添加响应拦截器
axios.interceptors.response.use(function(response){
  //对响应数据做些事
  return response;
},function(error){
  //请求错误时做些事
  return Promise.reject(error);
});

let urlFront = '/marketplace/api'
export function getPlayList (parameter) {
  return axios({
    url: urlFront+'/query'+ parameter,
    method: 'get',
  })
}

export function getChannels (parameter) {
  return axios({
    url: urlFront+'/property'+ parameter,
    method: 'get',
  })
}
export function property (parameter) {
  return axios({
    url: urlFront+'/property'+ parameter,
    method: 'get',
  })
}

export function subscribe (parameter) {
  return axios({
    url: urlFront+'/property/'+ parameter,
    method: 'post',
  })
}

export function getplaylist (parameter) {
  return axios({
    url: urlFront+'/playlist'+ parameter,
    method: 'get',
  })
}

export function Entry (parameter) {
  return axios({
    url: urlFront+'/entry'+ parameter,
    method: 'get',
  })
}
export function Subscription (parameter) {
  return axios({
    url: urlFront+'/subscription'+ parameter,
    method: 'get',
  })
}
export function setLocale (parameter) {
  return axios({
    url: urlFront+'/set-locale'+ parameter,
    method: 'get',
  })
}

export function Car (parameter) {
  return axios({
    url: urlFront+'/cart'+ parameter,
    method: 'get',
  })
}

export function CarAdd (parameter) {
  return axios({
    url: urlFront+'/cart/add',
    method: 'post',
    data:parameter
  })
}

export function CarDelete (parameter) {
  return axios({
    url: urlFront+'/cart/remove',
    method: 'delete',
    params:parameter
  })
}

export function CarCheckout (parameter) {
  return axios({
    url: urlFront+'/cart/checkout',
    method: 'post',
    data:parameter
  })
}

export function getSPuser (parameter) {
  return axios({
    url: urlFront+'/property/sp/manage'+parameter,
    method: 'get',
  })
}




