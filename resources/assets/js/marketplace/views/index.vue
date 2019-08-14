<template>
  <div class="main"  @click="mainClick">
    <universalTop :indexSign="indexSign" :isSearchSHow="isSearchSHow"></universalTop>
    <div class="topViewBox">
      <div class="topView">
        <div class="topViewShadow"></div>
        <div class="topviewName">{{$t('index.title')}}</div>
        <div class="topviewMinor">{{$t('index.sub_title')}}</div>
        <div class="topviewCnt">
          <div class="inputBox">
            <div class="inputContent">
              <div class="input"><input type="text" v-model="homeInputKeywords" :placeholder="$t('index.seach_placeholder')"></div>
              <div class="select">
                <div class="inputVerticalLine" style="width: 1px;background:#D8D8D8;"></div>
                <div class="sclectItem" style="cursor:pointer" @click.stop="filterallClick">
                  <div class="sclectItemName">{{$t(filterAll)}}</div>
                  <div v-if="!filterAllShow" class="sclectItemImg" ><img src="/static/img/openMore.png" alt=""></div>
                  <div v-else class="sclectItemImg" ><img src="/static/icon/upward.png" alt=""></div>
                </div>
              </div>
            </div>
            <div style="cursor:pointer" @click.stop="homeInputSearch" class="inputSearch">{{$t('index.search')}}</div>
            <div class="filterAllOpen" v-if="filterAllShow">
              <div style="" class="redline"></div>
              <div class="filterAllOpenItem" @click.stop="changeFilterAll(index)" v-for="(item,index) in filterAllOpenList" :key="index">{{$t(item.name)}}</div>
            </div>
          </div>
        </div>
        <video class="bg_video" src="http://img.ksbbs.com/asset/Mon_1703/eb048d7839442d0.mp4" width="100%"   autoplay loop muted></video>
      </div>
    </div>
    <div class="LeaveBlank1"></div>
    <div class="Media media1" style="">
      <div class="MediaTitle">{{$t('index.media_title')}}</div>
      <div class="mediaMinor">{{$t('index.sub_media_title')}}</div>
    </div>

    <div class="mediaType">
      <div class="mediaTypeItem" @click="changeMediaType(index)"  v-for="(item,index) in mediaTypeList" :key="index">
        <div v-if="!item.isImg"><img :src="item.img" alt=""></div>
        <div v-else><img :src="item.hoverImg" alt=""></div>
        <div :style="item.isImg?'color: #BA0334':'color:#000000'">{{$t(item.lanage)}}</div>
      </div>
    </div>

    <div class="mainContent">
      <!--媒体资源-->
      <div class="mediaBox">
        <div class="hoverItem" v-for="(item,index) in mediaList" :key="index">
          <div class="shadow"></div>
          <div class="mediaItem">
            <div class="shadowBottom"></div>
            <div style="cursor:pointer" @click="toCOntentListDetails(item.id)" class="hoverImg"><img src="/static/img/toMedia.png" alt=""></div>
            <div class="hoverName">{{item.name}}</div>
            <div class="hoverMinor">{{$t(item.genre)}}</div>
            <div class="hoverDescription">{{item.description?item.description:''}}</div>
            <div class="itemImg"><img :src="item.logo" alt=""></div>
            <div class="itemName">{{item.name}}</div>
            <div class="itemDescription">{{$t(item.genre)}}</div>
            <div class="source">
              <div class="sourceImg"><img :src="item.property_logo" alt=""></div>
              <div class="sourceName">{{item.property_name}}</div>
            </div>
          </div>
        </div>
        <div class="hoverItem" v-for="(item,index) in (4-mediaList.length%4)" v-if="mediaList.length%4>0"></div>
      </div>
      <div class="LeaveBlank1"></div>
      <div class="Media">
        <div class="MediaTitle">{{$t('index.content_title')}}</div>
        <div class="mediaMinor">{{$t('index.sub_content_title')}}</div>
      </div>
      <!--内容分类-->
      <div class="sortBox">
        <div style="margin:0 auto;display: flex;flex-wrap:wrap;justify-content: space-between">
          <div style="" v-if="item.id!=0" class="hoversortItem" v-for="(item,index) in sortList" :key="index" @click="toAllContent(item.id,0)">
            <div class="shadowsortItem"></div>
            <div class="sortItem" >
              <div class="textSOrtItem">{{$t(item.lanage)}}</div>
              <img :src="item.imgUrl" alt="">
            </div>
          </div>
        </div>

        <div class="blank1" style="width:100%;"></div>
        <div class="arrowBox"  style="">
          <div class="arrow"  style="cursor:pointer" @click="toAllContent(0,0)">
            <img style="" src="/static/img/toMore.png" alt="">
          </div>
        </div>
      </div>
      <div class="LeaveBlank1"></div>
      <div class="MediaNew">
        <div class="MediaTitleNew">{{$t('index.playlistTitle')}}</div>
        <div class="mediaMinorNew">{{$t('index.playlistsubTitle')}}</div>
      </div>
      <div class="SelectionRange">
        <div class="SelectionRangeBox">
          <div :style="playlistisChina?'background:#BB0737;color:#ffffff':''" @click="playlistSelectionRange(0)">{{$t('index.chinaRegion')}}</div>
          <div :style="!playlistisChina?'background:#BB0737;color:#ffffff':''" @click="playlistSelectionRange(1)">{{$t('index.internationalArea')}}</div>
        </div>
      </div>
      <!--环宇精选-->
      <div class="playlistBox">
        <div class="contentList">
          <div class="hoverItem" v-for="(item,index) in mediaList1" :key="index" >
            <div  class="content_list">{{$t('AllContentCategories.content_list')}}</div>
            <div  class="list_number">
              <img src="/static/icon/Group4.png" alt="">
              <span>{{item.entries_count}}<span>{{$t('index.one')}}</span></span>
            </div>
            <!--<div v-if="!item.type == 'video'" class="duration">-->
              <!--{{item.newduration}}-->
            <!--</div>-->
            <div class="mediaItem">
              <div class="bottomSharow"></div>
              <div class="itemImg" style="cursor:pointer"  @click="toDetails(item.type,item.id)"><img :src="item.logo" alt=""></div>
              <div class="itemName">{{item.name}}</div>
              <div class="itemDescription">{{$t(item.genre)}}</div>
              <div class="source" style="cursor:pointer"  @click="toChannelDetail(item.cp_id)">
                <div class="sourceImg"><img :src="item.cp_logo" alt=""></div>
                <div class="sourceName">{{item.cp_name}}</div>
              </div>
            </div>
          </div>
          <div class="hoverItem" v-for="(item1,index1) in (4-mediaList1.length%4)" v-if="mediaList1.length%4>0"></div>
        </div>
      </div>
      <div class="blank1" style="width:100%;"></div>
      <div class="arrowBox"  style="">
        <div class="arrow"  style="cursor:pointer" @click="">
          <img style="" src="/static/img/toMore.png" alt="">
        </div>
      </div>
      <div class="LeaveBlank1"></div>
      <div class="Media">
        <div class="MediaTitle">{{$t('index.channel_title')}}</div>
        <div class="mediaMinor">{{$t('index.sub_channel_title')}}</div>
      </div>
      <div class="SelectionRange" >
        <div class="SelectionRangeBox">
          <div :style="channelChina?'background:#BB0737;color:#ffffff':''" @click="channelSelectionRange(0)">{{$t('index.chinaRegion')}}</div>
          <div :style="!channelChina?'background:#BB0737;color:#ffffff':''" @click="channelSelectionRange(1)">{{$t('index.internationalArea')}}</div>
        </div>
      </div>
      <!--待定-->
      <!--频道-->
      <div class="ChannelBox" :style="!channelChina?styleBackground2:styleBackground1 ">
        <div v-if="index <=7" @click="toChannelDetail(item.id)" style="cursor:pointer" class="itemBox channeldiv" v-for="(item,index) in channelList" :key="index">
          <div class="item" >
            <div class="topUrl"><img :src="item.logo" alt=""></div>
            <div class="channelName">{{item.name}}</div>
            <div class="hoverNum">{{$t('index.video')}} <span>{{item.entry_count}}</span></div>
          </div>
        </div>
        <div class="arrowBox"  style="">
          <div class="arrow" @click="toAllChannels" style="cursor:pointer">
            <img style="" src="/static/img/toMore.png" alt="">
          </div>
        </div>
      </div>

      <div class="LeaveBlank1"></div>
      <div class="Media">
        <div class="MediaTitle">{{$t('index.partner_title')}}</div>
        <div class="mediaMinor">{{$t('index.sub_partner_title')}}</div>
      </div>
      <!--合作伙伴-->
      <div class="Partner">
          <swiper class="swiperBox" :options="swiperOption" ref="mySwiper">
            <!-- slides -->
            <swiper-slide :style="" class="swiperSlide" v-for="slide in swiperSlides"  :key="slide.id">
              <a :href="slide.link"><img :src="slide.img" alt=""></a>
            </swiper-slide>
          </swiper>
          <div class="swiper-button-prev" slot="button-prev">
            <img src="/static/icon/toLeftBlack.png" alt="">
          </div>

          <div class="swiper-button-next" slot="button-next">
            <img src="/static/icon/toRightBlack.png" alt="">
          </div>
      </div>
    </div>

    <universalBottom></universalBottom>

  </div>
