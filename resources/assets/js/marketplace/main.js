// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import VueI18n from 'vue-i18n'
import Vuex from 'vuex'
import store from './vuex/store.js'
import VueAwesomeSwiper from 'vue-awesome-swiper'

import VueClipboard from 'vue-clipboard2'  //复制
Vue.use(VueClipboard)

Vue.use(Vuex)
Vue.use(VueAwesomeSwiper)
import global_ from  './utils/global/index.vue'
Vue.use(VueI18n)
Vue.config.productionTip = false
Vue.prototype.Gloal = global_

//适配
var clientWidth = document.documentElement.clientWidth
var relativeWidth = clientWidth/1440
var fontSize =100 * relativeWidth
document.documentElement.style.fontSize = fontSize + "px"
//多语言
const i18n = new VueI18n({
  locale: store.state.lanage !='' ? store.state.lanage : document.documentElement.lang.substr(0,2),    // 语言标识
  messages: {
    'zh': require('./utils/lang/zh'),   // 中文语言包
    'en': require('./utils/lang/en'),   // 英文语言包
  },
})


/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  i18n,
  store,
  components: { App },
  template: '<App/>'
})
