<template>
  <div class="main">
    <universalTop :indexSign="indexSign"  :isSearchSHow="isSearchSHow"></universalTop>
    <div class="topPrc">
      <img  src="/static/allContent/aboutUsBgd.png" alt=""/>
      <div class="about">{{$t('notices.aboutUs')}}</div>
    </div>
    <div class="container">
      <div class="content">
        <div class="Introduction"><span></span><span>{{$t('notices.companyProfile')}}</span></div>
        <div class="article">
          <div class="articleCon">
            <div style="" v-for="(item,index) in article" :key="index">
              {{$t(item)}}
            </div>
          </div>
          <div class="articleImg"><img src="/static/allContent/abutUsImg.png" alt=""></div>
        </div>
        <div class="Introduction join"><span></span><span>{{$t('notices.joinUs')}}</span></div>
        <div class="mailbox">{{$t('notices.mailbox')}}：<span>{{email}}</span></div>
      </div>
    </div>


    <div class="blank3" style="width: 100%;"></div>
    <universalBottom></universalBottom>
  </div>
</template>
<script>
  import universalTop from '../../components/universalTop'
  import universalBottom from '../../components/universalBottom'
  export default {
    components: {
      universalTop,
      universalBottom,

    },
    data() {
      return {
        isSearchSHow : false,
        indexSign: true,
        article:[
          'about.row1','about.row2','about.row3','about.row4','about.row5','about.row6',
        ],
        email:'cnc@cncnews.cn',
        phone:'(010)63071114',
        address:this.$t('notices.aboutEmile'),
        scrollTop:window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop,
      }
    },
    mounted(){
      window.addEventListener('scroll', this.handleScroll)    //在mounted钩子中给window添加一个滚动滚动监听事件，

    },
    methods:{
      handleScroll () {
        this.scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop
        if(this.scrollTop > 10){
          this.indexSign = false
          this.isSearchSHow = true
        }else{
          this.indexSign = true
          this.isSearchSHow = false
        }
      },
    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .main{
        width: 100%;
        .topPrc{
          width: 100%;
          height:479/100rem;
          background: #000000;
          position: relative;
          left: 50%;
          transform: translateX(-50%);
          overflow: hidden;
          .about{
            position: absolute;
            height:56/100rem;
            width: 100%;
            font-family: PingFangSC-Semibold;font-weight:600;
            font-size: 40/100rem;
            color: #FFFFFF;
            letter-spacing: 10/100rem;
            line-height:56/100rem;
            text-align: center;
            top:221/100rem;
            z-index: 100;
          }
          img{
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
          }
        }
        .container{
          min-height:693/100rem;
          width:1247/100rem;
          margin:0 auto;
          position: relative;
          padding-bottom: 80/100rem;
          .content{
            width: 1149/100rem;
            padding:64/100rem 43/100rem 62/100rem 55/100rem;
            min-height: 522/100rem;
            margin-top: -81/100rem;
            background: #FFFFFF;
            box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
            .Introduction{
              width: 1149/100rem;
              display: flex;
              align-items: center;
              span:nth-of-type(1){
                background: #BA0132;
                width: 4/100rem;
                height:20/100rem;
                display: block;
              }
              span:nth-of-type(2){
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:28/100rem;
                margin-left:10/100rem;
              }
            }
            .article{
              padding-left:10/100rem;
              margin-top:15/100rem;
              width: 1139/100rem;
              display: flex;
              justify-content: space-between;
              .articleCon{
                padding-top:15/100rem;
                width: 581/100rem;
                min-height:253/100rem;
                font-family: PingFangSC-Regular;
                font-size: 16/100rem;
                color: #666666;
                letter-spacing: 0;
                text-align: left;
                line-height:22/100rem;
              }
              .articleImg{
                width: 496/100rem;
                height:279/100rem;
                img{
                  width: 100%;
                  height:100%;
                  float: left;
                }
              }
            }
            .join{
              margin-top:100/100rem;
            }
            .mailbox{
              text-indent: 85/100rem;
              margin-top:52/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22/100rem;
              span{
                font-family: PingFangSC-Regular;
                font-size: 16/100rem;
                color: #2A2A2A;
                letter-spacing: 0;
                text-align: left;
                line-height: 22/100rem;
              }
            }
          }

        }
      }
      .blank1{
        height:72/100rem;
      }
    }
    @media  (min-width:1301px){
      .main{
        width: 100%;
        .topPrc{
          width: 100%;
          height:479px;
          background: #000000;
          position: relative;
          left: 50%;
          transform: translateX(-50%);
          overflow: hidden;
          .about{
            position: absolute;
            height:56px;
            width: 100%;
            font-family: PingFangSC-Semibold;font-weight:600;
            font-size: 40px;
            color: #FFFFFF;
            letter-spacing: 10px;
            line-height:56px;
            text-align: center;
            top:221px;
            z-index: 100;
          }
          img{
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
          }
        }
        .container{
          min-height:693px;
          width:1247px;
          margin:0 auto;
          position: relative;
          padding-bottom: 80px;
          .content{
            width: 1149px;
            padding:64px 43px 62px 55px;
            min-height: 522px;
            margin-top: -81px;
            background: #FFFFFF;
            box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
            .Introduction{
              width: 1149px;
              display: flex;
              align-items: center;
              span:nth-of-type(1){
                background: #BA0132;
                width: 4px;
                height:20px;
                display: block;
              }
              span:nth-of-type(2){
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20px;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:28px;
                margin-left:10px;
              }
            }
            .article{
              padding-left:10px;
              margin-top:15px;
              width: 1139px;
              display: flex;
              justify-content: space-between;
              .articleCon{
                padding-top:15px;
                width: 581px;
                min-height:253px;
                font-family: PingFangSC-Regular;
                font-size: 16px;
                color: #666666;
                letter-spacing: 0;
                text-align: left;
                line-height:22px;
                div{
                  text-indent: 20px;
                  margin-bottom: 15px
                }
              }
              .articleImg{
                width: 496px;
                height:279px;
                img{
                  width: 100%;
                  height:100%;
                  float: left;
                }
              }
            }
            .join{
              margin-top:100px;
            }
            .mailbox{
              text-indent: 85px;
              margin-top:52px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22px;
              span{
                font-family: PingFangSC-Regular;
                font-size: 16px;
                color: #2A2A2A;
                letter-spacing: 0;
                text-align: left;
                line-height: 22px;
              }
            }
          }

        }
      }
      .blank1{
        height:72px;
      }

    }
  }
</style>
