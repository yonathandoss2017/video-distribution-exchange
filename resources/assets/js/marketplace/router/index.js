import Vue from 'vue'
import Router from 'vue-router'
const _import = file => (resolve) => require(['@/views/'+ file + '.vue'],resolve).default

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: '/marketplace',
  routes: [
    {
      path: '/',
      name: 'index',
      component: (resolve) => require(['@/views/index.vue'],resolve)
    },
    {
      path: '/allContentCategories',
      name: 'allContentCategories',
      component: (resolve) => require(['@/views/all_content_categories.vue'],resolve)
    },
    {
      path: '/ContentListDetails',
      name: 'ContentListDetails',
      component: (resolve) => require(['@/views/content_list_details.vue'],resolve)
    },
    {
      path: '/videoDetails',
      name: 'videoDetails',
      component: (resolve) => require(['@/views/video_details.vue'],resolve)
    },
    {
      path: '/allChannel',
      name: 'allChannel',
      component: (resolve) => require(['@/views/channels/all_channels.vue'],resolve)
    },
    {
      path: '/channelDetails',
      name: 'channelDetails',
      component: (resolve) => require(['@/views/channels/channel_details.vue'],resolve)
    },
    {
      path: '/mySubscription',
      name: 'mySubscription',
      component: (resolve) => require(['@/views/my/my_subscription.vue'],resolve)
    },
    {
      path: '/requestList',
      name: 'requestList',
      component: (resolve) => require(['@/views/my/request_list.vue'],resolve)
    },
    {
      path: '/protocol',
      name: 'protocol',
      component: (resolve) => require(['@/views/notices/protocol.vue'],resolve)
    },
    {
      path: '/help',
      name: 'help',
      component: (resolve) => require(['@/views/notices/help.vue'],resolve)
    },
    {
      path: '/about',
      name: 'about',
      component: (resolve) => require(['@/views/notices/about.vue'],resolve)
    },

  ]
})
