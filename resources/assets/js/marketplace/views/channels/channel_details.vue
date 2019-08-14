<template>
<div class="main">
  <loadingPage :showLoading="showLoading"></loadingPage>
  <universalTop :indexSign="indexSign" ></universalTop>
  <div class="blank1" style=""></div>
  <div class="container">
    <div class="top_nav">
      <span  @click="toIndex">{{$t('channelDetail.home')}}</span> > <span   @click="toAllChannel">{{$t('channelDetail.allChannels')}}</span> > <span style="color: #BA0132;">{{$t('channelDetail.channelDetails')}}</span>
    </div>
    <div class="loadingDiv" style="min-height: 800px" v-if="isLoadingDiv"></div>
    <div v-if="!isLoadingDiv" class="introduction">
      <div class="introduction_img">
      <div>
      <img :src="channelIntroduction.feature_image" alt="">
      </div>
      </div>
      <div class="introduction_main">
        <div class="introduction_title">{{channelIntroduction.name}}</div>
        <div class="introduction_subtitle">{{channelIntroduction.description}}</div>
        <div class="introduction_list">
            <div>
              <span>{{$t('channelDetail.contentList')}}</span>
              <span>{{channelIntroduction.playlist_count}}</span>
            </div><div>
              <span>{{$t('channelDetail.totalVideo')}}</span>
              <span>{{channelIntroduction.entry_count}}</span>
            </div><div>
              <span>{{$t('channelDetail.originalCountry')}}</span>
              <span>{{channelIntroduction.region}}</span>
            </div><div>
              <span>{{$t('channelDetail.lastUpdateTime')}}</span>
              <span>{{channelIntroduction.updated_at}}</span>
            </div>
        </div>
      </div>
      <div class="subscribe">
        <div class="subscribediv" @click="subscribedivClick"  v-if="channelIntroduction.is_subscribed==0?true:false">{{$t('channelDetail.subscription')}}</div>
        <div class="unsubscribe" @click="unsubscribeClick" v-else>{{$t('channelDetail.unsubscription')}}</div>
      </div>
    </div>
    <div v-if="!isLoadingDiv" class="content_list">
      <div class="content_list_title">{{$t('channelDetail.channelContentList')}}</div>
      <div class="listItemBox" >
        <div @click="toConListDetails(item.id)" :style="index>3?'cursor: pointer;margin-top:30px':'cursor: pointer'" class="listitem listitems" v-for="(item,index) in contentList" :key="index">
          <div class="imgUrl"><img :src="item.logo" alt=""></div>
          <div class="source">
            <span>{{item.name}}</span>
            <span>{{$t(item.genre)}}</span>
          </div>
          <div class="videoNum">
            <div><img src="/static/icon/Group4.png" alt=""></div>
            <div>{{item.entries_count}}{{$t('listDetails.one')}}</div>
          </div>
          <div class="contentList">{{$t('channelDetail.playlistNum')}}</div>
          <div class="bottomShadow"></div>
        </div>
        <div class="listitem" style="background: #f9f9f9;" v-for="(item,index) in (4 - contentList.length%4)" v-if="contentList.length%4>0"></div>
      </div>
    </div>

    <div  class="panges" v-if="all_count>8 && !isLoadingDiv">
      <Pagenation :allCount='all_count' :allPage='allPage' :countPagea="countPage" :meicount="page_count"
                  :jumpcounta="jumpCount" @nextPage="nextPage"></Pagenation>

    </div>
  </div>


  <div class="blank3" style="width: 100%;"></div>
  <universalBottom></universalBottom>
