<template>
  <div class="main">
    <loadingPage :showLoading="showLoading"></loadingPage>
    <universalTop :indexSign="indexSign" ></universalTop>
    <videoDialog :isvideoDialogShow="isvideoDialogShow" @closeDialog = 'closeDialog'
                 @copyButtom = 'copyButtom' :shareVideoDetails="shareVideoDetails"></videoDialog>
    <div class="blank1" style=""></div>
    <div class="top_nav">
      <div class="navigation_con">
        <span style="cursor:pointer" @click="toIndex">{{$t('videoDetails.tohome')}}</span> > <span style="cursor:pointer"  @click="toClassification">{{$t('videoDetails.sort')}}</span> > <span style="cursor:pointer" @click="toConList(playlistId)">{{$t('videoDetails.list')}}</span> > <span style="color: #BA0132;cursor: pointer">{{$t('videoDetails.detail')}}</span>
      </div>
    </div>

    <div  class="container">
      <div class="conEmpty" v-if="isFitstLoading" style=""></div>
      <div class="con_main">
        <div class="main_video">
          <div class="video_box" >
            <iframe height="100%" width="100%" frameborder="0" scrolling="no"></iframe>
          </div>
          <div class="share">
            <div class="name">{{videoName}}</div>
            <div class="shareTo">
              <div class="shareToText">{{$t('videoDetails.share')}}</div>
              <div class="cursorDiv" @click="shareToSina"><img src="/static/icon/sina.png" alt=""></div>
              <div class="cursorDiv" @click="shareToEmail" ><img src="/static/icon/email.png" alt=""></div>
              <div class="cursorDiv" @click="shareToQQ"><img src="/static/icon/qq.png" alt=""></div>
            </div>
          </div>
          <div class="Video_introduction">
            <span class="con_introduction">
              {{expandMessage}}
              <span class="expand" v-if="isExpand&&isExpandShow" @click="expandClick">{{$t('videoDetails.expand')}}</span>
              <span class="expand" v-else-if="!isExpand&&isExpandShow" @click="expandClick">{{$t('videoDetails.collapse')}}</span>
            </span>
          </div>
        </div>

        <!--所在列表-->
        <div class="Attribution">
          <div class="swiper_title">{{$t('videoDetails.listHome')}}</div>
          <div class="swiper" >
            <div class="to_left"  style="cursor: pointer" @click="swiperToLeft">
              <div v-if="!canNotGoLeft"><img src="/static/icon/toLeftBlack.png" alt=""></div>
              <div v-else style="cursor: not-allowed"><img src="/static/icon/toLeft.png" alt=""></div>
            </div>
            <div class="swiper_main" v-show="swiperIsShow">
              <div class="swiper_item" v-if="swiperList1!=''" v-for="(item,index) in swiperList1 " :key="index" >
                <div class="borderBottom"></div>
                <div class="imgUrl"><img :src="item.logo" alt=""></div>
                <div class="introduction">
                  <div class="name">{{item.name}}</div>
                  <div class="num">{{item.entries_count}} {{$t('videoDetails.videos')}}</div>
                  <div class="source" @click="toChannelDetail(item.cp_id)" style="cursor: pointer;">
                    <div class="sourceImg"><img :src="item.cp_logo" alt=""></div>
                    <div class="sourceName">{{item.cp_name}}</div>
                  </div>
                </div>
                <div class="view_list"  style="" @click="toConList(item.id)">
                  <div>{{$t('videoDetails.view_list')}}</div>
                </div>
              </div>
            </div>
            <div class="swiper_main" v-show="!swiperIsShow">
              <div class="swiper_item" v-if="swiperList2!=''" v-for="(item,index) in swiperList2 " :key="index" >
                <div class="borderBottom"></div>
                <div class="imgUrl"><img :src="item.logo" alt=""></div>
                <div class="introduction">
                  <div class="name">{{item.name}}</div>
                  <div class="num">{{item.entries_count}}&nbsp;{{$t('videoDetails.videos')}}</div>
                  <div class="source" @click="toChannelDetail(item.cp_id)" style="cursor: pointer;">
                    <div class="sourceImg"><img :src="item.cp_logo" alt=""></div>
                    <div class="sourceName">{{item.cp_name}}</div>
                  </div>
                </div>
                <div class="view_list"  style="" @click="toConList(item.id)">
                  <div>{{$t('videoDetails.view_list')}}</div>
                </div>
              </div>
            </div>
            <div class="to_right" style="cursor: pointer" @click="swiperToRight">
              <div v-if="!canNotGoRight"><img src="/static/icon/toRightBlack.png" alt=""></div>
              <div v-else style="cursor: not-allowed"><img src="/static/icon/toRight.png" alt=""></div>
            </div>
          </div>
        </div>

        <!--同风格的-->
        <div class="similar">
          <div class="sil_title">
            <div style="cursor: pointer" @click="changeStyle(0)" class="sil_list">
              {{$t('videoDetails.same_list_video')}}
              <div :style="isLineShow?'background:#BB0737':'background:transparent'"></div>
            </div>
            <div style="cursor: pointer" @click="changeStyle(1)" class="sil_channel">
              {{$t('videoDetails.same_channel_video')}}
              <div :style="!isLineShow?'background:#BB0737':'background:transparent'"></div>
            </div>
          </div>

          <div class="similar_box" v-if="isLineShow">
            <div class="similar_item" @click="toVideoDetails(item.id)" v-if="similarList!=''" v-for="(item,index) in similarList" :key="index" style="cursor: pointer">
              <div class="bottomShadow2"></div>
              <div class="ingUrl"><img :src="item.logo" alt=""></div>
              <div class="name">{{item.name}}</div>
              <div class="duration">{{item.duration}}</div>
            </div>
            <div style="box-shadow: none;" class="similar_item" v-for="(item1,index1) in (3 - similarList.length%3)" v-if="similarList.length%3>0"></div>
          </div>
          <div class="pager" v-if="isLineShow && all_count>6" >
            <Pagenation :allCount='all_count' :allPage='allPage' :countPagea="countPage" :meicount="page_count" :jumpcounta="jumpCount" @nextPage="nextPage"></Pagenation>
          </div>
          <div class="reco_box" v-if="!isLineShow">
            <div class="recommed_item" v-if="recommendList1!=''" v-for="(item,index) in recommendList1" :key="index"  >
              <div class="borderShadow3"></div>
              <div class="content_list">{{$t('AllContentCategories.content_list')}}</div>
              <div  class="list_number">
                <img src="/static/icon/Group4.png" alt="">
                <span>{{item.entries_count}}{{$t('listDetails.one')}}</span>
              </div>
              <div class="imgUrl" style="cursor: pointer" @click="toConList(item.id)"><img :src="item.logo" alt=""></div>
              <div class="title">
                <div>{{item.name}}</div>
                <div>{{$t(item.genre)}}</div>
              </div>
            </div>
            <div style="box-shadow: none;" class="recommed_item" v-for="(item1,index1) in (3 - recommendList1.length%3)" v-if="recommendList1.length%3>0"></div>
          </div>
          <div class="pager" v-if="!isLineShow&& all_count1>6" >
            <Pagenation :allCount='all_count1' :allPage='allPage1' :countPagea="countPage1" :meicount="page_count1" :jumpcounta="jumpCount1" @nextPage="nextPage1"></Pagenation>
          </div>
        </div>
      </div>

      <!--更多列表推荐-->
      <div class="con_recommend">
        <div class="recommend_title">{{$t('videoDetails.videoDetails')}}</div>
        <div class="videoDetailsList">
          <div v-if="videoPricesindex!=3&&item.price!=null" class="videoDetailsList_item videoDetailsList_item3" v-for="(item,index) in videoPrices" :key="index">
            <div>{{item.num == 1?$t('videoDetails.price'):''}}</div>
            <div><span>{{item.resolution}}</span><span>{{item.price}}</span>￥</div>
          </div>

          <div v-if="videoPricesindex==3" class="videoDetailsList_item" >
            <div>{{$t('videoDetails.price')}}</div>
            <div>{{$t('videoDetails.notSet')}}</div>
          </div>

          <div class="videoDetailsList_item">
            <div>{{$t('videoDetails.duration')}}</div>
            <div>{{videoDetails.duration?videoDetails.duration:''}}</div>
          </div>
          <div class="videoDetailsList_item">
            <div>{{$t('videoDetails.receipt')}}</div>
            <div>{{videoDetails.evidence==1?$t('videoDetails.hasReceipt'):$t('videoDetails.notReceipt')}}</div>
          </div>
          <div class="videoDetailsList_item videoDetailsList_item2">
            <div>{{$t('videoDetails.price_note')}}</div>
            <div>{{videoDetails.price_note==null||videoDetails.price_note==''?$t('videoDetails.no'):price_note_message}} <span style="cursor:pointer;margin-left:5px;font-family:PingFangSC-Medium;font-weight:500;color:rgba(51,51,51,1);" v-if="isPrice_note" @click="price_note_click">{{isPrice_note_expand?$t('videoDetails.expand'):$t('videoDetails.collapse')}}</span></div>
          </div>
        </div>
        <div class="recommend_title2"></div>
        <div class="recommend_title ">{{$t('videoDetails.more_recommended')}}</div>
        <div class="reco_box">
          <div class="recommed_item" v-if="recommendList!=''" v-for="(item,index) in recommendList" :key="index"  >
            <div class="borderShadow3"></div>
            <div class="content_list">{{$t('AllContentCategories.content_list')}}</div>
            <div  class="list_number">
              <img src="/static/icon/Group4.png" alt="">
              <span>{{item.videos_count}}{{$t('listDetails.one')}}</span>
            </div>
            <div class="imgUrl" style="cursor: pointer" @click="toConList(item.ivx_id)"><img :src="item.cover_image[0]" alt=""></div>
            <div class="title">
              <div>{{item.title[0]}}</div>
              <div>{{$t(item.newgenre)}}</div>
            </div>
            <div class="source" style="cursor: pointer" @click="toChannelDetail(item.property_id)">
              <div class="sourceImg"><img :src="item.property_logo[0]" alt=""></div>
              <div class="sourceName">{{item.property_name[0]}}</div>
            </div>
          </div>
        </div>
        <div class="more_box" >
          <div style="cursor: pointer" @click="allContentList" class="recommed_more">{{$t('videoDetails.see_more')}} <span></span></div>
        </div>
      </div>
    </div>


    <div class="blank3" style="width: 100%;"></div>
    <universalBottom></universalBottom>
  </div>
