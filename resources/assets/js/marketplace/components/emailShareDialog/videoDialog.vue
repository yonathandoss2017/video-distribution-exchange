<template>
  <div class="playlistMain" v-if="isvideoDialogShow">
    <div class="main">
      <div class="topTitle">
        <div>{{$t('shareToEmail.shareAndCopy')}}</div>
        <div @click="closeDialog"><img src="/static/icon/closed.png" alt=""></div>
      </div>
      <div id="boo" style="margin-top: 45px;">
        <table border="0" style='border:1px solid rgba(221,221,221,1);width: 620px'>
          <tr style='width: 620px;height: 49px;'>
            <td style="width: 141px;color:rgba(153,153,153,1);text-indent: 20px;font-size:16px;text-align: left;">{{$t('shareToEmail.videoTitle')}}</td>
            <td style="color:rgba(102,102,102,1);font-size:16px;text-align: left;">{{shareVideoDetails.name}}</td>
          </tr>
          <tr style='width: 620px;height: 49px;'>
            <td style="width: 141px;color:rgba(153,153,153,1);text-indent: 20px;font-size:16px;text-align: left;">{{$t('shareToEmail.Introduction')}}</td>
            <td style="color:rgba(102,102,102,1);font-size:16px;text-align: left;">{{shareVideoDetails.description}}</td>
          </tr>
          <tr style='width: 620px;height: 49px;'>
            <td style="width: 141px;color:rgba(153,153,153,1);text-indent: 20px;font-size:16px;text-align: left;">{{$t('shareToEmail.price')}}</td>
            <td style="color:rgba(102,102,102,1);font-size:16px;text-align: left;">4K 600¥</td>
          </tr>
          <tr style='width: 620px;height: 49px;'>
            <td style="width: 141px;color:rgba(153,153,153,1);text-indent: 20px;font-size:16px;text-align: left;"></td>
            <td style="color:rgba(102,102,102,1);font-size:16px;text-align: left;">高清HD 300¥</td>
          </tr>
          <tr style='width: 620px;height: 49px;'>
            <td style="width: 141px;color:rgba(153,153,153,1);text-indent: 20px;font-size:16px;text-align: left;">{{$t('shareToEmail.commit')}}</td>
            <td style="color:rgba(102,102,102,1);font-size:16px;text-align: left;">这是一条视频简介，最多展示两行，超出部分用省略号表示，这是一条视频简介，最多展示两行，超出部分用省略号表示…</td>
          </tr>
        </table>
        <table style="width: 620px;">
          <tr style='width: 620px;height: 67px;'>
            <td style='width: 620px;font-size:16px;text-align: left;'>{{$t('shareToEmail.link')}}</td>
          </tr>
        </table>
        <table style="border:1px solid rgba(221,221,221,1);width: 620px;">
          <tr style='width: 620px;height: 48px;'>
            <td style='width: 620px;text-indent: 20px;font-size:16px;text-align: left;'>{{url}}</td>
          </tr>
        </table>
      </div>

      <div class="buttonBox">
        <div @click="copyButtom()" class="btn" data-clipboard-action="copy" data-clipboard-target="#boo">{{$t('shareToEmail.copy')}}</div>
        <div @click="closeDialog">{{$t('shareToEmail.cancel')}}</div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    data(){
      return{
        url: window.location.href
      }
    },
    computed:{
      PaymentModeIndex(){
        return this.playlistDetails.PaymentModeIndex
      }
    },
    props: {
      isvideoDialogShow:{
        type: Boolean,
        default(){
          return false
        }
      },
      shareVideoDetails:{

        default(){
          return ''
        }
      }
    },
    methods:{
      closeDialog(){
        this.$emit('closeDialog')
      },
      copyButtom(){
        let str = this.$refs.container
        this.$emit('copyButtom',str)
      },
    }
  }
</script>
<style lang="less" scoped>
    .playlistMain{
        z-index: 400;
        position: absolute;
        top:0;
        left:0;
        background: rgba(0,0,0,0.20);
        width: 100%;
        height: 100%;
        display: flex;
        overflow:hidden;
        justify-content: center;
        align-items: flex-start;
        .main{
          margin-top:177px;
          width: 640px;
          display: block;
          min-height:681px;
          background: #ffffff;
          padding:0 40px 0 40px;
          .topTitle{
            margin-top:57px;
            width: 640px;
            height:33px;
            display: flex;
            justify-content: space-between;
            div:nth-of-type(1){
              height:33px;
              font-size:24px;
              font-family:PingFangSC-Medium;
              font-weight:500;
              color:rgba(51,51,51,1);
              line-height:33px;
            }
            div:nth-of-type(2){
              width: 33px;
              height:33px;
              display: flex;
              justify-content: center;
              align-items: center;
              cursor: pointer;
              img{
                width: 20px;
                height:21px;
              }
            }
          }
          .container{
            width: 595px;
            margin-top:45px;
            padding:0 25px 0 20px;
            min-height:316px;
            border:1px solid rgba(221,221,221,1);
            .conItem{
              width: 595px;
              margin-top:25px;
              display: flex;
              justify-content: space-between;
              .itemTitle{
                width: 64px;
                height: 22px;
                font-size:16px;
                font-family:PingFangSC-Medium;
                font-weight:500;
                color:rgba(51,51,51,1);
                line-height:22px;
                text-align: left;
              }
              .itemCon{
                width:453px;
                font-size:16px;
                font-family:PingFangSC-Regular;
                font-weight:400;
                color:rgba(102,102,102,1);
                line-height:22px;
                text-align: left;
              }
            }
          }
          .linkTitle{
            width: 640px;
            height:22px;
            margin-top: 25px;
            font-size:16px;
            font-family:PingFangSC-Medium;
            font-weight:500;
            color:rgba(51,51,51,1);
            line-height:22px;
            text-align: left;
          }
          .linkAddress{
            margin-top:20px;
            width:638px;
            height:46px;
            border:1px solid rgba(221,221,221,1);
            text-indent: 17px;
            font-size:16px;
            font-family:PingFangSC-Medium;
            font-weight:500;
            color:rgba(102,102,102,1);
            line-height:46px;
            text-align: left;
          }

          .buttonBox{
            margin-top:41px;
            margin-bottom: 40px;
            width: 640px;
            height:46px;
            display: flex;
            div:nth-of-type(1){
              width:111px;
              height:46px;
              background:rgba(187,7,55,1);
              border-radius:4px;
              font-size:14px;
              font-family:PingFangSC-Regular;
              font-weight:400;
              color:rgba(255,255,255,1);
              line-height:46px;
              text-align: center;
              cursor: pointer;
            }
            div:nth-of-type(2){
              margin-left:25px;
              width:111px;
              height:46px;
              background:rgba(216,216,216,1);
              border-radius:4px;
              font-size:14px;
              font-family:PingFangSC-Regular;
              font-weight:400;
              color:rgba(255,255,255,1);
              line-height:46px;
              text-align: center;
              cursor: pointer;
            }
          }
        }
      }
</style>
