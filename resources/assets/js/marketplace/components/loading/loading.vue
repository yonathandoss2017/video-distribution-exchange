<template>
  <div class="mainBox" v-if="showLoading">
    <div><img src="/static/icon/loadingWhite.png" alt=""></div>
  </div>
</template>
<script>
  export default {
    data(){
      return{

      }
    },
    props:{
      showLoading:{
        type: Boolean,
        default(){
          return true
        }
      },
    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .mainBox{
        position: fixed;
        top:0;
        left:0;
        width: 100%;
        height:100%;
        background: rgba(0,0,0,0.20);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index:1000;
        div{
          /*background: pink;*/
          animation:rotating 1.5s linear infinite;
          width: 100/100rem;
          height:100/100rem;
          img{
            width: 100/100rem;
            height:100/100rem;
            float: left;
          }
        }
        @keyframes rotating{
          from{transform:rotate(0)}
          to{transform:rotate(360deg)}
        }
      }
    }
    @media  (min-width:1301px){
      .mainBox{
        position: fixed;
        top:0;
        left:0;
        width: 100%;
        height:100%;
        background: rgba(0,0,0,0.20);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index:1000;
        div{
          /*background: pink;*/
          animation:rotating 1.5s linear infinite;
          width: 100px;
          height:100px;
          img{
            width: 100px;
            height:100px;
            float: left;
          }
        }
        @keyframes rotating{
          from{transform:rotate(0)}
          to{transform:rotate(360deg)}
        }
      }


    }
  }

</style>
