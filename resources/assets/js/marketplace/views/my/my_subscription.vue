<template>
  <div class="main">
    <loadingPage :showLoading="showLoading"></loadingPage>
    <universalTop :indexSign="indexSign" ></universalTop>
    <div class="blank1" style=""></div>
    <div class="container">
      <universalSearch class="search" :title="$t(searchTitle)" :subTitle="$t(searchSubTitle)"
                       :seachPlaceholder="seachPlaceholder" :isSearchShow="false"></universalSearch>
      <div class="emptyBox" v-if="ischannelListEmpty">
        <div class="conBox">
          <div class="imgBox"><img src="/static/icon/Empty.png" alt=""></div>
          <div class="emptyText">{{$t('allChannels.emptyContent')}}</div>
        </div>
      </div>
      <div class="channelBox" v-if="!ischannelListEmpty">
        <div class="channelItemBox" v-for="(item,index) in channelList" :key="index">
          <div class="channelItem">
            <div class="itemLeft">
              <div class="source">
                <div class="sourceMark"><img :src="item.logo" alt=""></div>
                <div class="sourceName">{{item.name}}</div>
                <div class="subscribeBox">
                  <div class="unsubscribe" @click="unSubscription(item.id)" style="" >{{$t('allChannels.unsubscribe')}}</div>
                </div>
              </div>
              <div class="borderBottom"></div>
              <div class="itemLeft_con">
                <div>
                  <span>{{$t('allChannels.contentList')}}</span>
                  <span>{{item.playlist_count}} {{$t('allChannels.numberOfcontentList')}}</span>
                </div><div>
                <span>{{$t('allChannels.totalVideo')}}</span>
                <span>{{item.entry_count}} {{$t('allChannels.video')}}</span>
              </div><div>
                <span>{{$t('allChannels.lastUpdateTime')}}</span>
                <span>{{item.updated_at}}</span>
              </div>
              </div>
            </div>
            <div class="itemRight">
              <div class="itemRightMore">
                <div @click="toChannelDetails(item.id)">
                  <span>{{$t('allChannels.seeMore')}}</span>
                  <span><img src="/static/icon/channelMore.png" alt=""></span>
                </div>
              </div>
              <div class="listItembox">
                <div class="listItem" style="cursor: pointer;" @click="toContentListDetails(subitem.id)" v-for="(subitem,subindex) in item.playlists" :key="subindex">
                  <div class="contentList">{{$t('allChannels.playlist')}}</div>
                  <div class="listItemImg"><img :src="subitem.logo" alt=""></div>
                  <div class="videoNum">
                    <div><img src="/static/icon/Group4.png" alt=""></div>
                    <div>{{subitem.video_count}}{{$t('listDetails.one')}}</div>
                  </div>
                </div>
                <div class="listItem" v-for="(item11,index11) in (3-(item.playlists.length)%3)" v-if="(item.playlists.length)%3>0"></div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="panges" v-if="all_count>5&&pangesShow">
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
  import {  Subscription,subscribe } from '@/utils/global/axios.js'
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
        page_count:5,//每页多少条
        jumpCount:1,//跳转页
        indexSign:false,
        showLoading:true,
        searchTitle:'allChannels.mySubscription',
        searchSubTitle:'allChannels.sub_title',
        seachPlaceholder:this.$t('allChannels.seach_placeholder'),
        channelList:[],
        pangesShow:false,
        ischannelListEmpty:false,
      }
    },
    watch:{
      channelList(){
        if(this.channelList.length == 0 || this.channelList==null){
          this.ischannelListEmpty = true
        }else{
          this.ischannelListEmpty = false
        }
      }
    },
    created(){
      this.getSubscriptionList(0)
    },
    methods:{
      //获取订阅列表
      getSubscriptionList(start){
        let params = '?limit=5&start='+start
        Subscription(params).then(res=>{
          if(res.data.status == 'success'){
            this.all_count = res.data.total_count
            this.allPage = Math.ceil(res.data.total_count/5)
            this.channelList = res.data.data
            this.pangesShow = true
          }
          this.showLoading = false

        }).catch(err=>{
          this.showLoading = false
        })
      },
      nextPage(val){
        this.showLoading = true
        let start = (val-1)*5
        this.getSubscriptionList(start)
        this.countPage = val
      },
      unSubscription(id){
        let params = id+'/unsubscribe'
        subscribe(params).then(res=>{
          if(res.data.status == 'success'){
            this.nextPage(this.countPage)
          }
          this.showLoading = false

        }).catch(err=>{
          this.showLoading = false
        })
      },
      toContentListDetails(id){
        this.$router.push({name: 'ContentListDetails',query:{playlistId:id}})
      },
      toChannelDetails(id){
        this.$router.push({name: 'channelDetails',query:{channelId:id}})

      }
    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .main{
        .container{
          width: 1292/100rem;
          margin:0 auto;
          .emptyBox{
            margin-top:10/100rem;
            width: 1292/100rem;
            height: 600/100rem;
            display: flex;
            justify-content: center;
            background: #fff;
            box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
            .conBox{
              width: 210/100rem;
              height:190/100rem;
              margin-top:200/100rem;
              display: flex;
              flex-direction: column;
              justify-content: space-between;
              align-items: center;
              /*align-content: space-between;*/
              .imgBox{
                width: 210/100rem;
                height:146/100rem;
                img{
                  width: 100%;
                  height:100%;
                  float: left;
                }
              }
              .emptyText{
                width: 210/100rem;
                font-size: 14/100rem;
                color: #666666;
                line-height:20/100rem;
                font-family: PingFangSC-Regular;
              }

            }
          }
          .channelBox{
            width: 1292/100rem;
            min-height: 500/100rem;
            .channelItemBox{
              width: 1292/100rem;
              .channelItem{
                margin-bottom:26/100rem;
                z-index: 10;
                width: 1292/100rem;
                height:251/100rem;
                background: #ffffff;
                box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
                display: flex;
                .itemLeft{
                  width: 305/100rem;
                  height:251/100rem;
                  margin-left:19/100rem;
                  .source{
                    margin-top:21/100rem;
                    height:57/100rem;
                    width:305/100rem;
                    display: flex;
                    align-items: center;
                    .sourceMark{
                      width: 57/100rem;
                      height:57/100rem;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      background: #FFFFFF;
                      border: 1/100rem solid #DDDDDD;
                      border-radius: 28.5/100rem;
                      img{
                        width: 45/100rem;
                        height:20/100rem;
                        float: left;
                      }
                    }
                    .sourceName{
                      width: 128/100rem;
                      margin-left:10/100rem;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 16/100rem;
                      color: #333333;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:57/100rem;
                      overflow:hidden; //超出的文本隐藏
                      text-overflow:ellipsis; //溢出用省略号显示
                      white-space:nowrap; //溢出不换行
                    }
                    .subscribeBox{
                      width: 104/100rem;
                      display: flex;
                      height:30/100rem;
                      justify-content: flex-end;
                      .subscription{
                        margin-left:32/100rem;
                        background: #BA0132;
                        border-radius: 2/100rem;
                        width: 72/100rem;
                        height:30/100rem;
                        /*font-family: PingFangSC-Medium;font-weight:500;*/
                        font-size: 12/100rem;
                        color: #FFFFFF;
                        letter-spacing: 0;
                        text-align: center;
                        line-height:30/100rem;
                        cursor: pointer;
                      }
                      .subscription:hover{
                        background: #C1204A;
                      }
                      .unsubscribe{
                        border-radius: 2/100rem;
                        padding:0 10/100rem;
                        height:30/100rem;
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
                  .borderBottom{
                    width:305/100rem ;
                    height:1/100rem;
                    background: #eeeeee;
                    margin-top:14/100rem;
                  }
                  .itemLeft_con{
                    width: 305/100rem;
                    height:120/100rem;
                    margin-top:11/100rem;
                    div{
                      width:305/100rem;
                      height:40/100rem;
                      display: flex;
                      span:nth-of-type(1){
                        width: 156/100rem;
                        height:40/100rem;
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 14/100rem;
                        color: #999999;
                        letter-spacing: 0;
                        text-align: left;
                        line-height: 40/100rem;
                        text-indent: 3/100rem;
                      }
                      span:nth-of-type(2){
                        flex: 1;
                        height:40/100rem;
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 14/100rem;
                        color: #2A2A2A;
                        letter-spacing: 0;
                        text-align: left;
                        line-height: 40/100rem;
                      }
                    }
                  }
                }
                .itemRight{
                  width: 918/100rem;
                  height:251/100rem;
                  margin-left:26/100rem;
                  /*background: gray;*/
                  .itemRightMore{
                    width: 918/100rem;
                    height:20/100rem;
                    /*background: gray;*/
                    margin-top:23/100rem;
                    display: flex;
                    justify-content: flex-end;
                    div{
                      cursor: pointer;
                      margin-right: 5/100rem;
                      display: flex;
                      align-items: center;
                      span:nth-of-type(1){
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 14/100rem;
                        color: #999999;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:20/100rem;
                      }
                      span:nth-of-type(2){
                        width: 8/100rem;
                        height:8/100rem;
                        img{
                          width: 8/100rem;
                          height:8/100rem;
                          float: left;
                        }
                      }
                    }
                  }
                  .listItembox{
                    width: 918/100rem;
                    height:162/100rem;
                    margin-top:23/100rem;
                    display: flex;
                    justify-content: space-between;
                    .listItem{
                      width:288/100rem;
                      height:162/100rem;
                      position: relative;
                      .listItemImg{
                        width:288/100rem;
                        height:162/100rem;
                        overflow: hidden;
                        img{
                          width:288/100rem;
                          height:162/100rem;
                          object-fit:cover;
                          float: left;
                          transition: 0.2s;
                        }
                        img:hover{transform:scale(1.1);}
                      }
                      .contentList{
                        z-index: 10;
                        position: absolute;
                        top:10/100rem;
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
                      .videoNum{
                        position: absolute;
                        right:12/100rem;
                        top:98/100rem;
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
                    }
                  }

                }
              }
              .channelItemBorder{
                width: 1292/100rem;
                height:26/100rem;
                background: #f9f9f9;
              }
            }

          }
          .panges{
            margin-top:55/100rem;
            margin-bottom:7/100rem;
          }
        }
      }
      .blank1{
        height:72/100rem;
      }
      .blank3{
        height:120/100rem;
      }
    }
    @media  (min-width:1301px){
      .main{
        .container{
          width: 1292px;
          margin:0 auto;
          .emptyBox{
            margin-top:10px;
            width: 1292px;
            height: 600px;
            display: flex;
            justify-content: center;
            background: #fff;
            box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
            .conBox{
              width: 210px;
              height:190px;
              margin-top:200px;
              display: flex;
              flex-direction: column;
              justify-content: space-between;
              align-items: center;
              /*align-content: space-between;*/
              .imgBox{
                width: 210px;
                height:146px;
                img{
                  width: 100%;
                  height:100%;
                  float: left;
                }
              }
              .emptyText{
                width: 210px;
                font-size: 14px;
                color: #666666;
                line-height:20px;
                font-family: PingFangSC-Regular;
              }

            }
          }
          .channelBox{
            width: 1292px;
            min-height: 500px;
            .channelItemBox{
              width: 1292px;
              .channelItem{
                margin-bottom:26px;
                z-index: 10;
                width: 1292px;
                height:251px;
                background: #ffffff;
                box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
                display: flex;
                .itemLeft{
                  width: 305px;
                  height:251px;
                  margin-left:19px;
                  .source{
                    margin-top:21px;
                    height:57px;
                    width:305px;
                    display: flex;
                    align-items: center;
                    .sourceMark{
                      width: 57px;
                      height:57px;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      background: #FFFFFF;
                      border: 1px solid #DDDDDD;
                      border-radius: 28.5px;
                      img{
                        width: 45px;
                        height:20px;
                        float: left;
                      }
                    }
                    .sourceName{
                      width: 128px;
                      margin-left:10px;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 16px;
                      color: #333333;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:57px;
                      overflow:hidden; //超出的文本隐藏
                      text-overflow:ellipsis; //溢出用省略号显示
                      white-space:nowrap; //溢出不换行
                    }
                    .subscribeBox{
                      width: 104px;
                      display: flex;
                      height:30px;
                      justify-content: flex-end;
                      .subscription{
                        margin-left:32px;
                        background: #BA0132;
                        border-radius: 2px;
                        width: 72px;
                        height:30px;
                        /*font-family: PingFangSC-Medium;font-weight:500;*/
                        font-size: 12px;
                        color: #FFFFFF;
                        letter-spacing: 0;
                        text-align: center;
                        line-height:30px;
                        cursor: pointer;
                      }
                      .subscription:hover{
                        background: #C1204A;
                      }
                      .unsubscribe{
                        border-radius: 2px;
                        padding:0 10px;
                        height:30px;
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
                  .borderBottom{
                    width:305px ;
                    height:1px;
                    background: #eeeeee;
                    margin-top:14px;
                  }
                  .itemLeft_con{
                    width: 305px;
                    height:120px;
                    margin-top:11px;
                    div{
                      width:305px;
                      height:40px;
                      display: flex;
                      span:nth-of-type(1){
                        width: 156px;
                        height:40px;
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 14px;
                        color: #999999;
                        letter-spacing: 0;
                        text-align: left;
                        line-height: 40px;
                        text-indent: 3px;
                      }
                      span:nth-of-type(2){
                        flex: 1;
                        height:40px;
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 14px;
                        color: #2A2A2A;
                        letter-spacing: 0;
                        text-align: left;
                        line-height: 40px;
                      }
                    }
                  }
                }
                .itemRight{
                  width: 918px;
                  height:251px;
                  margin-left:26px;
                  /*background: gray;*/
                  .itemRightMore{
                    width: 918px;
                    height:20px;
                    /*background: gray;*/
                    margin-top:23px;
                    display: flex;
                    justify-content: flex-end;
                    div{
                      cursor: pointer;
                      margin-right: 5px;
                      display: flex;
                      align-items: center;
                      span:nth-of-type(1){
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 14px;
                        color: #999999;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:20px;
                      }
                      span:nth-of-type(2){
                        width: 8px;
                        height:8px;
                        img{
                          width: 8px;
                          height:8px;
                          float: left;
                        }
                      }
                    }
                  }
                  .listItembox{
                    width: 918px;
                    height:162px;
                    margin-top:23px;
                    display: flex;
                    justify-content: space-between;
                    .listItem{
                      width:288px;
                      height:162px;
                      position: relative;
                      .listItemImg{
                        width:288px;
                        height:162px;
                        overflow: hidden;
                        img{
                          width:288px;
                          height:162px;
                          object-fit:cover;
                          float: left;
                          transition: 0.2s;
                        }
                        img:hover{transform:scale(1.1);}
                      }
                      .contentList{
                        z-index: 10;
                        position: absolute;
                        top:10px;
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
                      .videoNum{
                        position: absolute;
                        right:12px;
                        top:98px;
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
                    }
                  }

                }
              }
              .channelItemBorder{
                width: 1292px;
                height:26px;
                background: #f9f9f9;
              }
            }

          }
          .panges{
            margin-top:55px;
            margin-bottom:7px;
          }
        }
      }
      .blank1{
        height:72px;
      }
      .blank3{
        height:120px;
      }
    }
  }
</style>