</template>
<script>
  require('swiper/dist/css/swiper.css');
  import universalTop from '../components/universalTop'
  import universalBottom from '../components/universalBottom'
  import { swiper, swiperSlide } from 'vue-awesome-swiper'
  import {Car, getPlayList , getChannels,getplaylist,Entry } from '@/utils/global/axios.js'
  export default {
    components: {
      universalTop,
      universalBottom,
      swiper,
      swiperSlide,
    },
    computed:{
      mediaTypeIndex(){
        return this.$store.state.mediaTypeIndex
      },
    },
    data(){
      return {
        swiperOption: {
          notNextTick: true,
          autoplay: 3000,
          loop: true,
          spaceBetween:20,
          slidesPerView:4,
          centerInsufficientSlides:true,
          autoplayDisableOnInteraction : false,
          prevButton:'.swiper-button-next',
          nextButton:'.swiper-button-prev',
        },
        styleBackground1:"background: url('/static/icon/chinaBgd.png') no-repeat",
        styleBackground2:"background: url('/static/icon/worldBgd.png') no-repeat",
        swiperSlides:[
          {
            img: 'http://cnctest.ivideocloud.cn/images/marketplace/xinhua_logo.jpg',
            link: 'http://www.xinhuanet.com/'
          },
          {
            img: 'http://cnctest.ivideocloud.cn/images/marketplace/huamei.jpg',
            link: 'http://hztv.hangzhou.com.cn/'
          },
          {
            img: 'http://cnctest.ivideocloud.cn/images/marketplace/zhejiang_logo.jpg',
            link: 'https://v.zjstv.com/'
          },
          {
            img: 'http://cnctest.ivideocloud.cn/images/marketplace/meredith_logo.jpg',
            link: 'https://www.meredith.com/'
          },
        ],
        filterAllOpenList:[
          {
            text:this.$t('index.all'),
            name:'index.all',
            title:''
          },{
            text:this.$t('index.videoListOnly'),
            name:'index.videoListOnly'
          },{
            text:this.$t('index.videoOnly'),
            name:'index.videoOnly'
          },
        ],
        filterAll:'index.all',
        TypeAll:'index.all',
        homeInputKeywords:'',
        filterAllShow:false,
        typeAllShow:false,
        playlistisChina:true,
        channelChina:true,
        scrollTop:window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop,
        isOpenView:false,
        isSearchSHow:false,
        documentWidth:window.innerWidth,
        playerOptions : {
          playbackRates: [0.7, 1.0, 1.5, 2.0], //播放速度
          autoplay: false, //如果true,浏览器准备好时开始回放。
          muted: false, // 默认情况下将会消除任何音频。
          loop: false, // 导致视频一结束就重新开始。
          preload: 'auto', // 建议浏览器在<video>加载元素后是否应该开始下载视频数据。auto浏览器选择最佳行为,立即开始加载视频（如果浏览器支持）
          language: 'zh-CN',
          aspectRatio: '16:9', // 将播放器置于流畅模式，并在计算播放器的动态大小时使用该值。值应该代表一个比例 - 用冒号分隔的两个数字（例如"16:9"或"4:3"）
//          fluid: true, // 当true时，Video.js player将拥有流体大小。换句话说，它将按比例缩放以适应其容器。
          sources: [{
            type: "video/mp4",
            src: "http://img.ksbbs.com/asset/Mon_1703/eb048d7839442d0.mp4" //url地址
          }],
          poster: "../../static/images/test.jpg", //你的封面地址
          // width: document.documentElement.clientWidth,
          notSupportedMessage: '此视频暂无法播放，请稍后再试', //允许覆盖Video.js无法播放媒体源时显示的默认信息。
          controlBar: {
          }
        },
        indexSign:true,
        ischinese:false,
        mediaList:[],
        mediaTypeList:[
          {
            title:this.$t('index.generalSecretaryVideo'),
            lanage:'index.generalSecretaryVideo',
            isImg:false,
            isClick:false,
            img:'/static/icon/media_generalSecretaryVideo.png',
            hoverImg:'/static/icon/media_hover_generalSecretaryVideo.png',
          },{
            title:this.$t('index.chineseVideo'),
            lanage:'index.chineseVideo',
            isImg:false,
            isClick:false,
            img:'/static/icon/media_chineseVideo.png',
            hoverImg:'/static/icon/media_hover_chineseVideo.png',
          },{
            title:this.$t('index.internationalVideo'),
            lanage:'index.internationalVideo',
            isImg:true,
            isClick:true,
            img:'/static/icon/media_internationalVideo.png',
            hoverImg:'/static/icon/media_hover_internationalVideo.png',
          },
        ],
        sortList:[
          {
            name:this.$t('index.all'),
            lanage:'',
            id:0,
          },
          {
            imgUrl:'/static/type/politics.png',
            name:this.$t('index.politics'),
            lanage:'index.politics',
            id:1,
          },{
            imgUrl:'/static/type/military.png',
            name:this.$t('index.military'),
            lanage:'index.military',
            id:2,
          },{
            imgUrl:'/static/type/finance.png',
            name:this.$t('index.finance'),
            lanage:'index.finance',
            id:3,
          },{
            imgUrl:'/static/type/science.png',
            name:this.$t('index.science'),
            lanage:'index.science',
            id:4,
          },{
            imgUrl:'/static/type/society.png',
            name:this.$t('index.society'),
            lanage:'index.society',
            id:5,
          },{
            imgUrl:'/static/type/character.png',
            name:this.$t('index.character'),
            lanage:'index.character',
            id:6,
          },{
            imgUrl:'/static/type/cultrue.png',
            name:this.$t('index.cultrue'),
            lanage:'index.cultrue',
            id:7,
          },{
            imgUrl:'/static/type/education.png',
            name:this.$t('index.education'),
            lanage:'index.education',
            id:8,
          },{
            imgUrl:'/static/type/nature.png',
            name:this.$t('index.nature'),
            lanage:'index.nature',
            id:9,
          },{
            imgUrl:'/static/type/food.png',
            name:this.$t('index.food'),
            lanage:'index.food',
            id:10,
          },{
            imgUrl:'/static/type/health.png',
            name:this.$t('index.health'),
            lanage:'index.health',
            id:11,
          },{
            imgUrl:'/static/type/tourism.png',
            name:this.$t('index.tourism'),
            lanage:'index.tourism',
            id:12,
          },{
            imgUrl:'/static/type/sports.png',
            name:this.$t('index.sports'),
            lanage:'index.sports',
            id:13,
          },{
            imgUrl:'/static/type/automobile.png',
            name:this.$t('index.automobile'),
            lanage:'index.automobile',
            id:14,
          },{
            imgUrl:'/static/type/entertainment.png',
            name:this.$t('index.entertainment'),
            lanage:'index.entertainment',
            id:15,
          },{
            imgUrl:'/static/type/others.png',
            name:this.$t('index.others'),
            lanage:'index.others',
            id:16,
          },
        ],
        channelList:[],
        isLanageShow: false,
        clickAll:false,
        mediaList1:[],
        generalSecretaryVideoList:[],
        chineseVideoList:[],
        internationalVideoList:[],
        playlistChinese:[],
        playlistinternational:[],
      }
    },
    created(){
      if(this.mediaTypeIndex == 0){
        this.getGeneralSecretaryVideo()
      }else if(this.mediaTypeIndex == 1){
        this.getChineseVideo()
      }else if(this.mediaTypeIndex == 2){
        this.getinternationalVideo()
      }
      this.getChinesePlaylist()
      this.getAllChannels('china')
    },
    mounted(){
      this.mediaTypeList.forEach((item,index)=>{
        if(index == this.mediaTypeIndex ){
          item.isClick = true
          item.isImg = true
        }else{
          item.isClick = false
          item.isImg = false
        }
      })
      window.addEventListener('scroll', this.handleScroll)    //在mounted钩子中给window添加一个滚动滚动监听事件，
      document.querySelector('body').removeEventListener('click', this.mainClick);
    },
    beforeDestroy(){
      document.querySelector('body').removeEventListener('click', this.mainClick);
    },
    witch:{
      scrollTop(val){
      }
    },
    methods:{
      //今日媒体资源选择类型
      changeMediaType(index){
        this.$store.commit('newMediaTypeIndex',index)
        this.mediaTypeList.forEach((item,index1)=>{
          if(index == index1){
            item.isClick = true
            item.isImg = true
          }else{
            item.isClick = false
            item.isImg = false
          }
        })
        if(index == 0){
          if(this.generalSecretaryVideoList == ''||this.generalSecretaryVideoList == null){
            this.getGeneralSecretaryVideo()
          }else{
            this.mediaList = this.generalSecretaryVideoList
          }
        }else if(index==1){
          if(this.chineseVideoList == ''||this.chineseVideoList == null){
            this.getChineseVideo()
          }else{
            this.mediaList = this.chineseVideoList
          }
        }else if(index==2){
          if(this.internationalVideoList == ''||this.internationalVideoList == null){
            this.getinternationalVideo()
          }else{
            this.mediaList = this.internationalVideoList
          }
        }
      },
      //环宇精选选择区域
      playlistSelectionRange(index){
        if(index==0){
          this.playlistisChina = true
          if(this.playlistChinese == ''||this.playlistChinese == null){
            this.getChinesePlaylist()
          }else{
            this.mediaList1 = this.playlistChinese
          }
        }else{
          this.playlistisChina = false
          if(this.playlistinternational == ''||this.playlistinternational == null){
            this.getInternationalplaylist()
          }else{
            this.mediaList1 = this.playlistinternational
          }
        }
      },
      //媒体频道选择区域
      channelSelectionRange(index){
        if(index==0 && this.channelChina!=true){
          this.channelChina=true
          this.getAllChannels('china')
        }else{
          this.channelChina=false
          this.getAllChannels('international')
        }
      },
      changeFilterAll(index){
        this.filterAll = this.filterAllOpenList[index].name
        this.filterAllShow = false
      },
      changeTypeAll(index){
        this.TypeAll = this.sortList[index].name
        this.typeAllShow = false
      },
      mainClick(){
        this.filterAllShow = false
        this.typeAllShow = false
      },
      filterallClick(){
        this.filterAllShow = !this.filterAllShow
        this.typeAllShow = false
      },
      typeAllClick(){
        this.typeAllShow = !this.typeAllShow
        this.filterAllShow = false
      },
      toDetails(type,id){
        if(type == 'playlist'){
          this.$router.push({name: 'ContentListDetails',query:{playlistId:id}})
        }else{
          this.$router.push({name: 'videoDetails',query:{videoid:id}})
        }
      },
      toChannelDetail(id){
        this.$router.push({name: 'channelDetails',query:{channelId:id}})
      },
      openVideo(){
        this.isOpenView = !this.isOpenView
      },
      mouseover(index){
        this.mediaTypeList[index].isImg = true
      },
      mouseLeave(index){
        if(this.mediaTypeList[index].isClick){
          this.mediaTypeList[index].isImg = true
        }else{
          this.mediaTypeList[index].isImg = false
        }

      },


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


      //获取所有频道列表
      getAllChannels(range){
        let params = '?start=0&limit=8&region='+range
        getChannels(params).then(res=>{
          this.channelList = res.data.data
        }).catch(err=>{

        })
      },

      //获取今日媒体资源总书记视频
      getGeneralSecretaryVideo(){
        let params = '?limit=8&start=0&propertyId=144'
        Entry(params).then(res=>{
          this.generalSecretaryVideoList = res.data.data
          this.generalSecretaryVideoList.forEach((item,index)=>{
            this.Gloal.playlistType.forEach((item1,index1)=>{
              if(item.genre == item1.typeId){
                item.genre = item1.name
              }
            })
          })
          this.mediaList = this.generalSecretaryVideoList
        }).catch(err=>{

        })
      },

      //获取今日媒体资源中国视频
      getChineseVideo(){
        let params = '?limit=8&start=0&propertyId=224'
        Entry(params).then(res=>{
          this.chineseVideoList = res.data.data
          this.chineseVideoList.forEach((item,index)=>{
            this.Gloal.playlistType.forEach((item1,index1)=>{
              if(item.genre == item1.typeId){
                item.genre = item1.name
              }
            })
          })
          this.mediaList = this.chineseVideoList
        }).catch(err=>{

        })
      },

      //获取今日媒体资源国际视频
      getinternationalVideo(){
        let params = '?limit=8&start=0&propertyId=149'
        Entry(params).then(res=>{
          this.internationalVideoList= res.data.data
          this.internationalVideoList.forEach((item,index)=>{
            this.Gloal.playlistType.forEach((item1,index1)=>{
              if(item.genre == item1.typeId){
                item.genre = item1.name
              }
            })
          })
          this.mediaList = this.internationalVideoList
        }).catch(err=>{

        })
      },

      //获取中国区域寰宇精选
      getChinesePlaylist(){
        let params = '?limit=8&start=0&propertyId=144,224,149,215,217'
        getplaylist(params).then(res=>{
          this.playlistChinese = res.data.data
          this.playlistChinese.forEach((item,index)=>{
            item.type='playlist'
            this.Gloal.playlistType.forEach((item1,index1)=>{
              if(item.genre == item1.typeId){
                item.genre = item1.name
              }
            })
          })
          this.mediaList1 = this.playlistChinese
        }).catch(err=>{

        })
      },
      //获取全球区域环宇精选
      getInternationalplaylist(){
        let params = '?limit=8&start=0&propertyId=7,8,9,10,140,141,142,143,148,159,175,176,177'
        getplaylist(params).then(res=>{
          this.playlistinternational = res.data.data
          this.playlistinternational.forEach((item,index)=>{
            item.type='playlist'
            this.Gloal.playlistType.forEach((item1,index1)=>{
              if(item.genre == item1.typeId){
                item.genre = item1.name
              }
            })
          })
          this.mediaList1 = this.playlistinternational
        }).catch(err=>{

        })
      },
      homeInputSearch(){
        let filterType = this.filterAll.split('.')[1]
        let filter = ''
        let filterIndex = 0
        if(filterType == 'all'){
          filter = 'all'
          filterIndex = 0
        }else if(filterType == 'videoListOnly'){
          filter = 'playlist'
          filterIndex = 1
        }else if(filterType == 'videoOnly'){
          filterIndex = 2
          filter = 'video'
        }
        if(this.homeInputKeywords!=''){
          this.$store.commit('newTypeId',0)
          this.$store.commit('newTypeStyle',filterIndex)
          this.$router.push({name: 'allContentCategories',query:{keyWoeds:this.homeInputKeywords,filter:filter}})
        }

      },

      toCOntentListDetails(index){
        this.$router.push({name: 'videoDetails', query: {videoId: index}})
      },
      toAllContent(index,style){
        this.$store.commit('newTypeId',index)
        this.$store.commit('newTypeStyle',style)
        this.$router.push({name: 'allContentCategories', query: {typeId: index,typeStyle:style}})
      },
      toAllChannels(){
        this.$router.push({name: 'allChannel'})
      },
    }
  }


