<template>
  <div class="playlistMain" v-if="isPlsylistDialogShow">
    <div class="mainplayDialog">
      <div class="topTitle">
        <div>{{$t('shareToEmail.shareAndCopy')}}</div>
        <div @click="closeDialog"><img src="/static/icon/closed.png" alt=""></div>
      </div>
      <div class="container" id="boo" ref="container">
        <table border="0" style='width: 797px;text-align: left'>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);border-top:1px solid rgba(221,221,221,1);">{{$t('listDetails.content_list')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);border-top:1px solid rgba(221,221,221,1);">{{playlistDetails.name?playlistDetails.name:''}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(51,51,51,1);text-indent:20px;border-top:1px solid rgba(221,221,221,1);'>{{$t('listDetails.Regional_rights')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1);border-top:1px solid rgba(221,221,221,1);'></td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.source')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{playlistDetails.property.name}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;'>{{$t('listDetails.Allowed_area')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'>{{playlistDetails.term?playlistDetails.term.api_share_to:''}}</td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.effective_time')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{playlistDetails.start_date == null?$t('listDetails.no'):playlistDetails.start_date }}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;'>{{$t('listDetails.exclude')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'>{{playlistDetails.term?playlistDetails.term.region_excepted:''}}</td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.Last_update_time')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{ playlistDetails.updated_at}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(51,51,51,1);text-indent:20px;'>{{$t('listDetails.PaymentMode')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'></td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.Total_video')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{playlistDetails.entries_count}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;'>{{$t('listDetails.paymentMethod')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'>{{playlistDetails.term&&playlistDetails.term.payment_mode_in_lang?playlistDetails.term.payment_mode_in_lang:''}}</td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.classification')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{playlistDetails.genre}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;'>{{$t('listDetails.exclusive')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'>{{playlistDetails.term&&playlistDetails.term.exclusivity?$t(playlistDetails.term.exclusivity):''}}</td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.Original_country')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{playlistDetails.region}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;'>{{$t('listDetails.NumberOfVideoUpdates')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)' v-if="playlistDetails.term&&PaymentModeIndex==2">{{$t('listDetails.NotLessThanEveryYear')}}{{playlistDetails.term.update_count}}{{$t('listDetails.one')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)' v-if="playlistDetails.term&&PaymentModeIndex==3">{{$t('listDetails.NotLessThanMonthly')}}{{playlistDetails.term.update_count}}{{$t('listDetails.one')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)' v-else></td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.Language')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{playlistDetails.language}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;'>{{$t('listDetails.TotalPrice')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'  v-if="playlistDetails.term&&PaymentModeIndex==2">{{$t('listDetails.PerYear')}}  {{playlistDetails.term.price}}￥</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'  v-if="playlistDetails.term&&PaymentModeIndex==3">{{$t('listDetails.perMonth')}} {{playlistDetails.term.price}}￥</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'  v-if="playlistDetails.term&&PaymentModeIndex==1">{{playlistDetails.term.price}}￥</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)'  v-else></td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 49px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);">{{$t('listDetails.other')}}</td>
            <td style="width:227px;color:rgba(102,102,102,1);">{{playlistDetails.other == null?$t('listDetails.no'):playlistDetails.other}}</td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;'>{{$t('listDetails.Proportion')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)' v-if="playlistDetails.term&&PaymentModeIndex==0">{{$t('listDetails.ContentSide')}}{{playlistDetails.term.revenue_share_cp}}%，{{$t('listDetails.ServiceParty')}}{{playlistDetails.term.revenue_share_sp}}%</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1)' v-else></td>
          </tr>
          <tr style='font-size:16px;width: 797px;height: 65px;'>
            <td style="width: 141px;color:rgba(51,51,51,1);text-indent: 20px;border-left:1px solid rgba(221,221,221,1);border-bottom:1px solid rgba(221,221,221,1);"></td>
            <td style="width:227px;color:rgba(102,102,102,1);border-bottom:1px solid rgba(221,221,221,1);"></td>
            <td style='width: 19px; border-right:1px solid rgba(221,221,221,1);border-left:1px solid rgba(221,221,221,1);border-bottom:1px solid #ffffff;'></td>
            <td style='width: 170px;color:rgba(153,153,153,1);text-indent:20px;border-bottom:1px solid rgba(221,221,221,1);'>{{$t('listDetails.Comment')}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1);border-bottom:1px solid rgba(221,221,221,1);' v-if="playlistDetails.term">{{playlistDetails.term.payment_comments}}</td>
            <td style='width: 240px;color:rgba(102,102,102,1); border-right:1px solid rgba(221,221,221,1);border-bottom:1px solid rgba(221,221,221,1);' v-else></td>
          </tr>

        </table>
        <table style="font-size:16px;text-align: left">
          <tr style='width: 797px;height: 67px;width: 797px;'>
            <td style='width: 797px;'>{{$t('shareToEmail.link')}}</td>
          </tr>
        </table>
        <table style="border:1px solid rgba(221,221,221,1);width: 797px;">
          <tr style='width: 797px;height: 48px;'>
            <td style=';text-align: left;font-size:16px;width: 797px;text-indent: 20px;'>{{url}}</td>
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
      isPlsylistDialogShow:{
        type: Boolean,
        default(){
          return false
        }
      },
      playlistDetails:{

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
        justify-content: center;
        align-items: flex-start;
        .mainplayDialog{
          margin-top:77px;
          width: 905px;
          min-height:900px;
          background: #ffffff;
          padding:0 54px 0 41px;
          .topTitle{
            margin-top:57px;
            width: 905px;
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
            width: 905px;
            margin-top:45px;
            /*display: flex;*/
            /*justify-content: space-between;*/
            .conLeftBox{
              width: 368px;
              border:1px solid rgba(221,221,221,1);
              padding-top:21px;
              /*height:462px;*/
              .conLeftItem{
                height:22px;
                width: 368px;
                display: flex;
                justify-content: space-between;
                margin-bottom:27px;
                div:nth-of-type(1){
                  width: 141px;
                  text-indent: 19px;
                  height:22px;
                  font-size:16px;
                  font-family:PingFangSC-Medium;
                  font-weight:500;
                  color:rgba(51,51,51,1);
                  line-height:22px;
                  text-align: left;
                }
                div:nth-of-type(2){
                  width: 227px;
                  height:22px;
                  font-size:16px;
                  font-family:PingFangSC-Regular;
                  font-weight:400;
                  color:rgba(102,102,102,1);
                  line-height:22px;
                  text-align: left;
                }
              }
            }
            .conRightBox{
              width: 495px;
              padding:0 5px 0 18px;
              border:1px solid rgba(221,221,221,1);
              .rightTitle{
                width: 495px;
                height:22px;
                font-size:16px;
                font-family:PingFangSC-Medium;
                font-weight:500;
                color:rgba(51,51,51,1);
                line-height:22px;
                text-align: left;
                margin-top:21px;
                margin-bottom:25px;
              }
              .rightItem:nth-of-type(3){
                margin-bottom: 42px;
              }
              .rightItem{
                width: 495px;
                height:20px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                div:nth-of-type(1){
                  width: 170px;
                  height:20px;
                  font-size:14px;
                  font-family:PingFangSC-Medium;
                  font-weight:500;
                  color:rgba(153,153,153,1);
                  line-height:20px;
                  text-align: left;
                }
                div:nth-of-type(2){
                  width: 343px;
                  height:20px;
                  text-align: left;
                  font-size:14px;
                  font-family:PingFangSC-Medium;
                  font-weight:500;
                  color:rgba(102,102,102,1);
                  line-height:20px;
                }
              }

            }

          }
          .linkTitle{
            width: 905px;
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
            width:905px;
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
            width: 905px;
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
