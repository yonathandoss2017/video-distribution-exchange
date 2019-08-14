<template>
  <div class="mainBox">
    <div class="main">
      <div class="bottomLeft">
        <div class="phone">{{$t('global.contact_number')}}</div>
        <div class="phoneNum">{{$t('global.phone')}}</div>
        <div class="address">{{$t('global.address')}}</div>
        <div class="email">{{$t('global.email')}}</div>
      </div>
      <div class="bottomRight">
        <div class="requestList">
          <div @click="toAbout">{{$t('global.about')}}</div>
          <div @click="toHelp">{{$t('global.help')}}</div>
          <div @click="toAgreement">{{$t('global.Agreement')}}</div>
        </div>
        <div class="Filing">
          <a href="javascript:void(0)" style="cursor: initial;"><span>{{$t('global.copyright_1')}}</span></a>
          <div class="divder"></div>
          <a href="http://www.beian.miit.gov.cn"><span>{{$t('global.copyright_2')}}</span></a>
        </div>
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
    methods:{
      toAbout(){
        this.$router.push({name: 'about'})
      },
      toHelp(){
        this.$router.push({name: 'help'})
      },
      toAgreement(){
        this.$router.push({name: 'protocol'})
      }
    }
  }
</script>
<style scoped lang="less">



  @media screen {
    @media  (max-width:1300px) {
      .mainBox {
        width: 100%;
        height: 2.33rem;
        /*background:gray;*/
        background: #111720;
        .main {
          margin: 0 auto;
          max-width: 13.00rem;
          height: 2.33rem;
          position: relative;
          /*background: pink;*/
          .bottomLeft {
            position: absolute;
            left: 0.19rem;
            top: -0.47rem;
            padding-left: 0.47rem;
            width: 7.22rem;
            height: 2.33rem;
            background: #1D232B;
            .phone {
              margin-top: 0.32rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 0.14rem;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: left;
              line-height: 0.20rem;
            }
            .phoneNum {
              margin-top: 0.08rem;
              font-family: PingFangSC-Light;
              font-weight: 300;
              font-size: 0.30rem;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: left;
              line-height: 0.42rem;
            }
            .address {
              margin-top: 0.36rem;
              font-family: PingFangSC-Regular;
              font-size: 0.14rem;
              color: rgba(255, 255, 255, 0.78);
              letter-spacing: 0;
              text-align: left;
              line-height: 0.20rem;
            }
            .email {
              margin-top: 0.22rem;
              font-family: PingFangSC-Regular;
              font-size: 0.14rem;
              color: rgba(255, 255, 255, 0.78);
              letter-spacing: 0;
              text-align: left;
              line-height: 0.20rem;
            }
          }
          .bottomRight {
            position: absolute;
            top: 0;
            right: 0;
            width: 4.31rem;
            height: 2.33rem;
            /*background: #1D232B;*/
            .requestList {
              margin-top: 0.53rem;
              width: 2.85rem;
              height: 0.20rem;
              display: flex;
              justify-content: space-between;
              font-family: PingFangSC-Regular;
              font-size: 0.14rem;
              color: #B6B6B6;
              line-height: 0.20rem;
              div{
                cursor: pointer;
              }
            }
            .Filing {
              margin-top: 0.56rem;
              font-family: PingFangSC-Regular;
              font-size: 0.14rem;
              color: #747474;
              letter-spacing: 0;
              text-align: left;
              line-height: 1;
              display: flex;
              a {
                color: #747474;
                text-decoration: none;
              }
              .divder {
                width: 1px;
                height: 14px;
                background-color: #747474;
                margin: 0 4px;
              }
            }
          }
        }
      }
    }
    @media  (min-width:1301px){
      .mainBox{
        width: 100%;
        height:233px;
        /*background:gray;*/
        background: #111720;
        .main{
          margin:0 auto;
          max-width:1300px ;
          height:233px;
          position: relative;
          /*background: pink;*/
          .bottomLeft{
            position: absolute;
            left:19px;
            top:-47px;
            padding-left:47px;
            width: 722px;
            height:233px;
            background: #1D232B;
            .phone{
              margin-top: 32px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14px;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: left;
              line-height:20px;
            }
            .phoneNum{
              margin-top:8px;
              font-family: PingFangSC-Light;
              font-size: 30px;
              font-weight: 300;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: left;
              line-height:42px;
            }
            .address{
              margin-top:36px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: rgba(255,255,255,0.78);
              letter-spacing: 0;
              text-align: left;
              line-height:20px;
            }
            .email{
              margin-top:22px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: rgba(255,255,255,0.78);
              letter-spacing: 0;
              text-align: left;
              line-height:20px;
            }
          }
          .bottomRight{
            position: absolute;
            top:0;
            right:0;
            width: 431px;
            height:233px;
            /*background: #1D232B;*/
            .requestList{
              margin-top:53px;
              width: 285px;
              height:20px;
              display: flex;
              justify-content: space-between;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #B6B6B6;
              line-height:20px;
              div{
                cursor: pointer;
              }
            }
            .Filing{
              margin-top:56px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #747474;
              letter-spacing: 0;
              text-align: left;
              line-height: 1;
              display: flex;
              a {
                color: #747474;
                text-decoration: none;
              }
              .divder {
                width: 1px;
                height: 14px;
                background-color: #747474;
                margin: 0 4px;
              }
            }
          }
        }
      }
    }
  }
</style>
