<template>
  <div id="app">
    <router-view :key="$route.fullPath" v-if="isRouterAlive"/>
  </div>
</template>

<script>
export default {
  name: 'App',
  provide () {
    return {
      reload: this.reload
    }
  },
  data () {
    return {
      isRouterAlive: true
    }
  },
  watch: {
    '$route': function(to,from){
      document.body.scrollTop = 0
      document.documentElement.scrollTop = 0
    }
  },
  methods: {
    reload () {
      this.isRouterAlive = false
      this.$nextTick(function () {
        this.isRouterAlive = true
      })
    }
  }
}
</script>

<style lang="less" rel="stylesheet/less">
  body{
    margin:0;
    padding:0;
    background: #f9f9f9;
  }
#app {
  margin:0;
  padding:0;
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  /*margin-top: 60px;*/
}
</style>