</div>
</template>
<script>
  import universalTop from '../../components/universalTop'
  import universalBottom from '../../components/universalBottom'
  import universalSearch from '../../components/universalSearch/universalSearch.vue'
  import Pagenation from '../../components/pager/pager.vue'
  import loadingPage from '../../components/loading/loading.vue'
  import {  property,getplaylist,subscribe } from '@/utils/global/axios.js'

  export default {
    components:{
      universalTop,
      universalBottom,
      Pagenation,
      universalSearch,
      loadingPage
    },
    data(){
      return{
        all_count:0,//总条数
        allPage:0,//总页数
        countPage:1,//当前页
        page_count:8,//每页多少条
        jumpCount:1,//跳转页
        indexSign:false,
        isLoadingDiv:true,
        contentList:[],
        channelIntroduction:{},
        queryId:null,
        showLoading:true,
      }
    },
    created(){
      this.queryId = this.$route.query.channelId
      this.getDetails()
      this.getPlaylist()
    },
    computed:{
      channelID(){
        if(this.queryId == null ||this.queryId == undefined || this.queryId == ''){
          return '144'
        }else{
          return this.queryId
        }
      }
    },
    methods:{
      getDetails(){
        let params = '/' + this.channelID
        property(params).then(res=>{
          this.channelIntroduction = res.data.data
          this.showLoading = false
          this.isLoadingDiv = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      getPlaylist(){
        let params = '?limit=8&propertyId='+this.channelID+'&start=0'
        getplaylist(params).then(res=>{
          if(res.data){
            this.all_count = res.data.total_count
            this.allPage = Math.ceil(res.data.total_count/8)
            this.contentList = res.data.data
            this.contentList.forEach((item,index)=>{
              this.Gloal.playlistType.forEach((item1,index1)=>{
                if(item.genre == item1.typeId){
                  item.genre = item1.name
                }
              })
            })
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      nextPage(val){
        this.showLoading = true
        let params = '?limit=8&propertyId='+this.channelID+'&start='+(val-1)*8
        getplaylist(params).then(res=>{
          if(res.data){
            this.countPage = val
            this.contentList = res.data.data
            this.contentList.forEach((item,index)=>{
              this.Gloal.playlistType.forEach((item1,index1)=>{
                if(item.genre == item1.typeId){
                  item.genre = item1.name
                }
              })
            })
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      subscribedivClick(){
        let id = this.channelID
        let params = id+'/subscribe'
        subscribe(params).then(res=>{
          if(res.data.status == 'success'){
            this.channelIntroduction.is_subscribed = 1
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      unsubscribeClick(){
        let id = this.channelID
        let params = id+'/unsubscribe'
        subscribe(params).then(res=>{
          if(res.data.status == 'success'){
            this.channelIntroduction.is_subscribed = 0
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },

      toIndex(){
        this.$router.push({name: 'index'})
      },
      toAllChannel(){
        this.$router.push({name: 'allChannel'})
      },
      toConListDetails(id){
        this.$router.push({name: 'ContentListDetails',query:{playlistId:id}})
      }
    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .main{
        .container{
          width: 100%;
          background: #f9f9f9;
          .top_nav{
            padding-left:5/100rem;
            width: 1275/100rem;
            margin: 50/100rem auto 40/100rem auto;
            background: #f9f9f9;
            height: 20/100rem;
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 14/100rem;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:20/100rem;
            span{
              cursor: pointer;
            }
          }
          .introduction{
            width: 1280/100rem;
            /*height:361/100rem;*/
            padding-bottom: 40/100rem;
            margin:0 auto;
            background: #ffffff;
            border: 1/100rem solid #EEEEEE;
            display: flex;
            .introduction_img{
              width: 544/100rem;
              display: flex;
              /*align-items: center;*/
              div{
                margin-top:80/100rem;
                width: 544/100rem;
                height:252/100rem;
                overflow: hidden;
                img{
                  width: 544/100rem;
                  height:252/100rem;
                  object-fit:cover;
                  float: left;
                }
              }
            }
            .introduction_main{
              width: 605/100rem;
              /*height:361/100rem;*/
              /*padding-bottom: 40/100rem;*/
              .introduction_title{
                font-family: PingFangSC-Semibold;font-weight:600;
                font-size: 30/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:42/100rem;
                margin-top:60/100rem;
                /*display: inline-block;*/
              }
              .introduction_subtitle{
                /*display: inline-block;*/
                font-family: PingFangSC-Regular;
                font-size: 14/100rem;
                color: #999999;
                letter-spacing: 0;
                text-align: left;
                line-height:20/100rem;
                margin-top:12/100rem;
              }
              .introduction_list{
                height:160/100rem;
                margin-top:27/100rem;
                div{
                  height:40/100rem;
                  display: flex;
                  span:nth-of-type(1){
                    width: 160/100rem;
                    height:40/100rem;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14/100rem;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40/100rem;
                  }
                  span:nth-of-type(2){
                    height:40/100rem;
                    width:440/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 14/100rem;
                    color: #2A2A2A;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40/100rem;
                  }
                }
              }
            }
            .subscribe{
              height:361/100rem;
              .subscribediv{
                padding:0 10/100rem;
                height:30/100rem;
                margin-top:61/100rem;
                background: #BA0132;
                border-radius: 2/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 12/100rem;
                color: #FFFFFF;
                letter-spacing: 0;
                text-align: center;
                line-height:30/100rem;
                cursor: pointer;
              }
              .subscribediv:hover{
                background: #C1204A;
              }
              .unsubscribe{
                padding:0 10/100rem;
                height:30/100rem;
                margin-top:61/100rem;
                border-radius: 2/100rem;
                /*font-family: PingFangSC-Medium;font-weight:500;*/
                font-size: 12/100rem;
                letter-spacing: 0;
                text-align: center;
                line-height:30/100rem;
                cursor: pointer;
                background: #ffffff;
                color: #BB0737;
                border: 1/100rem solid #CA002E;
              }
              .unsubscribe:hover{
                background: #BA0132;
                color: #ffffff;
                border:1/100rem solid #BA0132;
              }


            }
          }
          .content_list{
            /*height:589/100rem;*/
            width: 1280/100rem;
            margin:0 auto;
            padding-top:34/100rem;
            background: #f9f9f9;
            .content_list_title{
              text-indent: 5/100rem;
              height:30/100rem;
              font-family: PingFangSC-Semibold;font-weight:600;
              font-size: 22/100rem;
              color: #666666;
              letter-spacing: 0;
              text-align: left;
              line-height:30/100rem;
            }
            .listItemBox{
              /*height:545/100rem;*/
              width: 1280/100rem;
              margin-top:14/100rem;
              /*background: gray;*/
              display: flex;
              flex-wrap: wrap;
              justify-content: space-between;
              align-content:space-between;
              .listitems:hover{
                box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
              }
              .listitem{

                background: #ffffff;

                width: 303/100rem;
                height:257/100rem;
                position: relative;
                .imgUrl{
                  width: 303/100rem;
                  height:171/100rem;
                  overflow: hidden;
                  img{
                    width: 303/100rem;
                    height:171/100rem;
                    object-fit:cover;
                    float: left;
                  }
                }
                .source{
                  height:85/100rem;
                  width: 303/100rem;
                  span:nth-of-type(1){
                    margin-top:15/100rem;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18/100rem;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25/100rem;
                    text-indent: 17/100rem;
                    display: block;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap
                  }
                  span:nth-of-type(2){
                    margin-top:8/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 12/100rem;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:17/100rem;
                    text-indent: 17/100rem;
                    display: block;
                  }
                }

                .videoNum{
                  position: absolute;
                  right:14/100rem;
                  top:105/100rem;
                  width: 52/100rem;
                  height: 52/100rem;
                  background: rgba(0,0,0,0.30);
                  border-radius: 52/100rem;
                  display: flex;
                  flex-direction: column;
                  align-items: center;
                  div:nth-of-type(1){
                    width: 20/100rem;
                    height:17/100rem;
                    margin-top:10/100rem;
                    img{
                      width: 20/100rem;
                      height:17/100rem;
                      float: left;
                    }
                  }
                  div:nth-of-type(2){
                    margin-top:2/100rem;
                    width: 57/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 12/100rem;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    text-align: center;
                    line-height:17/100rem;
                  }
                }
                .contentList{
                  position: absolute;
                  top:13/100rem;
                  left:-5/100rem;
                  width: 91/100rem;
                  height:29/100rem;
                  background: #BB0737;
                  border-radius: 4/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 14/100rem;
                  color: #FFFFFF;
                  letter-spacing: 0;
                  text-align: center;
                  line-height:29/100rem;
                }
                .bottomShadow{
                  width: 303/100rem;
                  height:31/100rem;
                  background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                  position: absolute;
                  top:140/100rem;
                  left:0;
                }
              }
            }

          }
          .panges{
            margin-top:81/100rem;
          }
        }
      }
      .blank1{
        height:72/100rem;
      }
      .blank3{
        height:120/100rem;
        /*background: pink;*/
      }

    }
    @media  (min-width:1301px){
      .main{
        .container{
          width: 100%;
          background: #f9f9f9;
          .top_nav{
            padding-left:5px;
            width: 1275px;
            margin: 50px auto 40px auto;
            background: #f9f9f9;
            height: 20px;
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 14px;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:20px;
            span{
              cursor: pointer;
            }
          }
          .introduction{
            width: 1280px;
            /*height:361px;*/
            padding-bottom: 40px;
            margin:0 auto;
            background: #ffffff;
            border: 1px solid #EEEEEE;
            display: flex;
            .introduction_img{
              width: 544px;
              display: flex;
              justify-content: center;
              /*align-items: center;*/
              div{
                margin-top:80px;
                width: 448px;
                height:252px;
                overflow: hidden;
                img{
                  width: 448px;
                  height:252px;
                  object-fit:cover;
                  float: left;
                }
              }
            }
            .introduction_main{
              width: 605px;
              /*height:361px;*/
              /*padding-bottom: 40px;*/
              .introduction_title{
                font-family: PingFangSC-Semibold;font-weight:600;
                font-size: 30px;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:42px;
                margin-top:60px;
                /*display: inline-block;*/
              }
              .introduction_subtitle{
                /*display: inline-block;*/
                font-family: PingFangSC-Regular;
                font-size: 14px;
                color: #999999;
                letter-spacing: 0;
                text-align: left;
                line-height:20px;
                margin-top:12px;
              }
              .introduction_list{
                height:160px;
                margin-top:27px;
                div{
                  height:40px;
                  display: flex;
                  span:nth-of-type(1){
                    width: 160px;
                    height:40px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14px;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40px;
                  }
                  span:nth-of-type(2){
                    height:40px;
                    width:440px;
                    font-family: PingFangSC-Regular;
                    font-size: 14px;
                    color: #2A2A2A;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40px;
                  }
                }
              }
            }
            .subscribe{
              height:361px;
              .subscribediv{
                padding:0 10px;
                height:30px;
                margin-top:61px;
                background: #BA0132;
                border-radius: 2px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 12px;
                color: #FFFFFF;
                letter-spacing: 0;
                text-align: center;
                line-height:30px;
                cursor: pointer;
              }
              .subscribediv:hover{
                background: #C1204A;
              }
              .unsubscribe{
                padding:0 10px;
                height:30px;
                margin-top:61px;
                border-radius: 2px;
                /*font-family: PingFangSC-Medium;font-weight:500;*/
                font-size: 12px;
                letter-spacing: 0;
                text-align: center;
                line-height:30px;
                cursor: pointer;
                background: #ffffff;
                color: #BB0737;
                border: 1px solid #CA002E;
              }
              .unsubscribe:hover{
                background: #BA0132;
                color: #ffffff;
                border:1px solid #BA0132;
              }


            }
          }
          .content_list{
            /*height:589px;*/
            width: 1280px;
            margin:0 auto;
            padding-top:34px;
            background: #f9f9f9;
            .content_list_title{
              text-indent: 5px;
              height:30px;
              font-family: PingFangSC-Semibold;font-weight:600;
              font-size: 22px;
              color: #666666;
              letter-spacing: 0;
              text-align: left;
              line-height:30px;
            }
            .listItemBox{
              /*height:545px;*/
              width: 1280px;
              margin-top:14px;
              /*background: gray;*/
              display: flex;
              flex-wrap: wrap;
              justify-content: space-between;
              align-content:space-between;
              .listitems:hover{
                box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
              }
              .listitem{

                background: #ffffff;

                width: 303px;
                height:257px;
                position: relative;
                .imgUrl{
                  width: 303px;
                  height:171px;
                  overflow: hidden;
                  img{
                    width: 303px;
                    height:171px;
                    object-fit:cover;
                    float: left;
                  }
                }
                .source{
                  height:85px;
                  width: 303px;
                  span:nth-of-type(1){
                    margin-top:15px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18px;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25px;
                    text-indent: 17px;
                    display: block;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap
                  }
                  span:nth-of-type(2){
                    margin-top:8px;
                    font-family: PingFangSC-Regular;
                    font-size: 12px;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:17px;
                    text-indent: 17px;
                    display: block;
                  }
                }

                .videoNum{
                  position: absolute;
                  right:14px;
                  top:105px;
                  width: 52px;
                  height: 52px;
                  background: rgba(0,0,0,0.30);
                  border-radius: 52px;
                  display: flex;
                  flex-direction: column;
                  align-items: center;
                  div:nth-of-type(1){
                    width: 20px;
                    height:17px;
                    margin-top:10px;
                    img{
                      width: 20px;
                      height:17px;
                      float: left;
                    }
                  }
                  div:nth-of-type(2){
                    margin-top:2px;
                    width: 57px;
                    font-family: PingFangSC-Regular;
                    font-size: 12px;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    text-align: center;
                    line-height:17px;
                  }
                }
                .contentList{
                  position: absolute;
                  top:13px;
                  left:-5px;
                  width: 91px;
                  height:29px;
                  background: #BB0737;
                  border-radius: 4px;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 14px;
                  color: #FFFFFF;
                  letter-spacing: 0;
                  text-align: center;
                  line-height:29px;
                }
                .bottomShadow{
                  width: 303px;
                  height:31px;
                  background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                  position: absolute;
                  top:140px;
                  left:0;
                }
              }
            }

          }
          .panges{
            margin-top:81px;
          }
        }
      }
      .blank1{
        height:72px;
      }
      .blank3{
        height:120px;
        /*background: pink;*/
      }
    }
  }
</style>