</script>

<style scoped lang="less">

  .main{
    margin:0 auto;
    width:100%;
    position: relative;
  }
  @media screen {

    @media  (max-width:1301px){
      .topView:before{
        content:"";
        display: table;
      }
      .topView:after{
        content:"";
        display: table;
        clear: both;
      }

      .topViewBox{
        width: 100%;
        overflow: hidden;
        position: relative;
        .topView{
          /*margin: 0 auto;*/
          width: 100%;
          height:800/100rem;
          background: #000000;
          position: relative;
          left: 50%;
          transform: translateX(-50%);
          .bg_video{
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
            /* background: url(polina.jpg) no-repeat; */
            background-size: cover;
            /*position: absolute;*/
            /*left:0;*/
            /*top:0/100rem;*/
            /*z-index:-1;*/
            /*background: gray;*/
          }
          .topViewShadow{
            position: absolute;
            top:0;
            max-width: 2000/100rem;
            height:800/100rem;
            background: #000000;
            opacity: 0.2;
            z-index:10;
          }
          .topviewName{
            position: absolute;
            top:239/100rem;
            width: 100%;
            height:56/100rem;
            font-family: PingFangSC-Semibold;font-weight:600;
            font-size: 40/100rem;
            color: #FFFFFF;
            letter-spacing: 0;
            text-align: center;
            line-height:56/100rem;
            z-index:10;
          }
          .topviewMinor{
            position: absolute;
            top:325/100rem;
            width: 100%;
            height:33/100rem;
            font-family: PingFangSC-Regular;
            font-size: 24/100rem;
            color: #FFFFFF;
            letter-spacing: 1.85/100rem;
            text-align: center;
            line-height:33/100rem;
            z-index:10
          }
          .watchVideoBox{
            position: absolute;
            top:668/100rem;
            width: 100%;
            display: flex;
            justify-content: center;
            .watchVideo{
              width: 100%;
              height:92/100rem;
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: space-between;
              z-index: 10;
              div:nth-of-type(1){
                width: 48/100rem;
                height:48/100rem;
                img{
                  width: 48/100rem;
                  height:48/100rem;
                  float: left;
                }
              }
              div:nth-of-type(2){
                /*width: 80/100rem;*/
                height:28/100rem;
                font-family: PingFangSC-Regular;
                font-size: 20/100rem;
                color: #FFFFFF;
                letter-spacing: 0;
                line-height:28/100rem;
              }
            }
          }

          .topviewCnt{
            position: absolute;
            top:444/100rem;
            width: 100%;
            height:111/100rem;
            display: flex;
            justify-content: center;
            z-index: 10;
            .inputBox{
              width: 1008/100rem;
              height:111/100rem;
              background: rgba(255,255,255,0.40);
              border-radius: 4/100rem;
              display: flex;
              align-items: center;
              position: relative;
              .filterAllOpen{
                position: absolute;
                top:98/100rem;
                left: 693/100rem;
                /*width: 114/100rem;*/
                height: 148/100rem;
                z-index:200;
                background:#ffffff;
                .redline{
                  width: 100%;
                  height:4/100rem;
                  background: #BA0132;
                }
                .filterAllOpenItem{
                  /*width: 114/100rem;*/
                  height:48/100rem;
                  background: #ffffff;
                  font-family: PingFangSC-Regular;
                  font-size: 14/100rem;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  padding:0 18/100rem;
                  line-height:48/100rem;
                  cursor: pointer;

                }
                .filterAllOpenItem:hover{
                  background: #F6F6F6;

                }
              }
              .typeAllOpen{
                width: 320/100rem;
                height:190/100rem;
                position: absolute;
                top:98/100rem;
                left:688/100rem;
                z-index:200;
                background: #ffffff;
                .redline{
                  width: 320/100rem;
                  height:4/100rem;
                  background: #BA0132;
                }
                .typeAllOpenBox{
                  width: 320/100rem;
                  height:186/100rem;
                  display: flex;
                  flex-wrap: wrap;
                  align-content:space-around;
                  .typeAllOpenItem{
                    cursor: pointer;
                    width: 28/100rem;
                    height:20/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 14/100rem;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:20/100rem;
                    margin-left: 30/100rem;
                  }
                  .typeAllOpenItem:hover{
                    color: #BA0132;
                  }
                }


              }
              .inputContent{
                margin-left:41/100rem;
                width: 762/100rem;
                height:51/100rem;
                background: #ffffff;
                display: flex;
                justify-content: space-between;
                .select{
                  height:51/100rem;
                  width:135/100rem;
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  .sclectItem{
                    width: 118/100rem;
                    height:51/100rem;
                    display: flex;
                    .sclectItemName{
                      height:51/100rem;
                      min-width: 60/100rem;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 16/100rem;
                      color: #666666;
                      letter-spacing: 0;
                      line-height:51/100rem;
                      text-align: right;
                    }
                    .sclectItemImg{
                      margin-left:6/100rem;
                      display: flex;
                      align-items: center;
                      img{
                        width: 11/100rem;
                        height:6/100rem;
                      }

                    }
                  }
                }
                .input{
                  width: 618/100rem;
                  height:51/100rem;
                  line-height:51/100rem;
                  color: #000000;
                  font-size: 14/100rem;
                  input{
                    width: 618/100rem;
                    height:49/100rem;
                    /*background: blue;*/
                    border:none;
                    text-align: left;
                    text-indent: 18/100rem;
                    outline-style: none ;
                    outline-width: 0/100rem ;
                    text-shadow: none ;
                    -webkit-appearance: none ;
                    -webkit-user-select: text ;
                    outline-color: transparent ;
                    box-shadow: none;
                  }
                  input::-webkit-input-placeholder {
                    font-size: 14/100rem;
                    color: #999999;
                    /* placeholder位置  */
                    /*text-align: right;*/
                  }
                }

              }
              .inputSearch{
                margin-left:33/100rem;
                width: 114/100rem;
                height:51/100rem;
                background: #BA0132;
                border-radius: 4/100rem;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: PingFangSC-Semibold;font-weight:600;
                font-size: 14/100rem;
                color: #FFFFFF;
                letter-spacing: 0;
                text-align: center;
                line-height:51/100rem;
              }
            }
          }
          .video{
            margin:0 auto;
          }

        }
      }
      .LeaveBlank1{
        height:93/100rem;
        width:100%;
        background: #f9f9f9;
      }
      .Media{
        width: 100%;
        height:128/100rem;
        background: #f9f9f9;
        .MediaTitle{
          width: 100%;
          height:42/100rem;
          text-align: center;
          font-family: PingFangSC-Medium;font-weight:500;
          font-size: 30/100rem;
          color: #333333;
          letter-spacing: 0;
          line-height:42/100rem;
          /*font-weight: 600;*/
        }
        .mediaMinor{
          width: 100%;
          text-align: center;
          margin-top:17/100rem;
          font-family: PingFangSC-Regular;
          font-size: 14/100rem;
          color: #999999;
          letter-spacing: 0;
          line-height:20/100rem;
        }
      }
      .MediaNew{
        width: 100%;
        height:117/100rem;
        background: #f9f9f9;
        .MediaTitleNew{
          width: 100%;
          height:42/100rem;
          text-align: center;
          font-family: PingFangSC-Medium;font-weight:500;
          font-size: 30/100rem;
          color: #333333;
          letter-spacing: 0;
          line-height:42/100rem;
          /*font-weight: 600;*/
        }
        .mediaMinorNew{
          width: 100%;
          text-align: center;
          margin-top:17/100rem;
          font-family: PingFangSC-Regular;
          font-size: 14/100rem;
          color: #999999;
          letter-spacing: 0;
          line-height:20/100rem;
        }
      }
      .SelectionRange{
        width:100%;
        height:48/100rem;
        display: flex;
        justify-content: center;
        .SelectionRangeBox{
          width: 240/100rem;
          display: flex;
          height:48/100rem;
          div{
            cursor:pointer;
            width: 118/100rem;
            height:46/100rem;
            background: #ffffff;
            border-radius: 0/100rem 4/100rem 4/100rem 0/100rem;
            border:1/100rem solid #BB0737;
            font-family: PingFangSC-Regular;
            font-size: 16/100rem;
            color: #BB0737;
            letter-spacing: 0;
            text-align: center;
            line-height: 46/100rem;
          }
          div:nth-of-type(2){
            border-radius: 0/100rem 4/100rem 4/100rem 0/100rem;
          }
          div:nth-of-type(1){
            border-radius: 4/100rem 0/100rem 0/100rem 4/100rem;
          }
        }

      }
      .mediaType{
        width: 786/100rem;
        margin:0 auto;
        height:105/100rem;
        padding:10/100rem 0 50/100rem 0;
        background: #f9f9f9;
        display: flex;
        justify-content: space-between;
        .mediaTypeItem{
          cursor: pointer;
          height:105/100rem;
          width: 262/100rem;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: space-between;
          div:nth-of-type(1){
            width: 64/100rem;
            height:64/100rem;
            img{
              width: 64/100rem;
              height:64/100rem;
              float: left;
            }
          }
          div:nth-of-type(2){
            height:25/100rem;
            font-family: PingFangSC-Regular;
            font-size: 18/100rem;
            color: #333333;
            letter-spacing: 0;
            text-align: center;
            line-height:25/100rem;
          }
        }
        /*.mediaTypeItem:hover{*/
        /*div:nth-of-type(2){*/
        /*color: #BA0334*/
        /*}*/
        /*}*/
      }
      .mainContent{
        background:#f9f9f9;
        max-width: 1282/100rem;
        margin:0 auto;
        /*background: blue;*/
        .mediaBox{
          max-width: 1282/100rem;
          /*margin:0 auto;*/
          display: flex;
          flex-wrap:wrap;
          justify-content: space-between;
          .hoverItem:hover{
            .shadow{
              /*display: block;*/
              top:-18/100rem;
              background-image: linear-gradient(-180deg, rgba(193,32,74,0.00) 0%, #C1204A 50%);
            }
            .mediaItem{
              top:-18/100rem;
              box-shadow:none;
              .shadowBottom{
                display: none;
              }
              .hoverImg{
                top:68/100rem;
                /*display: block;*/
              }
              .hoverName{
                left:12/100rem;
                /*display: block;*/
                z-index:100;
              }
              .hoverMinor{
                left:12/100rem;
                /*display: block;*/
                z-index:100;
              }
              .hoverDescription{
                top:228/100rem;
                /*display: block;*/
                z-index:100;
              }
            }

            .itemDescription{
              border-bottom:hidden
            }

          }
          .hoverItem{
            /*transform: 1s;*/
            margin-top:25/100rem;
            width: 304/100rem;
            height:309/100rem;
            position: relative;
            .shadow{
              position: absolute;
              top:0;
              left:0;
              width: 304/100rem;
              height:309/100rem;
              /*display: none;*/
              z-index: 10;
              /*background: #fff;*/
              -webkit-transition: 0.2s linear;
            }
            .mediaItem{
              box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
              position: absolute;
              top:0;
              left:0;
              width: 304/100rem;
              height:309/100rem;
              overflow: hidden;
              background: #fff;
              -webkit-transition: 0.2s linear;
              .shadowBottom{
                width: 304/100rem;
                height:31/100rem;
                position: absolute;
                top:140/100rem;
                left:0;
                background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
              }
              .hoverDescription{
                -webkit-transition: 0.2s linear;
                /*display: none;*/
                position: absolute;
                top:1000/100rem;
                left:12/100rem;
                width: 280/100rem;
                font-family: PingFangSC-Regular;
                font-size: 14/100rem;
                color: rgba(255,255,255,0.78);
                letter-spacing: 0;
                text-align: left;
                word-wrap:break-word;

                -webkit-line-clamp:3;
                display: -webkit-box;
                -webkit-box-orient:vertical;
                overflow:hidden;
                text-overflow: ellipsis;
              }
              .hoverImg{
                -webkit-transition: 0.2s linear;
                /*display: none;*/
                z-index:100;
                width: 50/100rem;
                height: 50/100rem;
                position: absolute;
                left:135/100rem;
                top:-50/100rem;
                img{
                  width: 50/100rem;
                  height: 50/100rem;
                  float: left;
                }
              }
              .hoverName{
                -webkit-transition: 0.2s linear;
                position: absolute;
                top:163/100rem;
                /*display: none;*/
                left: -280/100rem;
                height:25/100rem;
                width:280/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18/100rem;
                color: #FFFFFF;
                letter-spacing: 0;
                line-height:25/100rem;
                text-align: left;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap

              }
              .hoverMinor{
                position: absolute;
                top:193/100rem;
                /*display: none;*/
                left: -280/100rem;
                height:17/100rem;
                width:280/100rem;
                font-family: PingFangSC-Regular;
                font-size: 12/100rem;
                color: rgba(255,255,255,0.58);
                letter-spacing: 0;
                text-align: left;
                line-height:17/100rem;
                -webkit-transition: 0.2s linear;
              }
              .itemImg{
                width: 304/100rem;
                height: 171/100rem;
                overflow: hidden;
                img{
                  width: 304/100rem;
                  height: 171/100rem;
                  object-fit: cover;
                  float: left;
                }
              }
              .itemName{
                width: 304/100rem;
                height:25/100rem;
                margin-top:15/100rem;
                text-indent: 17/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18/100rem;
                color: #333333;
                letter-spacing: 0;
                line-height:25/100rem;
                text-align: left;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap
              }
              .itemDescription{
                width: 304/100rem;
                height:17/100rem;
                margin-top:8/100rem;
                font-family: PingFangSC-Regular;
                font-size: 12/100rem;
                color: #999999;
                letter-spacing: 0;
                line-height:17/100rem;
                padding-bottom:13/100rem;
                border-bottom:1/100rem solid #eeeeee;
                text-align: left;
                text-indent: 17/100rem;
              }
              .source{
                width: 304/100rem;
                height:55/100rem;
                /*background: yellow;*/
                display: flex;
                align-items: center;
                .sourceImg{
                  margin-left:13/100rem;
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
          }

        }
        .playlistBox{
          max-width: 1280/100rem;
          margin:0 auto;
          .contentList{
            margin:0 auto;
            max-width: 1282/100rem;
            display: flex;
            flex-wrap:wrap;
            justify-content: space-between;
            background: #f9f9f9;
            .hoverItem:hover{
              .mediaItem{
                box-shadow: 0 0 0.12rem 0 rgba(223,223,223,0.50);
              }
            }
            .hoverItem{
              margin-top:25/100rem;
              width: 304/100rem;
              height:309/100rem;
              position: relative;
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
                top:105/100rem;
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
              .duration{
                position: absolute;
                top:151/100rem;
                right:10/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 12/100rem;
                color: #FFFFFF;
                letter-spacing: 0;
                text-align: left;
                line-height:17/100rem;
                z-index: 101;
                background: transparent;
              }
              /*.shadow{*/
              /*position: absolute;*/
              /*top:0;*/
              /*left:0;*/
              /*width: 304/100rem;*/
              /*height:309/100rem;*/
              /*!*display: none;*!*/
              /*z-index: 10;*/
              /*!*background: #fff;*!*/
              /*-webkit-transition: 0.2s linear;*/
              /*}*/

              .mediaItem{
                /*box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);*/
                position: absolute;
                top:0;
                left:0;
                width: 304/100rem;
                height:309/100rem;
                overflow: hidden;
                background: #ffffff;
                -webkit-transition: 0.2s linear;
                .bottomSharow{
                  z-index: 10;
                  position: absolute;
                  top:140/100rem;
                  left:0;
                  width: 304/100rem;
                  height:31/100rem;
                  background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                }
                .hoverDescription{
                  -webkit-transition: 0.2s linear;
                  /*display: none;*/
                  position: absolute;
                  top:1000/100rem;
                  left:12/100rem;
                  width: 280/100rem;
                  font-family: PingFangSC-Regular;
                  font-size: 14/100rem;
                  color: rgba(255,255,255,0.78);
                  letter-spacing: 0;
                  text-align: left;
                  word-wrap:break-word;

                  -webkit-line-clamp:3;
                  display: -webkit-box;
                  -webkit-box-orient:vertical;
                  overflow:hidden;
                  text-overflow: ellipsis;
                }
                .hoverImg{
                  -webkit-transition: 0.2s linear;
                  /*display: none;*/
                  z-index:100;
                  width: 50/100rem;
                  height: 50/100rem;
                  position: absolute;
                  left:135/100rem;
                  top:-50/100rem;
                  img{
                    width: 50/100rem;
                    height: 50/100rem;
                    float: left;
                  }
                }
                .hoverName{
                  -webkit-transition: 0.2s linear;
                  position: absolute;
                  top:163/100rem;
                  /*display: none;*/
                  left: -280/100rem;
                  height:25/100rem;
                  width:280/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 18/100rem;
                  color: #FFFFFF;
                  letter-spacing: 0;
                  line-height:25/100rem;
                  text-align: left;

                }
                .hoverMinor{
                  position: absolute;
                  top:193/100rem;
                  /*display: none;*/
                  left: -280/100rem;
                  height:17/100rem;
                  width:280/100rem;
                  font-family: PingFangSC-Regular;
                  font-size: 12/100rem;
                  color: rgba(255,255,255,0.58);
                  letter-spacing: 0;
                  text-align: left;
                  line-height:17/100rem;
                  -webkit-transition: 0.2s linear;
                }
                .itemImg{
                  width: 304/100rem;
                  height: 171/100rem;
                  overflow: hidden;
                  img{
                    width: 304/100rem;
                    height: 171/100rem;
                    object-fit: cover;
                    float: left;
                    transition: 0.2s;
                  }
                  img:hover{transform:scale(1.1);}
                }
                .itemName{
                  width: 304/100rem;
                  height:25/100rem;
                  margin-top:15/100rem;
                  text-indent: 17/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 18/100rem;
                  color: #333333;
                  letter-spacing: 0;
                  line-height:25/100rem;
                  text-align: left;
                  overflow:hidden;
                  text-overflow:ellipsis;
                  white-space:nowrap
                }
                .itemDescription{
                  width: 304/100rem;
                  height:17/100rem;
                  margin-top:8/100rem;
                  font-family: PingFangSC-Regular;
                  font-size: 12/100rem;
                  color: #999999;
                  letter-spacing: 0;
                  line-height:17/100rem;
                  padding-bottom:13/100rem;
                  border-bottom:1/100rem solid #eeeeee;
                  text-align: left;
                  text-indent: 17/100rem;
                }
                .source{
                  width: 304/100rem;
                  height:55/100rem;
                  /*background: yellow;*/
                  display: flex;
                  align-items: center;
                  .sourceImg{
                    margin-left:13/100rem;
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
            }

          }
        }
        .sortBox{
          max-width: 1280/100rem;
          margin:0 auto;

          .hoversortItem:hover{
            .shadowsortItem{
              display: block;
              z-index: 10;
              background-image: linear-gradient(-182deg, #BA0132 0%, rgba(186,1,50,0.00) 100%);
            }
            .textSOrtItem{
              top:80/100rem;
              z-index: 20;
            }
            .sortItem{
              img{
                transform: scale(1.3);
              }

            }
          }
          .sortItem:hover{

          }


          .hoversortItem{
            width: 304/100rem;
            height:171/100rem;
            position: relative;
            cursor:pointer;
            margin-top:21/100rem;
            .shadowsortItem{
              position: absolute;
              top:0;
              left:0;
              width: 304/100rem;
              height:171/100rem;
              z-index: 10;
              background: rgba(0,0,0,0.30);
              /*display: none;*/
            }
            .textSOrtItem{
              position: absolute;
              top:92/100rem;
              /*left:124/100rem;*/
              width: 304/100rem;
              height: 48/100rem;
              text-align: center;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 32/100rem;
              color: #FFFFFF;
              letter-spacing: 0;
              line-height:48/100rem;
              transition: 0.2s linear;
              z-index: 20;
            }
            .sortItem{
              overflow: hidden;
              width: 304/100rem;
              height:171/100rem;
              position: relative;
              img{
                width: 304/100rem;
                height:171/100rem;
                float: left;
                transition: 0.2s linear;
                /*background: pink;*/
              }

            }
          }
        }
        .ChannelBox{
          position: relative;
          max-width: 1207/100rem;
          height:800/100rem;
          margin:0 auto;
          background-color: gray;
          background: url("/static/img/f8e31cafad633a0966f3ac4129c2855.png") no-repeat;
          .itemBox:hover{
            z-index:50;
            width: 294/100rem;
            background-image: linear-gradient(228deg, #BA0132 0%, rgba(186,1,50,0.00) 100%);
            .item{
              width: 294/100rem;
              /*position: absolute;*/
              top:12/100rem;
              right:11/100rem;
              box-shadow: 0 0 12/100rem 0 rgba(193,32,74,0.25);
              .topUrl{
                top:1/100rem
              }
              .channelName{
                /*display: none;*/
                bottom:-22/100rem

              }
              .hoverName{
                bottom: 42/100rem;
              }
              .hoverNum{
                bottom:15/100rem
              }
            }
          }
          .itemBox{
            /*width: 294/100rem;*/
            width: 202/100rem;
            height:151/100rem;
            position: absolute;
            transition: 0.2s linear;
            /*background: #000;*/
            .item{
              /*transition: 0.2s linear;*/
              position: absolute;
              top:0;
              right:0;
              width: 202/100rem;
              height:151/100rem;
              background: #ffffff;
              display: flex;
              justify-content: center;
              overflow: hidden;
              transition: 0.2s linear;
              .topUrl{
                position: absolute;
                top:33/100rem;
                width: 162/100rem;
                height:71/100rem;
                overflow: hidden;
                display: flex;
                justify-content: center;
                transition: 0.2s linear;
                img{
                  width: auto;
                  height:71/100rem;
                  float: left;
                }
              }
              .channelName{
                position: absolute;
                bottom:22/100rem;
                /*left:69/100rem;*/
                width:202/100rem ;
                height:22/100rem;
                text-align: center;
                font-family: PingFangSC-Regular;
                font-size: 16/100rem;
                color: #333333;
                letter-spacing: 0;
                line-height:22/100rem;
                /*background: blue;*/
                transition: 0.2s linear;
              }
              .hoverName{
                position: absolute;
                width: 294/100rem;
                height:20/100rem;
                left:0;
                bottom:-20/100rem;
                font-family: PingFangSC-Regular;
                font-size: 14/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: center;
                line-height:20/100rem;
                transition: 0.2s linear;
              }
              .hoverNum{
                position: absolute;
                left:0;
                bottom:-17/100rem;
                width: 294/100rem;
                height:17/100rem;
                font-family: PingFangSC-Regular;
                font-size: 12/100rem;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                line-height:17/100rem;
                transition: 0.2s linear;
              }
            }
          }
          .arrowBox{
            position: absolute;
            bottom: 0;
            width:100%;
            height: 56/100rem;
            display: flex;
            justify-content: center;
            .arrow{
              width: 56/100rem;
              height:56/100rem;
              img{
                width: 56/100rem;
                height: 56/100rem;
                float: left
              }
            }
          }

          .channeldiv:nth-of-type(1){
            left: 0/100rem;
            top:139/100rem;
          }
          .channeldiv:nth-of-type(2){
            left: 295/100rem;
            top:72/100rem;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(3){
            left: 540/100rem;
            top:182/100rem;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(4){
            left: 830/100rem;
            top:67/100rem;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(5){
            left: 151/100rem;
            top:356/100rem;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(6){
            left: 448/100rem;
            top:448/100rem;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(7){
            left: 738/100rem;
            top:404/100rem;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(8){
            left: 979/100rem;
            top:328/100rem;
            /*background: blue;*/
          }
        }


        .Partner{
          max-width: 1182/100rem;
          height:96/100rem;
          padding-bottom:183/100rem;
          margin:0 auto;
          display: flex;
          justify-content: space-around;
          position: relative;
          /*background:pink;*/
          .swiperBox{
            width:100%;
            height:96/100rem;
            /*background: gray;*/
            .swiperSlide{
              width: 223/100rem;
              height:96/100rem;
              display: flex;
              justify-content: center;
              a{
                width: 223/100rem;
                height:96/100rem;
                display: block;
                img{
                  width: 223/100rem;
                  height:96/100rem;
                  float: left;
                }
              }

            }
          }
          .swiper-button-prev{
            background: #f9f9f9;
            width:60/100rem;
            height:96/100rem;
            display: flex;
            justify-content: center;
            align-items: center;
            left: -60/100rem;
            top:21/100rem;
            img{
              width: 42/100rem;
              height:42/100rem;
            }
          }
          .swiper-button-next{
            background: #f9f9f9;
            width:60/100rem;
            height:96/100rem;
            display: flex;
            justify-content: center;
            align-items: center;
            right:-60/100rem;
            top:21/100rem;
            img{
              width: 42/100rem;
              height:42/100rem;
            }
          }
        }
      }

      .openVideoBox{
        width:800/100rem;
        height:600/100rem;
        z-index:200;
      }

      .arrowBox{
        width:100%;
        height: 56/100rem;
        display: flex;
        justify-content: center;
        .arrow{
          width: 56/100rem;
          height:56/100rem;
          img{
            width: 56/100rem;
            height: 56/100rem;
            float: left
          }
        }
      }
      .inputVerticalLine{
        height:34/100rem
      }
      .blank1{
        height:59/100rem
      }
      .blank2{
        height:130/100rem
      }
      .media1{
        height:110/100rem
      }
    }
    @media  (min-width:1301px){
      .topView:before{
        content:"";
        display: table;
      }
      .topView:after{
        content:"";
        display: table;
        clear: both;
      }

      .topViewBox{
        width: 100%;
        overflow: hidden;
        position: relative;
        .topView{
          /*margin: 0 auto;*/
          width: 100%;
          height:800px;
          background: #000000;
          position: relative;
          left: 50%;
          transform: translateX(-50%);
          .bg_video{
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
            /* background: url(polina.jpg) no-repeat; */
            background-size: cover;
            /*position: absolute;*/
            /*left:0;*/
            /*top:0px;*/
            /*z-index:-1;*/
            /*background: gray;*/
          }
          .topViewShadow{
            position: absolute;
            top:0;
            max-width: 2000px;
            height:800px;
            background: #000000;
            opacity: 0.2;
            z-index:10;
          }
          .topviewName{
            position: absolute;
            top:239px;
            width: 100%;
            height:56px;
            font-family: PingFangSC-Semibold;font-weight:600;
            font-size: 48px;
            color: #FFFFFF;
            letter-spacing: 0;
            text-align: center;
            line-height:56px;
            z-index:10;
          }
          .topviewMinor{
            position: absolute;
            top:325px;
            width: 100%;
            height:33px;
            font-family: PingFangSC-Regular;
            font-size: 24px;
            color: #FFFFFF;
            letter-spacing: 1.85px;
            text-align: center;
            line-height:33px;
            z-index:10
          }
          .watchVideoBox{
            position: absolute;
            top:668px;
            width: 100%;
            display: flex;
            justify-content: center;
            .watchVideo{
              width: 100%;
              height:92px;
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: space-between;
              z-index: 10;
              div:nth-of-type(1){
                width: 48px;
                height:48px;
                img{
                  width: 48px;
                  height:48px;
                  float: left;
                }
              }
              div:nth-of-type(2){
                /*width: 80px;*/
                height:28px;
                font-family: PingFangSC-Regular;
                font-size: 20px;
                color: #FFFFFF;
                letter-spacing: 0;
                line-height:28px;
              }
            }
          }

          .topviewCnt{
            position: absolute;
            top:444px;
            width: 100%;
            height:111px;
            display: flex;
            justify-content: center;
            z-index: 10;
            .inputBox{
              width: 1008px;
              height:111px;
              background: rgba(255,255,255,0.40);
              border-radius: 4px;
              display: flex;
              align-items: center;
              position: relative;
              .filterAllOpen{
                position: absolute;
                top:98px;
                left: 693px;
                /*width: 114px;*/
                height: 148px;
                z-index:200;
                background:#ffffff;
                .redline{
                  width: 100%;
                  height:4px;
                  background: #BA0132;
                }
                .filterAllOpenItem{
                  /*width: 114px;*/
                  height:48px;
                  background: #ffffff;
                  font-family: PingFangSC-Regular;
                  font-size: 14px;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  padding:0 18px;
                  line-height:48px;
                  cursor: pointer;

                }
                .filterAllOpenItem:hover{
                  background: #F6F6F6;

                }
              }
              .typeAllOpen{
                width: 320px;
                height:190px;
                position: absolute;
                top:98px;
                left:688px;
                z-index:200;
                background: #ffffff;
                .redline{
                  width: 320px;
                  height:4px;
                  background: #BA0132;
                }
                .typeAllOpenBox{
                  width: 320px;
                  height:186px;
                  display: flex;
                  flex-wrap: wrap;
                  align-content:space-around;
                  .typeAllOpenItem{
                    cursor: pointer;
                    width: 28px;
                    height:20px;
                    font-family: PingFangSC-Regular;
                    font-size: 14px;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:20px;
                    margin-left: 30px;
                  }
                  .typeAllOpenItem:hover{
                    color: #BA0132;
                  }
                }


              }
              .inputContent{
                margin-left:41px;
                width: 762px;
                height:51px;
                background: #ffffff;
                display: flex;
                justify-content: space-between;
                .select{
                  height:51px;
                  width:135px;
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  .sclectItem{
                    width: 118px;
                    height:51px;
                    display: flex;
                    .sclectItemName{
                      height:51px;
                      min-width: 60px;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 16px;
                      color: #666666;
                      letter-spacing: 0;
                      line-height:51px;
                      text-align: right;
                    }
                    .sclectItemImg{
                      margin-left:6px;
                      display: flex;
                      align-items: center;
                      img{
                        width: 11px;
                        height:6px;
                      }

                    }
                  }
                }
                .input{
                  width: 618px;
                  height:51px;
                  line-height:51px;
                  color: #000000;
                  font-size: 14px;
                  input{
                    width: 618px;
                    height:49px;
                    /*background: blue;*/
                    border:none;
                    text-align: left;
                    text-indent: 18px;
                    outline-style: none ;
                    outline-width: 0px ;
                    text-shadow: none ;
                    -webkit-appearance: none ;
                    -webkit-user-select: text ;
                    outline-color: transparent ;
                    box-shadow: none;
                  }
                  input::-webkit-input-placeholder {
                    font-size: 14px;
                    color: #999999;
                    /* placeholder位置  */
                    /*text-align: right;*/
                  }
                }

              }
              .inputSearch{
                margin-left:33px;
                width: 114px;
                height:51px;
                background: #BA0132;
                border-radius: 4px;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: PingFangSC-Semibold;font-weight:600;
                font-size: 14px;
                color: #FFFFFF;
                letter-spacing: 0;
                text-align: center;
                line-height:51px;
              }
            }
          }
          .video{
            margin:0 auto;
          }

        }
      }
      .LeaveBlank1{
        height:93px;
        width:100%;
        background: #f9f9f9;
      }
      .Media{
        width: 100%;
        height:128px;
        background: #f9f9f9;
        .MediaTitle{
          width: 100%;
          height:42px;
          text-align: center;
          font-family: PingFangSC-Medium;font-weight:500;
          font-size: 36px;
          color: #333333;
          letter-spacing: 0;
          line-height:42px;
          /*font-weight: 600;*/
        }
        .mediaMinor{
          width: 100%;
          text-align: center;
          margin-top:17px;
          font-family: PingFangSC-Regular;
          font-size: 14px;
          color: #999999;
          letter-spacing: 0;
          line-height:20px;
        }
      }
      .MediaNew{
        width: 100%;
        height:117px;
        background: #f9f9f9;
        .MediaTitleNew{
          width: 100%;
          height:42px;
          text-align: center;
          font-family: PingFangSC-Medium;font-weight:500;
          font-size: 30px;
          color: #333333;
          letter-spacing: 0;
          line-height:42px;
          /*font-weight: 600;*/
        }
        .mediaMinorNew{
          width: 100%;
          text-align: center;
          margin-top:17px;
          font-family: PingFangSC-Regular;
          font-size: 14px;
          color: #999999;
          letter-spacing: 0;
          line-height:20px;
        }
      }
      .SelectionRange{
        width:100%;
        height:48px;
        display: flex;
        justify-content: center;
        .SelectionRangeBox{
          width: 240px;
          display: flex;
          height:48px;
          div{
            cursor:pointer;
            width: 118px;
            height:46px;
            background: #ffffff;
            border-radius: 0px 4px 4px 0px;
            border:1px solid #BB0737;
            font-family: PingFangSC-Regular;
            font-size: 16px;
            color: #BB0737;
            letter-spacing: 0;
            text-align: center;
            line-height: 46px;
          }
          div:nth-of-type(2){
            border-radius: 0px 4px 4px 0px;
          }
          div:nth-of-type(1){
            border-radius: 4px 0px 0px 4px;
          }
        }

      }
      .mediaType{
        width: 786px;
        margin:0 auto;
        height:105px;
        padding:10px 0 50px 0;
        background: #f9f9f9;
        display: flex;
        justify-content: space-between;
        .mediaTypeItem{
          cursor: pointer;
          height:105px;
          width: 262px;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: space-between;
          div:nth-of-type(1){
            width: 64px;
            height:64px;
            img{
              width: 64px;
              height:64px;
              float: left;
            }
          }
          div:nth-of-type(2){
            height:25px;
            font-family: PingFangSC-Regular;
            font-size: 18px;
            color: #333333;
            letter-spacing: 0;
            text-align: center;
            line-height:25px;
          }
        }
        /*.mediaTypeItem:hover{*/
          /*div:nth-of-type(2){*/
            /*color: #BA0334*/
          /*}*/
        /*}*/
      }
      .mainContent{
        background:#f9f9f9;
        max-width: 1282px;
        margin:0 auto;
        /*background: blue;*/
        .mediaBox{
          max-width: 1282px;
          /*margin:0 auto;*/
          display: flex;
          flex-wrap:wrap;
          justify-content: space-between;
          .hoverItem:hover{
            .shadow{
              /*display: block;*/
              top:-18px;
              background-image: linear-gradient(-180deg, rgba(193,32,74,0.00) 0%, #C1204A 50%);
            }
            .mediaItem{
              top:-18px;
              box-shadow:none;
              .shadowBottom{
                display: none;
              }
              .hoverImg{
                top:68px;
                /*display: block;*/
              }
              .hoverName{
                left:12px;
                /*display: block;*/
                z-index:100;
              }
              .hoverMinor{
                left:12px;
                /*display: block;*/
                z-index:100;
              }
              .hoverDescription{
                top:228px;
                /*display: block;*/
                z-index:100;
              }
            }

            .itemDescription{
              border-bottom:hidden
            }

          }
          .hoverItem{
            /*transform: 1s;*/
            margin-top:25px;
            width: 304px;
            height:309px;
            position: relative;
            .shadow{
              position: absolute;
              top:0;
              left:0;
              width: 304px;
              height:309px;
              /*display: none;*/
              z-index: 10;
              /*background: #fff;*/
              -webkit-transition: 0.2s linear;
            }
            .mediaItem{
              box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
              position: absolute;
              top:0;
              left:0;
              width: 304px;
              height:309px;
              overflow: hidden;
              background: #fff;
              -webkit-transition: 0.2s linear;
              .shadowBottom{
                width: 304px;
                height:31px;
                position: absolute;
                top:140px;
                left:0;
                background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
              }
              .hoverDescription{
                -webkit-transition: 0.2s linear;
                /*display: none;*/
                position: absolute;
                top:1000px;
                left:12px;
                width: 280px;
                font-family: PingFangSC-Regular;
                font-size: 14px;
                color: rgba(255,255,255,0.78);
                letter-spacing: 0;
                text-align: left;
                word-wrap:break-word;

                -webkit-line-clamp:3;
                display: -webkit-box;
                -webkit-box-orient:vertical;
                overflow:hidden;
                text-overflow: ellipsis;
              }
              .hoverImg{
                -webkit-transition: 0.2s linear;
                /*display: none;*/
                z-index:100;
                width: 50px;
                height: 50px;
                position: absolute;
                left:135px;
                top:-50px;
                img{
                  width: 50px;
                  height: 50px;
                  float: left;
                }
              }
              .hoverName{
                -webkit-transition: 0.2s linear;
                position: absolute;
                top:163px;
                /*display: none;*/
                left: -280px;
                height:25px;
                width:280px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18px;
                color: #FFFFFF;
                letter-spacing: 0;
                line-height:25px;
                text-align: left;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap

              }
              .hoverMinor{
                position: absolute;
                top:193px;
                /*display: none;*/
                left: -280px;
                height:17px;
                width:280px;
                font-family: PingFangSC-Regular;
                font-size: 12px;
                color: rgba(255,255,255,0.58);
                letter-spacing: 0;
                text-align: left;
                line-height:17px;
                -webkit-transition: 0.2s linear;
              }
              .itemImg{
                width: 304px;
                height: 171px;
                img{
                  width: 304px;
                  height: 171px;
                  object-fit: cover;
                  float: left;
                }
              }
              .itemName{
                width: 304px;
                height:25px;
                margin-top:15px;
                text-indent: 17px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18px;
                color: #333333;
                letter-spacing: 0;
                line-height:25px;
                text-align: left;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap
              }
              .itemDescription{
                width: 304px;
                height:17px;
                margin-top:8px;
                font-family: PingFangSC-Regular;
                font-size: 12px;
                color: #999999;
                letter-spacing: 0;
                line-height:17px;
                padding-bottom:13px;
                border-bottom:1px solid #eeeeee;
                text-align: left;
                text-indent: 17px;
              }
              .source{
                width: 304px;
                height:55px;
                /*background: yellow;*/
                display: flex;
                align-items: center;
                .sourceImg{
                  margin-left:13px;
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
          }

        }
        .playlistBox{
          max-width: 1280px;
          margin:0 auto;
          .contentList{
            margin:0 auto;
            width: 1280px;
            display: flex;
            flex-wrap:wrap;
            justify-content: space-between;
            background: #f9f9f9;
            .hoverItem:hover{
              .mediaItem{
                box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
              }
            }
            .hoverItem{
              margin-top:25px;
              width: 304px;
              height:309px;
              position: relative;
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
                top:105px;
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
              .duration{
                position: absolute;
                top:151px;
                right:10px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 12px;
                color: #FFFFFF;
                letter-spacing: 0;
                text-align: left;
                line-height:17px;
                z-index: 101;
                background: transparent;
              }
              .mediaItem{
                position: absolute;
                top:0;
                left:0;
                width: 304px;
                height: 309px;
                overflow: hidden;
                background: #ffffff;
                -webkit-transition: 0.2s linear;
                .bottomSharow{
                  z-index: 10;
                  position: absolute;
                  top:140px;
                  left:0;
                  width: 304px;
                  height:31px;
                  background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                }

                .itemImg{
                  width: 304px;
                  height: 171px;
                  overflow: hidden;
                  img{
                    width: 304px;
                    height: 171px;
                    object-fit:cover;
                    float: left;
                    transition: 0.2s;
                  }
                  img:hover{transform:scale(1.1);}
                }
                .itemName{
                  width: 304px;
                  height:25px;
                  margin-top:15px;
                  text-indent: 17px;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 18px;
                  color: #333333;
                  letter-spacing: 0;
                  line-height:25px;
                  text-align: left;
                  overflow:hidden;
                  text-overflow:ellipsis;
                  white-space:nowrap
                }
                .itemDescription{
                  width: 304px;
                  height:17px;
                  margin-top:8px;
                  font-family: PingFangSC-Regular;
                  font-size: 12px;
                  color: #999999;
                  letter-spacing: 0;
                  line-height:17px;
                  padding-bottom:13px;
                  border-bottom:1px solid #eeeeee;
                  text-align: left;
                  text-indent: 17px;
                }
                .source{
                  width: 304px;
                  height:55px;
                  /*background: yellow;*/
                  display: flex;
                  align-items: center;
                  .sourceImg{
                    margin-left:13px;
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
            }

          }


        }
        .sortBox{
          max-width: 1280px;
          margin:0 auto;

          .hoversortItem:hover{
            .shadowsortItem{
              display: block;
              z-index: 10;
              background-image: linear-gradient(-182deg, #BA0132 0%, rgba(186,1,50,0.00) 100%);
            }
            .textSOrtItem{
              top:80px;
              z-index: 20;
            }
            .sortItem{
              img{
                transform: scale(1.3);
              }

            }
          }
          .sortItem:hover{

          }


          .hoversortItem{
            width: 304px;
            height:171px;
            position: relative;
            cursor:pointer;
            margin-top:21px;
            .shadowsortItem{
              position: absolute;
              top:0;
              left:0;
              width: 304px;
              height:171px;
              z-index: 10;
              background: rgba(0,0,0,0.30);
              /*display: none;*/
            }
            .textSOrtItem{
              position: absolute;
              top:92px;
              /*left:124px;*/
              width: 304px;
              height: 48px;
              text-align: center;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 32px;
              color: #FFFFFF;
              letter-spacing: 0;
              line-height:48px;
              transition: 0.2s linear;
              z-index: 20;
            }
            .sortItem{
              overflow: hidden;
              width: 304px;
              height:171px;
              position: relative;
              img{
                width: 304px;
                height:171px;
                float: left;
                transition: 0.2s linear;
                /*background: pink;*/
              }

            }
          }
        }
        .ChannelBox{
          position: relative;
          max-width: 1207px;
          height:800px;
          margin:0 auto;
          background-color: gray;
          background: url("/static/img/f8e31cafad633a0966f3ac4129c2855.png") no-repeat;
          .itemBox:hover{
            z-index:50;
            width: 294px;
            background-image: linear-gradient(228deg, #BA0132 0%, rgba(186,1,50,0.00) 100%);
            .item{
              width: 294px;
              /*position: absolute;*/
              top:12px;
              right:11px;
              box-shadow: 0 0 12px 0 rgba(193,32,74,0.25);
              .topUrl{
                top:1px
              }
              .channelName{
                /*display: none;*/
                bottom:-22px

              }
              .hoverName{
                bottom: 42px;
              }
              .hoverNum{
                bottom:15px
              }
            }
          }
          .itemBox{
            /*width: 294px;*/
            width: 202px;
            height:151px;
            position: absolute;
            transition: 0.2s linear;
            /*background: #000;*/
            .item{
              /*transition: 0.2s linear;*/
              position: absolute;
              top:0;
              right:0;
              width: 202px;
              height:151px;
              background: #ffffff;
              display: flex;
              justify-content: center;
              overflow: hidden;
              transition: 0.2s linear;
              .topUrl{
                position: absolute;
                top:33px;
                width: 162px;
                height:71px;
                overflow: hidden;
                display: flex;
                justify-content: center;
                transition: 0.2s linear;
                img{
                  width: auto;
                  height:71px;
                  float: left;
                }
              }
              .channelName{
                position: absolute;
                bottom:22px;
                /*left:69px;*/
                width:202px ;
                height:22px;
                text-align: center;
                font-family: PingFangSC-Regular;
                font-size: 16px;
                color: #333333;
                letter-spacing: 0;
                line-height:22px;
                /*background: blue;*/
                transition: 0.2s linear;
              }
              .hoverName{
                position: absolute;
                width: 294px;
                height:20px;
                left:0;
                bottom:-20px;
                font-family: PingFangSC-Regular;
                font-size: 14px;
                color: #333333;
                letter-spacing: 0;
                text-align: center;
                line-height:20px;
                transition: 0.2s linear;
              }
              .hoverNum{
                position: absolute;
                left:0;
                bottom:-17px;
                width: 294px;
                height:17px;
                font-family: PingFangSC-Regular;
                font-size: 12px;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                line-height:17px;
                transition: 0.2s linear;
              }
            }
          }
          .arrowBox{
            position: absolute;
            bottom: 0;
            width:100%;
            height: 56px;
            display: flex;
            justify-content: center;
            .arrow{
              width: 56px;
              height:56px;
              img{
                width: 56px;
                height: 56px;
                float: left
              }
            }
          }

          .channeldiv:nth-of-type(1){
            left: 0px;
            top:139px;
          }
          .channeldiv:nth-of-type(2){
            left: 295px;
            top:72px;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(3){
             left: 540px;
             top:182px;
             /*background: blue;*/
           }
          .channeldiv:nth-of-type(4){
            left: 830px;
            top:67px;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(5){
             left: 151px;
             top:356px;
             /*background: blue;*/
           }
          .channeldiv:nth-of-type(6){
            left: 448px;
            top:448px;
            /*background: blue;*/
          }
          .channeldiv:nth-of-type(7){
             left: 738px;
             top:404px;
             /*background: blue;*/
           }
          .channeldiv:nth-of-type(8){
            left: 979px;
            top:328px;
            /*background: blue;*/
          }
        }


        .Partner{
          max-width: 1182px;
          height:96px;
          padding-bottom:183px;
          margin:0 auto;
          display: flex;
          justify-content: space-around;
          position: relative;
          /*background:pink;*/
          .swiperBox{
            width:100%;
            height:96px;
            /*background: gray;*/
            .swiperSlide{
              width: 223px;
              height:96px;
              display: flex;
              justify-content: center;
              a{
                width: 223px;
                height:96px;
                display: block;
                img{
                  width: 223px;
                  height:96px;
                  float: left;
                }
              }
            }
          }
          .swiper-button-prev{
              background: #f9f9f9;
              width:60px;
              height:96px;
              display: flex;
              justify-content: center;
              align-items: center;
              left: -60px;
              top:21px;
              img{
                width: 42px;
                height:42px;
              }
            }
          .swiper-button-next{
            background: #f9f9f9;
            width:60px;
            height:96px;
            display: flex;
            justify-content: center;
            align-items: center;
            right:-60px;
            top:21px;
            img{
              width: 42px;
              height:42px;
            }
          }
        }
      }

      .openVideoBox{
        width:800px;
        height:600px;
        z-index:200;
      }

      .arrowBox{
        width:100%;
        height: 56px;
        display: flex;
        justify-content: center;
        .arrow{
          width: 56px;
          height:56px;
          img{
            width: 56px;
            height: 56px;
            float: left
          }
        }
      }
      .inputVerticalLine{
        height:34px
      }
      .blank1{
        height:59px
      }
      .blank2{
        height:130px
      }
      .media1{
        height:110px
      }
    }
  }
</style>
