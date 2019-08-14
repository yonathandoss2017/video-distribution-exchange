<template>
  <div class="mainBox" v-if="isShow">
    <div class="main">
      <!--<div class="Delete" @click="deleteClose"><img src="/static/icon/closed.png" alt=""></div>-->
      <div class="row1">{{$t('requestList.deleteTitle')}}</div>
      <div class="row2"><span :style="lanage== 'zh'?'text-align: center;':'text-align: left;'">{{$t('requestList.deleteSubTitle')}}</span></div>
      <div class="row3">
        <div @click="deleteSubmit">{{$t('requestList.deleteDetermine')}}</div>
        <div @click="deleteClose">{{$t('requestList.deleteCancel')}}</div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    data(){
      return{

      }
    },
    props: {
      isShow:{
        type: Boolean,
        default(){
          return false
        }
      },


    },
    computed:{
      lanage(){
        return this.$store.state.lanage
      },
    },

    methods:{
      deleteClose(){
        this.$emit('deleteClose')
      },
      deleteSubmit(){
        this.$emit('deleteSubmit')
      },

    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .mainBox{
        z-index: 200;
        position: fixed;
        top:0;
        left:0;
        background: rgba(0,0,0,0.20);
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        .main{
          position: relative;
          width: 340/100rem;
          /*height: 246/100rem;*/
          padding-bottom: 25/100rem;
          background: #FFFFFF;
          box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
          .Delete{
            width: 15/100rem;
            height:15/100rem;
            position: relative;
            left:302/100rem;
            top:17/100rem;
            img{
              width: 15/100rem;
              height:15/100rem;
            }
          }
          .row1{
            width: 340/100rem;
            font-family: PingFangSC-Medium;
            font-weight:500;
            font-size: 18/100rem;
            color: #333333;
            letter-spacing: 0;
            text-align: center;
            margin-top:39/100rem;
            line-height:25/100rem;
          }
          .row2{
            margin-top:31/100rem;
            font-family: PingFangSC-Regular;
            font-size: 16/100rem;
            color: #666666;
            letter-spacing: 0;
            text-align: center;
            line-height:22/100rem;
            width: 340/100rem;
            span{
              width: 290/100rem;
              display: inline-block;
              text-align: left;
              word-break:normal;
            }
          }
          .row3{
            width: 340/100rem;
            margin-top:43/100rem;
            display: flex;
            div:nth-of-type(1){
              background: #BA0132;
              border-radius: 4/100rem;
              width: 111/100rem;
              height:46/100rem;
              margin-left:40/100rem;
              font-family: PingFangSC-Regular;
              font-size: 14/100rem;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: center;
              line-height:46/100rem;
              cursor: pointer;
            }
            div:nth-of-type(1):hover{
              background: #C1204A;
            }
            div:nth-of-type(2){
              margin-left:38/100rem;
              width: 109/100rem;
              height:44/100rem;
              border: 1/100rem solid #999999;
              border-radius: 4/100rem;
              font-family: PingFangSC-Regular;
              font-size: 14/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: center;
              line-height: 44/100rem;
              cursor: pointer;
            }
            div:nth-of-type(2):hover{
              background: #d8d8d8;
            }

          }

        }
      }
    }
    @media  (min-width:1301px){
      .mainBox{
        z-index: 200;
        position: fixed;
        top:0;
        left:0;
        background: rgba(0,0,0,0.20);
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        .main{
          position: relative;
          width: 340px;
          /*height: 246px;*/
          padding-bottom: 25px;
          background: #FFFFFF;
          box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
          .Delete{
            width: 15px;
            height:15px;
            position: relative;
            left:302px;
            top:17px;
            img{
              width: 15px;
              height:15px;
            }
          }
          .row1{
            width: 340px;
            font-family: PingFangSC-Medium;
            font-weight:500;
            font-size: 18px;
            color: #333333;
            letter-spacing: 0;
            text-align: center;
            margin-top:39px;
            line-height:25px;
          }
          .row2{
            margin-top:31px;
            font-family: PingFangSC-Regular;
            font-size: 16px;
            color: #666666;
            letter-spacing: 0;
            text-align: center;
            line-height:22px;
            width: 340px;
            span{
              width: 290px;
              display: inline-block;
              text-align: left;
              word-break:normal;
            }
          }
          .row3{
            width: 340px;
            margin-top:43px;
            display: flex;
            div:nth-of-type(1){
              background: #BA0132;
              border-radius: 4px;
              width: 111px;
              height:46px;
              margin-left:40px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: center;
              line-height:46px;
              cursor: pointer;
            }
            div:nth-of-type(1):hover{
              background: #C1204A;
            }
            div:nth-of-type(2){
              margin-left:38px;
              width: 109px;
              height:44px;
              border: 1px solid #999999;
              border-radius: 4px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #333333;
              letter-spacing: 0;
              text-align: center;
              line-height: 44px;
              cursor: pointer;
            }
            div:nth-of-type(2):hover{
              background: #d8d8d8;
            }

          }

        }
      }


    }
  }
</style>