</template>
<script>
  import Clipboard from 'clipboard';
  import universalTop from '../components/universalTop'
  import universalBottom from '../components/universalBottom'
  import Pagenation from '../components/pager/pager.vue'
  import loadingPage from '../components/loading/loading.vue'
  import videoDialog from '../components/emailShareDialog/videoDialog.vue'
  import { Entry,getplaylist,getPlayList } from '@/utils/global/axios.js'
  export default {
    components:{
      universalTop,
      universalBottom,
      Pagenation,
      loadingPage,
      videoDialog,
    },
    data(){
      return{
        isvideoDialogShow:false,
        all_count:0,//总条数
        allPage:0,//总页数
        countPage:1,//当前页
        page_count:6,//每页多少条
        jumpCount:1,//跳转页
        all_count1:0,//总条数
        allPage1:0,//总页数
        countPage1:1,//当前页
        page_count1:6,//每页多少条
        jumpCount1:1,//跳转页
        indexSign:false,
        swiperIsShow:true,
        swiperIndex:1,
        swiperLength:1,
        canNotGoLeft:true,
        canNotGoRight:false,
        isLineShow:true,
        showLoading:true,
        isFitstLoading:true,
        recommendList:[
        ],
        recommendList1:[],
        swiperList:[
        ],
        swiperList1:[
        ],
        swiperList2:[
        ],
        similarList:[
        ],
        ischannel:false,
        isExpand:true,
        introduction:'',
        videoName:'',
        videoId:null,
        playlistId:null,
        propertyId:null,
        isExpandShow:false,
        genreList:[],
        typeId:'',
        typeStyle:'',
        aliPlayerHtml:'',
        videoDetails:'',
        videoPrices:null,
        shareVideoDetails:'',
        videoPricesindex:0,
        isPrice_note:false,
        isPrice_note_expand:true,
      }
    },
    created(){
      let queryVideoId = this.$route.query.videoId
      this.videoId = queryVideoId.toString().split('?')[0]
      this.getHomeList()
      this.getVideoDetails()
    },
    mounted(){
      this.getaliplay()
    },
    computed: {
      expandMessage: function () {
        if(!this.isExpand){
          return this.introduction
        }else{
          if(this.introduction!=null){
            if(this.introduction.length >118){
              let arr = new Array()
              let newintroduction = this.introduction.substring(0,118)
              let point = '...'
              arr.push(newintroduction)
              arr.push(point)
              return arr.join('')
            }else{
              return this.introduction
            }
          }
        }
      },
      price_note_message:function(){
        if(!this.isPrice_note_expand){
          return this.videoDetails.price_note
        }else{
          if(this.videoDetails.price_note!=null){
            if(this.videoDetails.price_note.length >16){
              let arr = new Array()
              let newintroduction = this.videoDetails.price_note.substring(0,16)
              let point = '...'
              arr.push(newintroduction)
              arr.push(point)
              return arr.join('')
            }else{
              return this.videoDetails.price_note
            }
          }
        }
      },
      shareEmailDetail(){
        return{
          videoTitle:this.$t('shareToEmail.videoTitle'),
          Introduction:this.$t('shareToEmail.Introduction'),
          price:this.$t('shareToEmail.price'),
          commit:this.$t('shareToEmail.commit'),
          link:this.$t('shareToEmail.link'),
          videoTitle_value:this.shareVideoDetails.name,
          Introduction_value:this.shareVideoDetails.description,
          price_value:'4K 600¥',
          price_value2:'高清HD 600¥',
          commit_value:'这是一条注释内容，最多展示两行，超出部分用省略号表示，这是一条注释内容，最多展示两行，超出部分用省略号表示…',
        }
      }
    },
    methods:{
      shareToSina(){
        let ftit = `${this.$t('videoDetails.ShareVideo')}：“${this.videoName}”`;
        let flink = '';
        window.open('http://service.weibo.com/share/share.php?url=' + encodeURIComponent(document.location) + '?sharesource=weibo&title=' + ftit + '&pic=' + flink + '&appkey=4030046416');
      },
      shareToEmail(){
        let baseurl = window.location.href;
        this.isvideoDialogShow = true
        let subject = `${this.$t('videoDetails.ShareVideo')}：“${this.videoName}”`
        let that = this;
        parent.location.href = 'mailto:?subject='+subject+'&body=';

      },
      shareToQQ(){
        let ftit = `${this.$t('videoDetails.ShareVideo')}：“${this.videoName}”`;
        let flink = '';
        window.open('https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + encodeURIComponent(document.location) + '?sharesource=qzone&title=' + ftit + '&pics=' + flink );
      },
      closeDialog(){
        this.isvideoDialogShow = false
      },
      copyButtom(str){
        let newStr = JSON.stringify(str)
        let clipboard = new Clipboard('.btn',{
        });
        clipboard.on('success', function(e) {
        });
      },


      //获取单个视频详细信息
      getVideoDetails(){
        let that = this
        let params = '/' + that.videoId
        Entry( params ).then(res => {
          if(res.data){
            this.videoName = res.data.data.name
            this.introduction = res.data.data.description
            this.playlistId = res.data.data.playlistId[0]
            this.propertyId = res.data.data.propertyId
            this.videoDetails = res.data.data
            if(this.videoDetails.duration){
              this.videoDetails.duration = this.formatSeconds(this.videoDetails.duration )
            }
            this.videoPrices = this.videoDetails.prices
            let videoPricesNum = 1
            this.videoPrices.forEach((item,index)=>{
              if(item.price==null){
                this.videoPricesindex++
                item.num = 0
              }else{
                item.num = videoPricesNum
                videoPricesNum++
              }
            })
            if(this.videoDetails.price_note !=null&&this.videoDetails.price_note.length>16){
              this.isPrice_note = true
            }else{
              this.isPrice_note = false
            }

            this.shareVideoDetails = res.data.data
            if(this.introduction!=null && this.introduction.length>118){
              this.isExpandShow = true
            }else{
              this.isExpandShow = false
            }
            this.isFitstLoading = false
            this.showLoading = false
            //同频道列表
            let params1 = '?start=0&limit=6&propertyId='+this.propertyId
            return getplaylist( params1 )
          }
          this.showLoading = false

        })
        .then(res1 => {
          if(res1.data){
            this.all_count1 = res1.data.total_count
            this.allPage1 = Math.ceil(res1.data.total_count/6)
            this.countPage1 = 1
            this.recommendList1 = res1.data.data
            this.recommendList1.forEach((item,index)=>{
              this.Gloal.playlistType.forEach((item1,index1)=>{
                if(item.genre == item1.typeId){
                  item.genre = item1.name
                }
              })
            })
          }
          this.showLoading = false
          //同列表视频
          let params2 = '?start=0&limit=6&playlistId='+this.playlistId
          return Entry( params2 )
        })
        .then(res2 => {
          if(res2.data){
            this.all_count = res2.data.total_count
            this.allPage = Math.ceil(res2.data.total_count/6)
            this.countPage = 1
            this.similarList = res2.data.data
            this.similarList.forEach((item,index)=>{
              item.duration = this.formatSeconds(item.duration)
            })
          }
          this.showLoading = false
        })
        .catch(err => {
          this.showLoading =false
        })
      },

      //获取视频aliplay
      getaliplay(){
        let that = this
        let params = '/' + that.videoId+'/player'
        Entry( params ).then(res => {
          if(res.data){
            let iframe = document.getElementsByTagName('iframe')
            let ifrdoc = iframe[0].contentWindow.document;
            ifrdoc.designMode ='on';
            ifrdoc.open();
            ifrdoc.write(res.data);
            ifrdoc.close();
            ifrdoc.designMode ='off';
          }else{
            this.showLoading = false
          }
        }).catch(err => {
          this.showLoading =false
        })
      },
      //所在列表
      getHomeList(){
        let params = '?entryId=' + this.videoId
        getplaylist( params ).then(res => {
          if(res.data){
            this.swiperList = res.data.data
            this.swiperList.forEach((item,index)=>{
              this.genreList.push(item.genre)
            })
            this.genreList = this.dedupe(this.genreList)
            this.swiperLength = this.swiperList.length
            this.swiperList1.push(this.swiperList[0])
            if(this.swiperLength>1){
              this.canNotGoRight = false
            }else{
              this.canNotGoRight = true
            }
            //更多列表推荐
            let genres = this.genreList.join(',')
            let params1 = '?filter=playlist&limit=4&start=0&genre='+genres
            return  getPlayList( params1 )
          }
          this.showLoading = false
        })
        .then(res1 => {
          if(res1.data){
            this.recommendList = res1.data.response.docs
            this.recommendList.forEach((item,index)=>{
              this.Gloal.playlistType.forEach((item1,index1)=>{
                if(item.genre[0] == item1.typeId){
                  item.genre[0] = item1.name
                }
              })
              item.newgenre = item.genre.join(',')
            })
          }
          this.showLoading = false
        })
        .catch(err => {
          this.showLoading =false
        })
      },

      changeStyle(index){
        if(index==0){
          this.isLineShow = true
          this.getSamelistVideo(0)
          this.countPage = 1
        }else{
          this.isLineShow = false
          this.getSameChannelList(0)
          this.countPage1 = 1
        }
      },
      swiperToRight(){
        if(this.swiperIndex == this.swiperLength){
          return
        }else{
          this.swiperIndex++
          this.swiperJudge()
        }
      },
      swiperToLeft(){
        if(this.swiperIndex == 1){
          return
        }else{
          this.swiperIndex--
          this.swiperJudge()
        }
      },

      swiperJudge(){
          if(this.swiperList1==''){
          this.swiperList1.push(this.swiperList[this.swiperIndex-1])
          this.swiperIsShow = true
          this.swiperList2 = []
        }else{
          this.swiperList2.push(this.swiperList[this.swiperIndex-1])
          this.swiperIsShow = false
          this.swiperList1 = []
        }
        if(this.swiperIndex == 1){
          this.canNotGoLeft = true
        }else{
          this.canNotGoLeft = false
        }
        if(this.swiperIndex == this.swiperLength){
          this.canNotGoRight = true
        }else{
          this.canNotGoRight = false
        }
      },

      //同列表视频
      getSamelistVideo(start){
        let params = '?start='+start+'&limit=6&playlistId='+this.playlistId
        Entry( params ).then(res => {
          if(res.data){
            this.similarList = res.data.data
            this.similarList.forEach((item,index)=>{
              item.duration = this.formatSeconds(item.duration)
            })
          }
          this.showLoading = false

        }).catch(err => {
          this.showLoading =false
        })
      },


      //同频道列表
      getSameChannelList(start){
        let params = '?start='+start+'&limit=6&propertyId='+this.propertyId
        getplaylist( params ).then(res1 => {
          if(res1.data){
            this.recommendList1 = res1.data.data
          }
          this.showLoading = false
        }).catch(err => {
          this.showLoading =false
        })

      },
      //去重
      dedupe(array) {
        return Array.from(new Set(array));
      },




      formatSeconds(value) {
        var theTime = parseInt(value);// 秒
        var theTime1 = 0;// 分
        var theTime2 = 0;// 小时
        if(theTime > 60) {
          theTime1 = parseInt(theTime/60);
          theTime = parseInt(theTime%60);
          if(theTime1 > 60) {
            theTime2 = parseInt(theTime1/60);
            theTime1 = parseInt(theTime1%60);
          }
        }
        var result = ''
        if(theTime < 10){
          result = "0"+parseInt(theTime)+"";
        }else{
          result = ""+parseInt(theTime)+"";
        }
        if(theTime1 == 0){
          result = "0"+parseInt(theTime1)+":"+result;
        } else if(theTime1 > 0 && theTime1<10) {
          result = "0"+parseInt(theTime1)+":"+result;
        }else{
          result = ""+parseInt(theTime1)+":"+result;
        }
        if(theTime2 > 0 && theTime2<10) {
          result = "0"+parseInt(theTime2)+":"+result;
        }else if(theTime2>=10){
          result = ""+parseInt(theTime2)+":"+result;
        }
        return result;
      },

      toIndex(){
        this.$router.push({name: 'index'})
      },
      toClassification(){
        this.$router.push({name: 'allContentCategories',query:{typeStyle:0,typeId:0}})
      },
      toConList(id){
        this.$router.push({name: 'ContentListDetails',query:{playlistId:id}})
      },
      toChannelDetail(id){
        this.$router.push({name: 'channelDetails',query:{channelId:id}})
      },
      allContentList(){
        this.$router.push({name: 'allContentCategories',query:{typeStyle:1,typeId:0}})
      },
      toVideoDetails(id){
        this.$router.push({name: 'videoDetails',query:{videoId:id}})
      },
      expandClick(){
        this.isExpand = !this.isExpand
      },
      price_note_click(){
        this.isPrice_note_expand = !this.isPrice_note_expand
      },
      nextPage(val){
        this.showLoading =true
        let start = (val-1)*6
        this.countPage = val
        this.getSamelistVideo(start)
      },
      nextPage1(val){
        this.showLoading =true
        let start = (val-1)*6
        this.countPage1 = val
        this.getSameChannelList(start)
      },
    }

  }
</script>
<style scoped lang="less">
  @multiple : 100;

  @media screen {
    @media  (max-width:1300px){
      .main{
        width: 100%;
        position: relative;
        .top_nav{
          max-width: 1282/100rem;
          margin:0 auto;
          height:64/100rem;
          padding-top:50/100rem;
          .navigation_con{
            height: 20/100rem;
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 14/100rem;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:20/100rem;
          }
        }
        .container{
          margin:0 auto;
          max-width: 1282/100rem;
          display: flex;
          justify-content: space-between;
          position: relative;
          .conEmpty{
            position: absolute;
            width: 100%;
            height:100%;
            top: 0px;
            left: 0px;
            background: #ffffff;
            z-index:100;
          }
          .con_main{
            width: 958/100rem;
            display: flex;
            flex-direction: column;
            .main_video{
              width: 958/100rem;
              min-height:683/100rem;
              background: #ffffff;
              border: 1/100rem solid #EEEEEE;
              display: flex;
              flex-direction: column;
              align-items: center;
              .video_box{
                width: 880/100rem;
                height:495/100rem;
                position: relative;
                margin-top:30/100rem;
                .bg_video{
                  position: absolute;
                  right: 0;
                  bottom: 0;
                  width: 100%;
                  height:100%;
                  background-size: cover;
                }
              }
              .share{
                width: 880/100rem;
                height:28/100rem;
                margin-top:24/100rem;
                display: flex;
                justify-content: space-between;
                .name{
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 20/100rem;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:28/100rem;
                  margin-left:5/100rem;
                }
                .shareTo{
                  width: 182/100rem;
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  font-family: PingFangSC-Regular;
                  font-size: 14/100rem;
                  color: #666666;
                  letter-spacing: 0;
                  line-height:28/100rem;
                  margin-right:20/100rem;
                  .cursorDiv{
                    cursor: pointer;
                  }

                }
              }
              .Video_introduction{
                margin-top:28/100rem;
                width: 880/100rem;
                height:auto;
                font-family: PingFangSC-Regular;
                font-size: 14/100rem;
                color: #666666;
                letter-spacing: 0;
                text-indent: 20/100rem;
                text-align: left;
                padding-bottom:50/100rem;
                .con_introduction{
                }
                .expand{
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 14/100rem;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:20/100rem;
                  cursor: pointer;
                }
              }


            }
            .Attribution{
              margin-top:21/100rem;
              width: 958/100rem;
              height:280/100rem;
              background: #ffffff;
              border: 1/100rem solid #EEEEEE;
              .swiper_title{
                height: 74/100rem;
                width: 958/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20/100rem;
                color: #333333;
                letter-spacing: 0;
                text-indent: 38/100rem;
                line-height:74/100rem;
                border-bottom:1/100rem solid #dddddd;
                text-align: left;
              }
              .swiper{
                width: 958/100rem;
                height: 148/100rem;
                padding-top:23/100rem;
                padding-bottom: 34/100rem;
                display: flex;
                .swiper_main{
                  width: 785/100rem;
                  height:148/100rem;
                  -webkit-transition: 0.5s linear;
                  .swiper_item{
                    width: 785/100rem;
                    height:148/100rem;
                    box-shadow:0px 0px 12px 0px rgba(223,223,223,0.5);
                    position: relative;
                    display: flex;
                    .borderBottom{
                      width: 258/100rem;
                      height:34/100rem;
                      position: absolute;
                      top:114/100rem;
                      left:0;
                      background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                    }
                    .imgUrl{
                      width: 258/100rem;
                      height:148/100rem;
                      overflow: hidden;
                      img{
                        width: 258/100rem;
                        height:148/100rem;
                        object-fit: cover;
                        float: left;
                      }
                    }
                    .introduction{
                      width: 342/100rem;
                      padding-left:27/100rem;
                      height:148/100rem;
                      .name{
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 18/100rem;
                        color: #333333;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:25/100rem;
                        margin-top:21/100rem;
                      }
                      .num{
                        font-family: PingFangSC-Regular;
                        font-size: 14/100rem;
                        color: #999999;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:20/100rem;
                        margin-top:10/100rem;
                      }
                      .source{
                        width: 342/100rem;
                        height:55/100rem;
                        display: flex;
                        align-items: center;
                        margin-top:10/100rem;
                        .sourceImg{
                          width: 37/100rem;
                          height:37/100rem;
                          -webkit-border-radius: 37/100rem;
                          -moz-border-radius: 37/100rem;
                          background: #FFFFFF;
                          box-shadow: 0 0 4/100rem 0 rgba(195,195,195,0.50);
                          border-radius: 18.5/100rem;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          img{
                            width: 29/100rem;
                            height:13/100rem;
                          }
                        }
                        .sourceName{
                          margin-left:9/100rem;
                          font-family: PingFangSC-Regular;
                          font-size: 14/100rem;
                          color: #666666;
                          letter-spacing: 0;
                          line-height:20/100rem;
                        }
                      }

                    }
                    .view_list{
                      width: 135/100rem;
                      height:140/100rem;
                      padding-right:28/100rem;
                      display: flex;
                      justify-content: flex-end;
                      align-items: center;
                      div{
                        cursor: pointer;
                        min-width: 60/100rem;
                        padding:0 10/100rem;
                        height:30/100rem;
                        background: #ffffff;
                        color: #BA0132;
                        border:1/100rem solid #BA0132;
                        border-radius: 2/100rem;
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 12/100rem;
                        letter-spacing: 0;
                        line-height:30/100rem;
                        text-align: center;
                      }
                      div:hover{
                        color: #FFFFFF;
                        background: #BA0132;
                        border:none;
                      }
                    }
                  }
                }
                .to_left , .to_right{
                  flex:1;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  div{
                    width: 42/100rem;
                    height:42/100rem;
                    background: #999999;
                    border-radius: 42/100rem;
                    img{
                      width: 42/100rem;
                      height:42/100rem;
                      float: left;
                    }
                  }
                }

              }

            }
            .similar{
              width: 958/100rem;
              height:683/100rem;
              flex:1;
              margin-top:24/100rem;
              background: #ffffff;
              border: 1/100rem solid #EEEEEE;
              .sil_title{
                width: 958/100rem;
                height:78/100rem;
                display: flex;
                border-bottom:1/100rem solid #dddddd;
                .sil_title_ditto{
                  position: relative;
                  height: 78/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 20/100rem;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:74/100rem;
                  div{
                    width: 100%;
                    height:4/100rem;
                    position: absolute;
                    background: #ffffff;
                    bottom: 0;
                    left:0;
                  }
                }
                .sil_list{
                  .sil_title_ditto;
                  margin-left:67/100rem;
                }
                .sil_channel{
                  .sil_title_ditto;
                  margin-left:92/100rem;
                }
              }
              .similar_box{
                width: 824/100rem;
                margin-left:71/100rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;

                .similar_item{
                  width: 258/100rem;
                  height:203/100rem;
                  margin-top:29/100rem;
                  position: relative;
                  box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
                  background: #ffffff;
                  .bottomShadow2{
                    width: 258/100rem;
                    height:25/100rem;
                    position: absolute;
                    top:119/100rem;
                    left:0;
                    background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                  }
                  .ingUrl{
                    width: 258/100rem;
                    height:144/100rem;
                    overflow: hidden;
                    img{
                      width: 258/100rem;
                      height:144/100rem;
                      object-fit: cover;
                      float: left;
                    }
                  }
                  .name{
                    width: 258/100rem;
                    height:56/100rem;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18/100rem;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:56/100rem;
                    text-indent: 15/100rem;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap
                  }
                  .duration{
                    position: absolute;
                    height: 17/100rem;
                    top:122/100rem;
                    right:8/100rem;
                    text-align: right;
                    font-family: PingFangSC-Regular;
                    font-size: 12/100rem;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    line-height:17/100rem;

                  }
                }
              }
              .reco_box{
                width: 824/100rem;
                margin-left:71/100rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                .recommed_item{
                  position: relative;
                  width: 258/100rem;
                  height:208/100rem;
                  margin-top:22/100rem;
                  background: #FFFFFF;
                  box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
                  .borderShadow3{
                    width: 258/100rem;
                    height:25/100rem;
                    position: absolute;
                    top:120/100rem;
                    left:0;
                    background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                  }
                  .imgUrl{
                    width: 258/100rem;
                    height:145/100rem;
                    overflow: hidden;
                    img{
                      width: 258/100rem;
                      height:145/100rem;
                      object-fit: cover;
                      float: left;
                    }
                  }
                  .title{
                    width:258/100rem;
                    height:59/100rem;
                    padding-top:10/100rem;
                    div:nth-of-type(1){
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 18/100rem;
                      color: #333333;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:25/100rem;
                      text-indent: 18/100rem;
                    }
                    div:nth-of-type(2){
                      font-family: PingFangSC-Regular;
                      font-size: 12/100rem;
                      color: #999999;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:17/100rem;
                      margin-top:7/100rem;
                      text-indent: 18/100rem;

                    }
                  }
                  .source{
                    cursor: pointer;
                    height:59/100rem;
                    width:258/100rem;
                    display: flex;
                    align-items: center;
                    .sourceImg{
                      margin-left:18/100rem;
                      width: 37/100rem;
                      height:37/100rem;
                      -webkit-border-radius: 37/100rem;
                      -moz-border-radius: 37/100rem;
                      background: #FFFFFF;
                      box-shadow: 0 0 4/100rem 0 rgba(195,195,195,0.50);
                      border-radius: 18.5/100rem;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      img{
                        width: 29/100rem;
                        height:13/100rem;
                      }
                    }
                    .sourceName{
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 14/100rem;
                      color: #333333;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:59/100rem;
                      margin-left:9/100rem;
                    }
                  }
                  .content_list{
                    position: absolute;
                    top:11/100rem;
                    left: -5/100rem;
                    height:29/100rem;
                    padding:0 18/100rem 0 18/100rem;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14/100rem;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    line-height:29/100rem;
                    text-align: left;
                    background: #BB0737;
                    border-radius: 4/100rem;
                    z-index:101;
                  }
                  .list_number{
                    position: absolute;
                    top:88/100rem;
                    right:12/100rem;
                    width: 52/100rem;
                    height:52/100rem;
                    background: rgba(0,0,0,0.30);
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    z-index: 101;
                    -webkit-border-radius: 52/100rem;
                    -moz-border-radius: 52/100rem;
                    border-radius: 52/100rem;
                    img{
                      width: 20/100rem;
                      height: 17/100rem;
                      margin-top:10/100rem;
                    }
                    span{
                      margin-top:3/100rem;
                      font-family: PingFangSC-Regular;
                      width: 100%;
                      font-size: 12/100rem;
                      color: #FFFFFF;
                      letter-spacing: 0;
                      text-align: center;
                      line-height: 17/100rem;
                    }
                  }
                }
              }
              .pager{
                margin-top:50/100rem;
                margin-bottom:40/100rem;
                height:28/100rem;
                width: 100%;
              }
            }
          }

          .con_recommend{
            width: 304/100rem;
            background: #ffffff;
            border: 1/100rem solid #EEEEEE;
            .recommend_title{
              height:74/100rem;
              text-indent: 22/100rem;
              line-height:74/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 20/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              border-bottom: 1/100rem solid #DDDDDD;;
            }
            .recommend_title2{
              height:20/100rem;
              width: 304/100rem;
              background: #f9f9f9;
              border-top:1px solid rgba(238,238,238,1);
              border-bottom:1px solid rgba(238,238,238,1);
            }
            .videoDetailsList{
              width: 304/100rem;
              background: #ffffff;
              padding-bottom:20/100rem;
              .videoDetailsList_item{
                width: 294/100rem;
                padding-right:10/100rem;
                min-height:20/100rem;
                display: flex;
                justify-content: space-between;
                margin-top:20/100rem;
                div:nth-of-type(1){
                  width: 109/100rem;
                  min-height:20/100rem;
                  text-indent: 18/100rem;
                  font-size:14/100rem;
                  font-family:PingFangSC-Medium;
                  font-weight:500;
                  color:rgba(153,153,153,1);
                  line-height:20/100rem;
                  text-align: left;
                }
                div:nth-of-type(2){
                  width: 186/100rem;
                  min-height:20/100rem;
                  font-size:14/100rem;
                  font-family:PingFangSC-Medium;
                  font-weight:500;
                  color:rgba(102,102,102,1);
                  line-height:20/100rem;
                  text-align: left;
                }
              }
              .videoDetailsList_item2{
                min-height:20/100rem;
              }
              .videoDetailsList_item3{
                div:nth-of-type(2){
                  display: flex;
                  span:nth-of-type(2){
                    margin-left:40/100rem;
                  }
                }
              }
            }
            .reco_box{
              width: 304/100rem;
              display: flex;
              flex-direction: column;
              align-items: center;

              .recommed_item{
                position: relative;
                width: 258/100rem;
                height:274/100rem;
                margin-top:22/100rem;
                background: #FFFFFF;
                box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
                .borderShadow3{
                  width: 258/100rem;
                  height:25/100rem;
                  position: absolute;
                  top:120/100rem;
                  left:0;
                  background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                }
                .imgUrl{
                  width: 258/100rem;
                  height:145/100rem;
                  overflow: hidden;
                  img{
                    width: 258/100rem;
                    height:145/100rem;
                    object-fit: cover;
                    float: left;
                  }
                }
                .title{
                  width:258/100rem;
                  height:59/100rem;
                  padding-top:10/100rem;
                  div:nth-of-type(1){
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18/100rem;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25/100rem;
                    text-indent: 18/100rem;
                  }
                  div:nth-of-type(2){
                    font-family: PingFangSC-Regular;
                    font-size: 12/100rem;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:17/100rem;
                    margin-top:7/100rem;
                    text-indent: 18/100rem;

                  }
                }
                .source{
                  cursor: pointer;
                  height:59/100rem;
                  width:258/100rem;
                  display: flex;
                  align-items: center;
                  .sourceImg{
                    margin-left:18/100rem;
                    width: 37/100rem;
                    height:37/100rem;
                    -webkit-border-radius: 37/100rem;
                    -moz-border-radius: 37/100rem;
                    background: #FFFFFF;
                    box-shadow: 0 0 4/100rem 0 rgba(195,195,195,0.50);
                    border-radius: 18.5/100rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    img{
                      width: 29/100rem;
                      height:13/100rem;
                    }
                  }
                  .sourceName{
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14/100rem;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:59/100rem;
                    margin-left:9/100rem;
                  }
                }
                .content_list{
                  position: absolute;
                  top:11/100rem;
                  left: -5/100rem;
                  height:29/100rem;
                  padding:0 18/100rem 0 18/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 14/100rem;
                  color: #FFFFFF;
                  letter-spacing: 0;
                  line-height:29/100rem;
                  text-align: left;
                  background: #BB0737;
                  border-radius: 4/100rem;
                  z-index:101;
                }
                .list_number{
                  position: absolute;
                  top:88/100rem;
                  right:12/100rem;
                  width: 52/100rem;
                  height:52/100rem;
                  background: rgba(0,0,0,0.30);
                  display: flex;
                  flex-direction: column;
                  align-items: center;
                  z-index: 101;
                  -webkit-border-radius: 52/100rem;
                  -moz-border-radius: 52/100rem;
                  border-radius: 52/100rem;
                  img{
                    width: 20/100rem;
                    height: 17/100rem;
                    margin-top:10/100rem;
                  }
                  span{
                    margin-top:3/100rem;
                    font-family: PingFangSC-Regular;
                    width: 100%;
                    font-size: 12/100rem;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    text-align: center;
                    line-height: 17/100rem;
                  }
                }
              }
            }
            .more_box{
              width: 304/100rem;
              padding-bottom:60/100rem;
              display: flex;
              justify-content: center;
              align-items: center;
              .recommed_more{
                margin-top:41/100rem;
                width: 177/100rem;
                height:30/100rem;
                border-radius: 2/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 12/100rem;
                background: #ffffff;
                color: #BA0132;
                border:1/100rem solid #BA0132;
                letter-spacing: 0;
                text-align: center;
                line-height:30/100rem;
              }
              .recommed_more:hover{
                color: #FFFFFF;
                background: #BA0132;
                border:none;
              }
            }

          }
        }
      }
      .blank1{
        height:7200/100/100rem;
      }
      .blank3{
        height:120/100rem;
      }
    }
    @media  (min-width:1301px){
      .main{
        width: 100%;
        position: relative;
        .top_nav{
          max-width: 1282px;
          margin:0 auto;
          height:64px;
          padding-top:50px;
          .navigation_con{
            height: 20px;
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 14px;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:20px;
          }
        }
        .container{
          margin:0 auto;
          max-width: 1282px;
          display: flex;
          justify-content: space-between;
          position: relative;
          .conEmpty{
            position: absolute;
            width: 100%;
            height:100%;
            top: 0px;
            left: 0px;
            background: #ffffff;
            z-index:100;
          }
          .con_main{
            width: 958px;
            display: flex;
            flex-direction: column;
            .main_video{
              width: 958px;
              min-height:683px;
              background: #ffffff;
              border: 1px solid #EEEEEE;
              display: flex;
              flex-direction: column;
              align-items: center;
              .video_box{
                width: 880px;
                height:495px;
                position: relative;
                margin-top:30px;
                .bg_video{
                  position: absolute;
                  right: 0;
                  bottom: 0;
                  width: 100%;
                  height:100%;
                  background-size: cover;
                }
              }
              .share{
                width: 880px;
                height:28px;
                margin-top:24px;
                display: flex;
                justify-content: space-between;
                .name{
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 20px;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:28px;
                  margin-left:5px;
                }
                .shareTo{
                  width: 182px;
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  font-family: PingFangSC-Regular;
                  font-size: 14px;
                  color: #666666;
                  letter-spacing: 0;
                  line-height:28px;
                  margin-right:20px;
                  .cursorDiv{
                    cursor: pointer;
                  }
                }
              }
              .Video_introduction{
                margin-top:28px;
                width: 880px;
                height:auto;
                font-family: PingFangSC-Regular;
                font-size: 14px;
                color: #666666;
                letter-spacing: 0;
                text-indent: 20px;
                text-align: left;
                padding-bottom:50px;
                .con_introduction{
                }
                .expand{
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 14px;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:20px;
                  cursor: pointer;
                }
              }


            }
            .Attribution{
              margin-top:21px;
              width: 958px;
              height:280px;
              background: #ffffff;
              border: 1px solid #EEEEEE;
              .swiper_title{
                height: 74px;
                width: 958px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20px;
                color: #333333;
                letter-spacing: 0;
                text-indent: 38px;
                line-height:74px;
                border-bottom:1px solid #dddddd;
                text-align: left;
              }
              .swiper{
                width: 958px;
                height: 148px;
                padding-top:23px;
                padding-bottom: 34px;
                display: flex;
                .swiper_main{
                  width: 785px;
                  height:148px;
                  -webkit-transition: 0.5s linear;
                  .swiper_item{
                    width: 785px;
                    height:148px;
                    position: relative;
                    display: flex;
                    box-shadow:0px 0px 12px 0px rgba(223,223,223,0.5);
                    .borderBottom{
                      width: 258px;
                      height:34px;
                      position: absolute;
                      top:114px;
                      left:0;
                      background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                    }
                    .imgUrl{
                      width: 258px;
                      height:148px;
                      overflow: hidden;
                      img{
                        width: 258px;
                        height:148px;
                        object-fit: cover;
                        float: left;
                      }
                    }
                    .introduction{
                      width: 342px;
                      padding-left:27px;
                      height:148px;
                      .name{
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 18px;
                        color: #333333;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:25px;
                        margin-top:21px;
                      }
                      .num{
                        font-family: PingFangSC-Regular;
                        font-size: 14px;
                        color: #999999;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:20px;
                        margin-top:10px;
                      }
                      .source{
                        width: 342px;
                        height:55px;
                        display: flex;
                        align-items: center;
                        margin-top:10px;
                        .sourceImg{
                          width: 37px;
                          height:37px;
                          -webkit-border-radius: 37px;
                          -moz-border-radius: 37px;
                          background: #FFFFFF;
                          box-shadow: 0 0 4px 0 rgba(195,195,195,0.50);
                          border-radius: 18.5px;
                          display: flex;
                          align-items: center;
                          justify-content: center;
                          img{
                            width: 29px;
                            height:13px;
                          }
                        }
                        .sourceName{
                          margin-left:9px;
                          font-family: PingFangSC-Regular;
                          font-size: 14px;
                          color: #666666;
                          letter-spacing: 0;
                          line-height:20px;
                        }
                      }

                    }
                    .view_list{
                      width: 135px;
                      height:148px;
                      padding-right:28px;
                      display: flex;
                      justify-content: flex-end;
                      align-items: center;
                      div{
                        cursor: pointer;
                        min-width: 60px;
                        padding:0 10px;
                        height:30px;
                        background: #ffffff;
                        color: #BA0132;
                        border:1px solid #BA0132;
                        border-radius: 2px;
                        font-family: PingFangSC-Medium;font-weight:500;
                        font-size: 12px;
                        letter-spacing: 0;
                        line-height:30px;
                        text-align: center;
                      }
                      div:hover{
                        color: #FFFFFF;
                        background: #BA0132;
                        border:none;
                      }
                    }
                  }
                }
                .to_left , .to_right{
                  flex:1;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  div{
                    width: 42px;
                    height:42px;
                    background: #999999;
                    border-radius: 42px;
                    img{
                      width: 42px;
                      height:42px;
                      float: left;
                    }
                  }
                }

              }

            }
            .similar{
              width: 958px;
              /*height:683px;*/
              flex:1;
              margin-top:24px;
              background: #ffffff;
              border: 1px solid #EEEEEE;
              .sil_title{
                width: 958px;
                height:78px;
                display: flex;
                border-bottom:1px solid #dddddd;
                .sil_title_ditto{
                  position: relative;
                  height: 78px;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 20px;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:74px;
                  div{
                    width: 100%;
                    height:4px;
                    position: absolute;
                    background: #ffffff;
                    bottom: 0;
                    left:0;
                  }
                }
                .sil_list{
                  .sil_title_ditto;
                  margin-left:67px;
                }
                .sil_channel{
                  .sil_title_ditto;
                  margin-left:92px;
                }
              }
              .similar_box{
                width: 824px;
                margin-left:71px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;

                .similar_item{
                  width: 258px;
                  height:203px;
                  margin-top:29px;
                  position: relative;
                  box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
                  background: #ffffff;
                  .bottomShadow2{
                    width: 258px;
                    height:25px;
                    position: absolute;
                    top:119px;
                    left:0;
                    background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                  }
                  .ingUrl{
                    width: 258px;
                    height:144px;
                    overflow: hidden;
                    img{
                      width: 258px;
                      height:144px;
                      object-fit: cover;
                      float: left;
                    }
                  }
                  .name{
                    width: 258px;
                    height:56px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18px;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:56px;
                    text-indent: 15px;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap
                  }
                  .duration{
                    position: absolute;
                    height: 17px;
                    top:122px;
                    right:8px;
                    text-align: right;
                    font-family: PingFangSC-Regular;
                    font-size: 12px;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    line-height:17px;

                  }
                }
              }
              .reco_box{
                width: 824px;
                margin-left:71px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                .recommed_item{
                  position: relative;
                  width: 258px;
                  height:208px;
                  margin-top:22px;
                  background: #FFFFFF;
                  box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
                  .borderShadow3{
                    width: 258px;
                    height:25px;
                    position: absolute;
                    top:120px;
                    left:0;
                    background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                  }
                  .imgUrl{
                    width: 258px;
                    height:145px;
                    overflow: hidden;
                    img{
                      width: 258px;
                      height:145px;
                      object-fit: cover;
                      float: left;
                    }
                  }
                  .title{
                    width:258px;
                    height:59px;
                    padding-top:10px;
                    div:nth-of-type(1){
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 18px;
                      color: #333333;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:25px;
                      text-indent: 18px;
                    }
                    div:nth-of-type(2){
                      font-family: PingFangSC-Regular;
                      font-size: 12px;
                      color: #999999;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:17px;
                      margin-top:7px;
                      text-indent: 18px;

                    }
                  }
                  .source{
                    cursor: pointer;
                    height:59px;
                    width:258px;
                    display: flex;
                    align-items: center;
                    .sourceImg{
                      margin-left:18px;
                      width: 37px;
                      height:37px;
                      -webkit-border-radius: 37px;
                      -moz-border-radius: 37px;
                      background: #FFFFFF;
                      box-shadow: 0 0 4px 0 rgba(195,195,195,0.50);
                      border-radius: 18.5px;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      img{
                        width: 29px;
                        height:13px;
                      }
                    }
                    .sourceName{
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 14px;
                      color: #333333;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:59px;
                      margin-left:9px;
                    }
                  }
                  .content_list{
                    position: absolute;
                    top:11px;
                    left: -5px;
                    height:29px;
                    padding:0 18px 0 18px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14px;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    line-height:29px;
                    text-align: left;
                    background: #BB0737;
                    border-radius: 4px;
                    z-index:101;
                  }
                  .list_number{
                    position: absolute;
                    top:88px;
                    right:12px;
                    width: 52px;
                    height:52px;
                    background: rgba(0,0,0,0.30);
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    z-index: 101;
                    -webkit-border-radius: 52px;
                    -moz-border-radius: 52px;
                    border-radius: 52px;
                    img{
                      width: 20px;
                      height: 17px;
                      margin-top:10px;
                    }
                    span{
                      margin-top:3px;
                      font-family: PingFangSC-Regular;
                      width: 100%;
                      font-size: 12px;
                      color: #FFFFFF;
                      letter-spacing: 0;
                      text-align: center;
                      line-height: 17px;
                    }
                  }
                }
              }
              .pager{
                margin-top:50px;
                margin-bottom: 40px;
                height:28px;
                width: 100%;
              }
            }
          }

          .con_recommend{
            width: 304px;
            background: #ffffff;
            border: 1px solid #EEEEEE;
            .recommend_title{
              height:74px;
              text-indent: 22px;
              line-height:74px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 20px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              border-bottom: 1px solid #DDDDDD;;
            }
            .recommend_title2{
              height:20px;
              width: 304px;
              background: #f9f9f9;
              border-top:1px solid rgba(238,238,238,1);
              border-bottom:1px solid rgba(238,238,238,1);
            }
            .videoDetailsList{
              width: 304px;
              background: #ffffff;
              padding-bottom:20px;
              .videoDetailsList_item{
                width: 294px;
                padding-right:10px;
                min-height:20px;
                display: flex;
                justify-content: space-between;
                margin-top:20px;
                div:nth-of-type(1){
                  width: 109px;
                  min-height:20px;
                  text-indent: 18px;
                  font-size:14px;
                  font-family:PingFangSC-Medium;
                  font-weight:500;
                  color:rgba(153,153,153,1);
                  line-height:20px;
                  text-align: left;
                }
                div:nth-of-type(2){
                  width: 186px;
                  min-height:20px;
                  font-size:14px;
                  font-family:PingFangSC-Medium;
                  font-weight:500;
                  color:rgba(102,102,102,1);
                  line-height:20px;
                  text-align: left;
                }
              }
              .videoDetailsList_item2{
                min-height:20px;
              }
              .videoDetailsList_item3{
                div:nth-of-type(2){
                  display: flex;
                  span:nth-of-type(2){
                    margin-left:40/100rem;
                  }
                }
              }
            }
            .reco_box{
              width: 304px;
              display: flex;
              flex-direction: column;
              align-items: center;

              .recommed_item{
                position: relative;
                width: 258px;
                height:274px;
                margin-top:22px;
                background: #FFFFFF;
                box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
                .borderShadow3{
                  width: 258px;
                  height:25px;
                  position: absolute;
                  top:120px;
                  left:0;
                  background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                }
                .imgUrl{
                  width: 258px;
                  height:145px;
                  overflow: hidden;
                  img{
                    width: 258px;
                    height:145px;
                    object-fit: cover;
                    float: left;
                  }
                }
                .title{
                  width:258px;
                  height:59px;
                  padding-top:10px;
                  div:nth-of-type(1){
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18px;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25px;
                    text-indent: 18px;
                  }
                  div:nth-of-type(2){
                    font-family: PingFangSC-Regular;
                    font-size: 12px;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:17px;
                    margin-top:7px;
                    text-indent: 18px;

                  }
                }
                .source{
                  cursor: pointer;
                  height:59px;
                  width:258px;
                  display: flex;
                  align-items: center;
                  .sourceImg{
                    margin-left:18px;
                    width: 37px;
                    height:37px;
                    -webkit-border-radius: 37px;
                    -moz-border-radius: 37px;
                    background: #FFFFFF;
                    box-shadow: 0 0 4px 0 rgba(195,195,195,0.50);
                    border-radius: 18.5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    img{
                      width: 29px;
                      height:13px;
                    }
                  }
                  .sourceName{
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14px;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:59px;
                    margin-left:9px;
                  }
                }
                .content_list{
                  position: absolute;
                  top:11px;
                  left: -5px;
                  height:29px;
                  padding:0 18px 0 18px;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 14px;
                  color: #FFFFFF;
                  letter-spacing: 0;
                  line-height:29px;
                  text-align: left;
                  background: #BB0737;
                  border-radius: 4px;
                  z-index:101;
                }
                .list_number{
                  position: absolute;
                  top:88px;
                  right:12px;
                  width: 52px;
                  height:52px;
                  background: rgba(0,0,0,0.30);
                  display: flex;
                  flex-direction: column;
                  align-items: center;
                  z-index: 101;
                  -webkit-border-radius: 52px;
                  -moz-border-radius: 52px;
                  border-radius: 52px;
                  img{
                    width: 20px;
                    height: 17px;
                    margin-top:10px;
                  }
                  span{
                    margin-top:3px;
                    font-family: PingFangSC-Regular;
                    width: 100%;
                    font-size: 12px;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    text-align: center;
                    line-height: 17px;
                  }
                }
              }
            }
            .more_box{
              width: 304px;
              padding-bottom:60px;
              display: flex;
              justify-content: center;
              align-items: center;
              .recommed_more{
                margin-top:41px;
                width: 177px;
                height:30px;
                border-radius: 2px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 12px;
                background: #ffffff;
                color: #BA0132;
                border:1px solid #BA0132;
                letter-spacing: 0;
                text-align: center;
                line-height:30px;
              }
              .recommed_more:hover{
                color: #FFFFFF;
                background: #BA0132;
                border:none;
              }
            }

          }
        }
      }
      .blank1{
        height:7200/100px;
      }
      .blank3{
        height:120px;
      }
    }
  }

</style>
