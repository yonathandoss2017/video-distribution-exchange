<template>
  <div class="main">
    <loadingPage :showLoading="showLoading"></loadingPage>
    <playlistDialog :isPlsylistDialogShow="isPlsylistDialogShow" @copyButtom = 'copyButtom'
                    :playlistDetails="playlistDetails" @closeDialog = 'closeDialog'></playlistDialog>
    <universalTop :indexSign="indexSign" ></universalTop>
    <div class="blank1" style=""></div>
    <div class="container">
      <div class="navigation">
        <div class="navigation_con">
          <span @click="toIndex" style="cursor:pointer">{{$t('listDetails.index')}}</span> > <span style="cursor:pointer" @click="toClassification(0,0)">{{$t('listDetails.content_classification')}}</span> > <span style="color: #BA0132;cursor:pointer">{{$t('listDetails.content_list')}}</span>
        </div>
      </div>
      <div class="loadingDiv" style="min-height: 800px" v-if="isLoadingDiv">

      </div>
      <div v-if="!isLoadingDiv" class="con_detail">
        <div class="con_detail_div" v-if="ListIntroduction!=''">
          <div class="con_detail_left">
            <div class="detail_left_row1"><img :src="ListIntroduction.thumbnail_url" alt=""></div>
            <div class="detail_left_row2">
              <div>
                <span>{{$t('listDetails.effective_time')}}</span>
                <span>{{$t('listDetails.Last_update_time')}}</span>
                <span>{{$t('listDetails.Total_video')}}</span>
                <span>{{$t('listDetails.classification')}}</span>
                <span>{{$t('listDetails.Original_country')}}</span>
                <span>{{$t('listDetails.Language')}}</span>
                <span>{{$t('listDetails.other')}}</span>
              </div>
              <div>
                <span>{{ListIntroduction.start_date == null?$t('listDetails.no'):ListIntroduction.start_date }}</span>
                <span>{{ ListIntroduction.updated_at}}</span>
                <span>{{ListIntroduction.entries_count}}</span>
                <span>{{ListIntroduction.genre}}</span>
                <span>{{ListIntroduction.region}}</span>
                <span>{{ListIntroduction.language}}</span>
                <span>{{ListIntroduction.other == null?$t('listDetails.no'):ListIntroduction.other}}</span>
              </div>
            </div>
            <div class="detail_left_row3">
              <div class="share">{{$t('listDetails.share_to')}}</div>
              <div class="shareTo">
                <div @click="shareToSina"><img src="/static/icon/sina.png" alt=""></div>
                <!--<div ><a href="mailto:17864212247@sina.cn?subject=Hello&body=你好"><img src="/static/icon/email.png" alt=""></a></div>-->
                <!--<div @click="shareToEmail"><a id="emailA" href=""><img src="/static/icon/email.png" alt=""></a></div>-->
                <div @click="shareToEmail"><img src="/static/icon/email.png" alt=""></div>
                <div @click="shareToQQ"><img src="/static/icon/qq.png" alt=""></div>
              </div>
            </div>
          </div>

          <div class="con_detail_right">
            <div class="detail_right_title">{{ListIntroduction.name?ListIntroduction.name:''}}</div>
            <div class="detail_right_request">
              <div>
                <span @click="toChannelDetails(ListIntroduction.property.id)" style="cursor: pointer" v-if="ListIntroduction.property">{{ListIntroduction.property.name}}</span>
                <!--<span>{{ListIntroduction.region}}</span>-->
              </div>
              <div v-if="ListIntroduction.in_cart == 0" style="cursor: pointer" @click="addCard(ListIntroduction.id)">{{$t('listDetails.Request_content')}}</div>
              <div v-else style="cursor: not-allowed" >{{$t('listDetails.Already_added_cart')}}</div>
            </div>
            <div class="terms">{{$t('listDetails.Distribution_clause')}}</div>
            <div class="detail_right_subtitle">{{$t('listDetails.Regional_rights')}}</div>
            <div class="subtitle_con">
              <div class="subtitle_item">
                <span>{{$t('listDetails.Allowed_area')}}</span>
                <span v-if="ListIntroduction.term">{{ListIntroduction.term.region_allowed}}</span>
              </div>
              <div class="subtitle_item">
                <span>{{$t('listDetails.exclude')}}</span>
                <span v-if="ListIntroduction.term">{{ListIntroduction.term.region_excepted}}</span>
              </div>
            </div>
            <!--分发模式-->
            <div class="detail_right_subtitle">{{$t('listDetails.DistributionMode')}}</div>
            <div class="subtitle_con">
               <div class="subtitle_item">
                 <span>{{$t('listDetails.APIDistribution')}}</span>
                 <span v-if="ListIntroduction.term">{{ListIntroduction.term.api_share_to}}</span>
               </div>
               <div class="subtitle_item" v-if="PaymentModeIndex!=0">
                 <span>{{$t('listDetails.VideoDownload')}}</span>
                 <span v-if="ListIntroduction.term">{{ListIntroduction.term.download_resolution}}</span>
               </div>
             </div>
            <div class="detail_right_subtitle">{{$t('listDetails.PaymentMode')}}</div>
            <div class="subtitle_con">
              <div class="subtitle_item">
                <span>{{$t('listDetails.paymentMethod')}}</span>
                <span v-if="ListIntroduction.term">{{ListIntroduction.term.payment_mode_in_lang}}</span>
              </div>
              <div class="subtitle_item" v-if="PaymentModeIndex!=4">
                <span>{{$t('listDetails.exclusive')}}</span>
                <span v-if="ListIntroduction.term">{{$t(ListIntroduction.term.exclusivity)}}</span>
              </div>
              <div class="subtitle_item" v-if="PaymentModeIndex==2||PaymentModeIndex==3">
                <span>{{$t('listDetails.NumberOfVideoUpdates')}}</span>
                <span v-if="ListIntroduction.term&&PaymentModeIndex==2">{{$t('listDetails.NotLessThanEveryYear')}}{{ListIntroduction.term.update_count}}{{$t('listDetails.one')}}</span>
                <span v-if="ListIntroduction.term&&PaymentModeIndex==3">{{$t('listDetails.NotLessThanMonthly')}}{{ListIntroduction.term.update_count}}{{$t('listDetails.one')}}</span>
              </div>
              <div class="subtitle_item" v-if="PaymentModeIndex!=0&&PaymentModeIndex!=4">
                <span>{{$t('listDetails.TotalPrice')}}</span>
                <span v-if="ListIntroduction.term&&PaymentModeIndex==2">{{$t('listDetails.PerYear')}}  {{ListIntroduction.term.price}}￥</span>
                <span v-if="ListIntroduction.term&&PaymentModeIndex==3">{{$t('listDetails.perMonth')}}  {{ListIntroduction.term.price}}￥</span>
                <span v-if="ListIntroduction.term&&PaymentModeIndex==1">{{ListIntroduction.term.price}}￥</span>
              </div>
              <div class="subtitle_item" v-if="PaymentModeIndex==0">
                <span>{{$t('listDetails.Proportion')}}</span>
                <span v-if="ListIntroduction.term">{{$t('listDetails.ContentSide')}}{{ListIntroduction.term.revenue_share_cp}}%，{{$t('listDetails.ServiceParty')}}{{ListIntroduction.term.revenue_share_sp}}%</span>
              </div>
              <div class="subtitle_item" v-if="ListIntroduction.term&&ListIntroduction.term.payment_comments!=''">
                <span>{{$t('listDetails.Comment')}}</span>
                <span style="word-wrap: break-word;word-break:break-all;">
                  {{expandMessage}}
                  <span class="expand" v-if="isExpand&&isExpandShow" @click="expandClick">{{$t('videoDetails.expand')}}</span>
                  <span class="expand" v-else-if="!isExpand&&isExpandShow" @click="expandClick">{{$t('videoDetails.collapse')}}</span>
                </span>
              </div>
            </div>

          </div>
        </div>
        <div class="List_video">
          <div class="List_video_title">{{$t('listDetails.Content_list_video')}}</div>
          <div class="List_video_con">
            <div class="List_video_item" v-if="viewList!=''" v-for="(item,index) in viewList" :key="index" :style="index>3?'cursor: pointer;margin-top:25px':'cursor: pointer'" @click="tovideoDetail(item.id)">
              <div><img :src="item.logo" alt=""></div>
              <div><span>{{item.newduration}}</span></div>
              <div>{{item.name?item.name:''}}</div>
            </div>
            <div class="List_video_item" style="box-shadow:none" v-for="(item,index) in (4-viewList.length%4)" v-if="viewList.length%4>0"></div>
          </div>
        </div>
        <div class="pager" v-if="all_count>8">
          <Pagenation :allCount='all_count' :allPage='allPage' :countPagea="countPage" :meicount="page_count" :jumpcounta="jumpCount" @nextPage="nextPage"></Pagenation>
        </div>
      </div>
      <div v-if="!isLoadingDiv" class="blank2" style="width: 100%;background: #f9f9f9;"></div>
      <div v-if="!isLoadingDiv" class="recommend">
        <div class="recommend_title">
          <div @click="popularClick(1)" style="cursor: pointer">
            {{$t('listDetails.Same_style_recommendation')}}
            <span :style="isPopular?'background: #ffffff':'background: #BA0132'"></span>
          </div>
          <div @click="popularClick(2)" style="cursor: pointer">
            {{$t('listDetails.Popular_recommendation')}}
            <span :style="isPopular?'background: #BA0132':'background: #ffffff'"></span>
          </div>
        </div>
        <div class="recommend_list">
          <div v-if="isPopular&&popularRecommendList!=''" class="recommend_list_item" v-for="(item,index) in popularRecommendList" :key="index" style="cursor: pointer" @click="toListDetail(item.ivx_id)">
            <div class="imgUrl"><img :src="item.cover_image[0]" alt=""></div>
            <div  class="content_list">{{$t('AllContentCategories.content_list')}}</div>
            <div  class="list_number">
              <img src="/static/icon/Group4.png" alt="">
              <span>{{item.videos_count}}{{$t('listDetails.one')}}</span>
            </div>
            <div class="recommnd_name">{{item.title[0]}}</div>
          </div>
          <div class="recommend_list_item" style="box-shadow:none" v-for="(item,index) in (4-popularRecommendList.length%4)" v-if="popularRecommendList.length%4>0&&isPopular"></div>
          <div v-if="!isPopular&&SameStyleRecommendation!=''" class="recommend_list_item" v-for="(item,index) in SameStyleRecommendation" :key="index" style="cursor: pointer" @click="toListDetail(item.ivx_id)">
            <div class="imgUrl"><img :src="item.cover_image[0]" alt=""></div>
            <div  class="content_list">{{$t('AllContentCategories.content_list')}}</div>
            <div  class="list_number">
              <img src="/static/icon/Group4.png" alt="">
              <span>{{item.videos_count}}{{$t('listDetails.one')}}</span>
            </div>
            <div class="recommnd_name">{{item.title[0]}}</div>
          </div>
          <div class="recommend_list_item" style="box-shadow:none" v-for="(item,index) in (4-SameStyleRecommendation.length%4)" v-if="SameStyleRecommendation.length%4>0&&!isPopular"></div>
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
  import playlistDialog from '../components/emailShareDialog/playlistDialog.vue'
  import {Car, getPlayList, getContentListDetails,getplaylist,Entry,CarAdd } from '@/utils/global/axios.js'
  export default {
    components:{
      universalTop,
      universalBottom,
      Pagenation,
      loadingPage,
      playlistDialog,
    },
    watch:{
      lanage(val){
        if(this.lanage !=''){
          let that = this
          this.showLoading = true
          setTimeout(function () {
            that.changeLanageGetDetails()
          },500)
        }
      },
    },
    computed:{
      lanage(){
        return this.$store.state.lanage
      },
      expandMessage: function () {
        if(this.ListIntroduction.term.payment_comments){
          if(!this.isExpand){
            return this.ListIntroduction.term.payment_comments
          }else{
            if(this.ListIntroduction.term.payment_comments!=''){
              if(this.ListIntroduction.term.payment_comments.length >45){
                let arr = new Array()
                let newintroduction = this.ListIntroduction.term.payment_comments.substring(0,45)
                let point = '...   '
                arr.push(newintroduction)
                arr.push(point)
                return arr.join('')
              }else{
                return this.ListIntroduction.term.payment_comments
              }
            }
          }
        }else{
          return ''
        }

      },
      shareEmailDetail(){
        return{
          content_list:this.$t('listDetails.content_list'),     //内容列表
          source:this.$t('listDetails.source'),                 //来源
          effective_time:this.$t('listDetails.effective_time'),     //有效时间
          Last_update_time:this.$t('listDetails.Last_update_time'), //最后更新时间
          Total_video:this.$t('listDetails.Total_video'),           //总计视频
          classification:this.$t('listDetails.classification'),     //分类
          Original_country:this.$t('listDetails.Original_country'), //内容原国家
          listDetails:this.$t('listDetails.Language'),              //语言
          other:this.$t('listDetails.other'),                       //内容其他信息
          //区域权力
          Regional_rights:this.$t('listDetails.Regional_rights'),   //区域权力
          Allowed_area:this.$t('listDetails.Allowed_area'),         //允许区域
          exclude:this.$t('listDetails.exclude'),                   //排除
          //付费模式',
          PaymentMode:this.$t('listDetails.PaymentMode'),           //付费模式
          paymentMethod:this.$t('listDetails.paymentMethod'),       //支付方式
          exclusive:this.$t('listDetails.exclusive'),               //独家
          NumberOfVideoUpdates:this.$t('listDetails.NumberOfVideoUpdates'), //视频更新数量
          TotalPrice:this.$t('listDetails.TotalPrice'),                       //总价
          Proportion:this.$t('listDetails.Proportion'),                       //分成比例
          Comment:this.$t('listDetails.Comment'),                       //注释
          link:this.$t('shareToEmail.link'),

          content_list_value:this.ListIntroduction.name?this.ListIntroduction.name:'',     //内容列表
          source_value:this.ListIntroduction.property?this.ListIntroduction.property.name:'',
          effective_time_value:this.ListIntroduction.start_date == null?this.$t('listDetails.no'):this.ListIntroduction.start_date,
          Last_update_time_value:this.ListIntroduction.updated_at, //最后更新时间
          Total_video_value:this.ListIntroduction.entries_count,           //总计视频
          classification_value:this.ListIntroduction.genre,     //分类
          Original_country_value:this.ListIntroduction.region, //内容原国家
          listDetails_value:this.ListIntroduction.language,              //语言
          other_value:this.ListIntroduction.other == null?this.$t('listDetails.no'):this.ListIntroduction.other,//内容其他信息
          //区域权力
          Allowed_area_value:this.ListIntroduction.term&&this.ListIntroduction.term.region_allowed?this.ListIntroduction.term.region_allowed:'',         //允许区域
          exclude_value:this.ListIntroduction.term&&this.ListIntroduction.term.region_excepted?this.ListIntroduction.term.region_excepted:'',                   //排除
          //付费模式',
          paymentMethod_value:this.ListIntroduction.term&&this.ListIntroduction.term.payment_mode_in_lang?this.ListIntroduction.term.payment_mode_in_lang:'',       //支付方式
          exclusive_value:this.ListIntroduction.term&&this.ListIntroduction.term.exclusivity?this.ListIntroduction.term.exclusivity:'',               //独家
          NumberOfVideoUpdates_value:this.ListIntroduction.term&&this.ListIntroduction.term.update_count?this.ListIntroduction.term.update_count +this.$t('listDetails.one'):'', //视频更新数量
          TotalPrice_value:this.ListIntroduction.term&&this.ListIntroduction.term.price?this.ListIntroduction.term.price+'￥':'',                       //总价
          Proportion_value:this.ListIntroduction.term&&this.ListIntroduction.term.revenue_share_cp?`${this.$t('listDetails.ContentSide')+this.ListIntroduction.term.revenue_share_cp}%，${this.$t('listDetails.ServiceParty')+this.ListIntroduction.term.revenue_share_sp}%`:'',                       //分成比例
          Comment_value:this.ListIntroduction.term&&this.ListIntroduction.term.payment_comments?this.ListIntroduction.term.payment_comments:'',
        }
      },
    },
    data(){
      return{
        isPlsylistDialogShow:false,
        playlistDetails:'',
        isExpandShow:false,
        isExpand:true,
        indexSign:false,
        similarGenre:'',
        all_count:0,//总条数
        allPage:0,//总页数
        countPage:1,//当前页
        page_count:8,//每页多少条
        jumpCount:1,//跳转页
        viewList:[],
        popularRecommendList:[],
        SameStyleRecommendation:[],
        ListIntroduction:{},
        playlistId:null,
        isPopular:false,
        showLoading:true,
        isLoadingDiv:true,
        PaymentMode:[
          {
            en:'revenue-share',
          },{
            en:'charge-download',
          },{
            en:'annual-download',
          },{
            en:'monthly-download',
          },{
            en:'free-download',
          },
        ],
        PaymentModeIndex:1,

      }
    },
    created(){
      let queryPlaylistId = this.$route.query.playlistId
      this.playlistId = queryPlaylistId.toString().split('?')[0]
      this.getDetails()
      this.getViewlist()
    },
    methods:{
      shareToSina(){
        let ftit = `${this.$t('listDetails.sharePlaylist')}："${this.ListIntroduction.name}"`;
        let flink = '';
        window.open('http://service.weibo.com/share/share.php?url=' + encodeURIComponent(document.location) + '?sharesource=weibo&title=' + ftit + '&pic=' + flink + '&appkey=4030046416');
      },
      shareToEmail(){
        let baseurl = window.location.href;
        this.isPlsylistDialogShow = true
        let subject=`${this.$t('listDetails.sharePlaylist')}："${this.ListIntroduction.name}"`
        parent.location.href = 'mailto:?subject='+subject+'&body=';
      },
      shareToQQ(){
        let ftit = `${this.$t('listDetails.sharePlaylist')}："${this.ListIntroduction.name}"`;
        let flink = '';
        window.open('https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + encodeURIComponent(document.location) + '?sharesource=qzone&title=' + ftit + '&pics=' + flink + '&summary=' + '');
      },
      closeDialog(){
        this.isPlsylistDialogShow = false
      },
      copyButtom(str){
        console.log(str)
        let newStr = JSON.stringify(str)
        console.log(newStr)
        let clipboard = new Clipboard('.btn',{
        });

        clipboard.on('success', function(e) {

        });
      },

      //加入购物车
      addCard(id){
        this.showLoading = true
        let params = {
          playlist_id:id
        }
        CarAdd(params).then(res=>{
          if(res.data.status == 'success'){
            this.ListIntroduction.in_cart = 1
            let params = ''
            return  Car(params)
          }
          this.showLoading = false
        })
        .then(res=>{
          if(res.data.status == 'success'){
            let number = res.data.data.length
            this.$store.commit('newPurchaseQuantity',number)
          }
          this.showLoading = false
        })
        .catch(err=>{
          this.showLoading = false
        })
      },
      expandClick(){
        this.isExpand = !this.isExpand
      },
      changeLanageGetDetails(){
        let params ='/'+ this.playlistId
        getplaylist(params).then(res=>{
          this.ListIntroduction = res.data.data
          this.similarGenre = res.data.data.genre
          if(this.ListIntroduction.term){
            this.PaymentMode.forEach((item,index)=>{
              if(item.en==this.ListIntroduction.term.payment_mode){
                this.PaymentModeIndex = index
              }
            })
            if(this.ListIntroduction.term.payment_comments !='' && this.ListIntroduction.term.payment_comments.length>45){
              this.isExpandShow = true
            }else{
              this.isExpandShow = false
            }
            this.ListIntroduction.term.exclusivity = this.isexclusive(this.ListIntroduction.term.exclusivity)
            this.ListIntroduction.term.region_allowed = this.changeSeparate(this.ListIntroduction.term.region_allowed)
            this.ListIntroduction.term.region_excepted = this.changeSeparate(this.ListIntroduction.term.region_excepted)
            this.ListIntroduction.term.api_share_to = this.changeSeparate(this.ListIntroduction.term.api_share_to)
            if(this.ListIntroduction.term.download_resolution!=null){
              this.ListIntroduction.term.download_resolution = this.changeSeparate(this.ListIntroduction.term.download_resolution)
            }
          }
          this.ListIntroduction.updated_at = this.getDate(this.ListIntroduction.updated_at)
          this.showLoading = false
          this.isLoadingDiv = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      getDetails(){
        let params ='/'+ this.playlistId
        getplaylist(params).then(res=>{
          this.ListIntroduction = res.data.data
          this.similarGenre = res.data.data.genre
          if(this.ListIntroduction.term){
            this.PaymentMode.forEach((item,index)=>{
              if(item.en==this.ListIntroduction.term.payment_mode){
                this.PaymentModeIndex = index
              }
            })
            if(this.ListIntroduction.term.payment_comments !='' && this.ListIntroduction.term.payment_comments.length>45){
              this.isExpandShow = true
            }else{
              this.isExpandShow = false
            }
            this.ListIntroduction.term.exclusivity = this.isexclusive(this.ListIntroduction.term.exclusivity)
            this.ListIntroduction.term.region_allowed = this.changeSeparate(this.ListIntroduction.term.region_allowed)
            this.ListIntroduction.term.region_excepted = this.changeSeparate(this.ListIntroduction.term.region_excepted)
            this.ListIntroduction.term.api_share_to = this.changeSeparate(this.ListIntroduction.term.api_share_to)
            if(this.ListIntroduction.term.download_resolution!=null){
              this.ListIntroduction.term.download_resolution = this.changeSeparate(this.ListIntroduction.term.download_resolution)
            }
          }
          this.ListIntroduction.updated_at = this.getDate(this.ListIntroduction.updated_at)
          this.playlistDetails = this.ListIntroduction
          this.playlistDetails.PaymentModeIndex = this.PaymentModeIndex
          this.getSameStyle()
          this.showLoading = false
          this.isLoadingDiv = false
        }).catch(err=>{
          this.showLoading = false

        })
      },
      changeSeparate(val){
        if(val){
          return val.split(', ').join('  、')
        }
      },
      isexclusive(val){
        let that = this
        if(val == 'exclusive'){
          return 'listDetails.yes_Exclusive'
        }else{
          return 'listDetails.no_Exclusive'
        }

      },
      getViewlist(){
        let that = this
        let params = '?limit=8&start=0&playlistId='+this.playlistId
        Entry(params).then(res=>{
          if(res.data.status == 'success'){
            this.all_count = res.data.total_count
            this.allPage = Math.ceil(res.data.total_count/8)
            this.viewList = res.data.data
            this.viewList.forEach((item,index)=>{
              item.newduration = this.formatSeconds(item.duration)
            })
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })

      },
      nextPage(val){
        this.showLoading = true
        let params = '?playlistId='+this.playlistId+'&limit=8&start='+(val-1)*8
        Entry(params).then(res=>{
          if(res.data.status == 'success'){
            this.viewList = res.data.data
            this.viewList.forEach((item,index)=>{
              item.newduration = this.formatSeconds(item.duration)
            })
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },

      getSameStyle(){
        let params = '?limit=4&start=0&filter=playlist&genre='+this.similarGenre
        getPlayList(params).then(res=>{
          if(res.data){
            this.SameStyleRecommendation = res.data.response.docs
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      popularClick(index){
        if(index == 1){
          if(this.isPopular == false){
            return
          }else{
            this.isPopular = false
          }
        }else if(index == 2){
          if(this.isPopular == true){
            return
          }else{
            this.isPopular = true
            if(this.popularRecommendList == ''){
              let params = '?limit=4&start=0&mostpopular=1&filter=playlist'
              getPlayList(params).then(res=>{
                if(res.data){
                  this.popularRecommendList = res.data.response.docs
                }
                this.showLoading = false
              }).catch(err=>{
                this.showLoading = false
              })
            }
          }
        }
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
      getLocalTime(nS) {
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
      },

      //时间戳转化为时间
      getDate(time){
        if(time == null || time == ''){
          return null
        }else{
          var timestamp4 = new Date(time*1000)
          var newTime = (timestamp4.toLocaleDateString().replace(/\//g, "-") + " " + timestamp4.toTimeString().substr(0, 8))
          return newTime
        }
      },
      toIndex(){
        this.$router.push({name: 'index'})
      },
      toClassification(){
        this.$router.push({name: 'allContentCategories',query:{typeId:0,typeStyle:0}})
      },
      tovideoDetail(id){
        this.$router.push({name: 'videoDetails',query:{videoId:id}})
      },
      toListDetail(ivx_id){
        this.$router.push({name: 'ContentListDetails',query:{playlistId:ivx_id}})
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
        width:100%;
        position: relative;
        .container{
          max-width: 1280/100rem;
          height:auto;
          margin:0 auto;
          background: #ffffff;
          .navigation{
            padding-top:50/100rem;
            height:60/100rem;
            width: 100%;
            background: #f9f9f9;
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
          .con_detail{
            padding-top:50/100rem;
            width: 100%;
            height:auto;
            background: #FFFFFF;
            border: 1/100rem solid #EEEEEE;
            padding-bottom:47/100rem;
            .con_detail_div{
              width: 100%;
              display: flex;
              .con_detail_left{
                width: 478/100rem;
                margin-left:50/100rem;
                .detail_left_row1{
                  width: 478/100rem;
                  height:269/100rem;
                  overflow: hidden;
                  img{
                    width: 478/100rem;
                    height:269/100rem;
                    object-fit:cover;
                    float: left;
                  }
                }
                .detail_left_row2{
                  display: flex;
                  margin-top:40/100rem;
                  div:nth-of-type(1){
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14/100rem;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40/100rem;
                    span{
                      display: block;
                    }
                  }
                  div:nth-of-type(2){
                    margin-left:50/100rem;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14/100rem;
                    color: #2A2A2A;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40/100rem;
                    span{
                      display: block;
                    }
                  }
                }
                .detail_left_row3{
                  margin-top:22/100rem;
                  height:20/100rem;
                  display: flex;
                  .share{
                    font-family: PingFangSC-Regular;
                    font-size: 14/100rem;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:20/100rem;
                  }
                  .shareTo{
                    margin-left:38/100rem;
                    width: 134/100rem;
                    height:20/100rem;
                    display: flex;
                    justify-content: space-between;
                    div{
                      width: 20/100rem;
                      height:20/100rem;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      cursor: pointer;
                      img{
                        width: auto;
                        max-height:20/100rem;
                        float: left;
                      }
                    }
                  }
                }

              }
              .con_detail_right{
                width: 667/100rem;
                margin-left:32/100rem;
                .detail_right_title{
                  font-family: PingFangSC-Semibold;font-weight:600;
                  font-size: 30/100rem;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:42/100rem;
                }
                .detail_right_request{
                  height:30/100rem;
                  margin-top:9/100rem;
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  div:nth-of-type(1){
                    display: flex;
                    align-items: center;
                    span:nth-of-type(1){
                      height:30/100rem;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 14/100rem;
                      color: #666666;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:30/100rem;
                    }
                    span:nth-of-type(2){
                      margin-left:6/100rem;
                      background: #BA0132;
                      border-radius: 2/100rem;
                      width:34/100rem;
                      height:20/100rem;
                      font-family: PingFangSC-Semibold;font-weight:600;
                      font-size: 14/100rem;
                      color: #FFFFFF;
                      letter-spacing: 0;
                      text-align: center;
                      line-height:20/100rem;
                    }
                  }
                  div:nth-of-type(2){
                    padding:0 16/100rem;
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
                  div:nth-of-type(2):hover{
                    color: #FFFFFF;
                    background: #BA0132;
                    border:none;
                  }
                }
                .terms{
                  margin-top:25/100rem;
                  height:28/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 20/100rem;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:28/100rem;
                }
                .detail_right_subtitle{
                  margin-top:15/100rem;
                  height: 22/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 16/100rem;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:22/100rem;
                }
                .subtitle_con{
                  margin-top:15/100rem;
                  margin-bottom:30/100rem;
                  width: 667/100rem;
                  padding:20/100rem 0 0 0;
                  border: 1/100rem solid #DDDDDD;
                  .subtitle_item{
                    width: 667/100rem;
                    min-height:20/100rem;
                    margin-bottom:17/100rem;
                    display: flex;
                    span:nth-of-type(1){
                      width: 287/100rem;
                      text-indent: 18/100rem;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 14/100rem;
                      color: #999999;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:20/100rem;
                    }
                    span:nth-of-type(2){
                      flex: 1;
                      padding-right: 15/100rem;
                      font-family: PingFangSC-Regular;
                      font-size: 14/100rem;
                      color: #666666;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:20/100rem;
                      .expand{
                        cursor: pointer;
                        font-family: PingFangSC-Medium;
                        font-weight: 500;
                        font-size: 14/100rem;
                        color: #333333;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:20/100rem;
                      }
                    }
                  }
                }
              }
            }
            .List_video{
              margin-top:55/100rem;
              max-width:1178/100rem ;
              padding:0 52/100rem 0 50/100rem;
              .List_video_title{
                height:30/100rem;
                font-family: PingFangSC-Semibold;font-weight:600;
                font-size: 22/100rem;
                color: #666666;
                letter-spacing: 0;
                text-align: left;
                line-height:30/100rem;
              }
              .List_video_con{
                margin-top:31/100rem;
                max-width: 1178/100rem;
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
                .List_video_item{
                  background: #FFFFFF;
                  box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
                  width: 272/100rem;
                  height:215/100rem;
                  position: relative;
                  div:nth-of-type(1){
                    width: 272/100rem;
                    height:153/100rem;
                    overflow: hidden;
                    img{
                      width: 272/100rem;
                      height:153/100rem;
                      object-fit:cover;
                      float: left;
                      transition: 0.2s;
                    }
                    img:hover{transform:scale(1.1);}
                  }
                  div:nth-of-type(2){
                    position: absolute;
                    top:131/100rem;
                    right:0/100rem;
                    height:22/100rem;
                    width: 272/100rem;
                    background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                    font-size: 12/100rem;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    text-align: right;
                    line-height:22/100rem;
                    span{

                      height:22/100rem;
                      width: auto;
                      position: absolute;
                      top:0;
                      right:4/100rem;
                      font-size: 12/100rem;
                      color: #FFFFFF;
                      letter-spacing: 0;
                      text-align: right;
                      line-height:22/100rem;

                    }
                  }
                  div:nth-of-type(3){
                    margin-top:16/100rem;
                    height:34/100rem;
                    width: 272/100rem;
                    text-indent: 15/100rem;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18/100rem;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25/100rem;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap
                  }
                }
              }
            }
            .pager{
              margin-top:77/100rem;
              height:28/100rem;
              width: 100%;
            }
          }
          .recommend{
            width: 100%;
            height:388/100rem;
            background: #FFFFFF;
            border: 1/100rem solid #EEEEEE;
            .recommend_title{
              height:81/100rem;
              width: 100%;
              display: flex;
              border-bottom: 1/100rem solid #DDDDDD;
              div:nth-of-type(1){
                margin-left:50/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:81/100rem;
                position: relative;
                span{
                  position: absolute;
                  bottom: 0/100rem;
                  left:0/100rem;
                  width: 100%;
                  height:4/100rem;
                  background: #BA0132;
                }
              }
              div:nth-of-type(2){
                margin-left:85/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:81/100rem;
                position: relative;
                span{
                  position: absolute;
                  bottom: 0/100rem;
                  left:0/100rem;
                  width: 100%;
                  height:4/100rem;
                  background: #BA0132;
                }
              }
            }
            .recommend_list{
              margin-top:48/100rem;
              margin-left:50/100rem;
              max-width: 1178/100rem;
              height:215/100rem;
              display: flex;
              justify-content: space-between;
              .recommend_list_item{
                position: relative;
                background: #FFFFFF;
                box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
                width: 272/100rem;
                height:215/100rem;
                .imgUrl{
                  width: 272/100rem;
                  height:153/100rem;
                  overflow: hidden;
                  img{
                    width: 272/100rem;
                    height:153/100rem;
                    object-fit: cover;
                    float: left;
                    transition: 0.2s;
                  }
                  img:hover{transform:scale(1.1);}

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
                  bottom:75/100rem;
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
                .recommnd_name{
                  margin-top:16/100rem;
                  height:34/100rem;
                  width: 272/100rem;
                  text-indent: 15/100rem;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 18/100rem;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:25/100rem;
                  overflow:hidden;
                  text-overflow:ellipsis;
                  white-space:nowrap
                }
              }

            }
          }

        }
      }
      .blank1{
        height:72/100rem
      }
      .blank2{
        height:20/100rem;
      }
      .blank3{
        height:120/100rem;
      }
    }
    @media  (min-width:1301px){
      .main{
        width:100%;
        position: relative;
        .container{
          max-width: 1280px;
          height:auto;
          margin:0 auto;
          background: #ffffff;
          .navigation{
            padding-top:50px;
            height:60px;
            width: 100%;
            background: #f9f9f9;
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
          .con_detail{
            padding-top:50px;
            width: 100%;
            height:auto;
            background: #FFFFFF;
            border: 1px solid #EEEEEE;
            padding-bottom:47px;
            .con_detail_div{
              width: 100%;
              display: flex;
              .con_detail_left{
                width: 478px;
                margin-left:50px;
                .detail_left_row1{
                  width: 478px;
                  height:269px;
                  overflow: hidden;
                  img{
                    width: 478px;
                    height:269px;
                    object-fit:cover;
                    float: left;
                  }
                }
                .detail_left_row2{
                  display: flex;
                  margin-top:40px;
                  div:nth-of-type(1){
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14px;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40px;
                    span{
                      display: block;
                    }
                  }
                  div:nth-of-type(2){
                    margin-left:50px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 14px;
                    color: #2A2A2A;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 40px;
                    span{
                      display: block;
                    }
                  }
                }
                .detail_left_row3{
                  margin-top:22px;
                  height:20px;
                  display: flex;
                  .share{
                    font-family: PingFangSC-Regular;
                    font-size: 14px;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:20px;
                  }
                  .shareTo{
                    margin-left:38px;
                    width: 134px;
                    height:20px;
                    display: flex;
                    justify-content: space-between;
                    div{
                      width: 20px;
                      height:20px;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      cursor: pointer;
                      img{
                        width: auto;
                        max-height:20px;
                        float: left;
                      }
                    }
                  }
                }

              }
              .con_detail_right{
                width: 667px;
                margin-left:32px;
                .detail_right_title{
                  font-family: PingFangSC-Semibold;font-weight:600;
                  font-size: 30px;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:42px;
                }
                .detail_right_request{
                  height:30px;
                  margin-top:9px;
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  div:nth-of-type(1){
                    display: flex;
                    align-items: center;
                    span:nth-of-type(1){
                      height:30px;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 14px;
                      color: #666666;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:30px;
                    }
                    span:nth-of-type(2){
                      margin-left:6px;
                      background: #BA0132;
                      border-radius: 2px;
                      width:34px;
                      height:20px;
                      font-family: PingFangSC-Semibold;font-weight:600;
                      font-size: 14px;
                      color: #FFFFFF;
                      letter-spacing: 0;
                      text-align: center;
                      line-height:20px;
                    }
                  }
                  div:nth-of-type(2){
                    padding:0 16px;
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
                  div:nth-of-type(2):hover{
                    color: #FFFFFF;
                    background: #BA0132;
                    border:none;
                  }
                }
                .terms{
                  margin-top:25px;
                  height:28px;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 20px;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:28px;
                }
                .detail_right_subtitle{
                  margin-top:15px;
                  height: 22px;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 16px;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:22px;
                }
                .subtitle_con{
                  margin-top:15px;
                  margin-bottom:30px;
                  width: 667px;
                  padding:20px 0 0 0;
                  border: 1px solid #DDDDDD;
                  .subtitle_item{
                    width: 667px;
                    min-height:20px;
                    margin-bottom:17px;
                    display: flex;
                    span:nth-of-type(1){
                      width: 287px;
                      text-indent: 18px;
                      font-family: PingFangSC-Medium;font-weight:500;
                      font-size: 14px;
                      color: #999999;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:20px;
                    }
                    span:nth-of-type(2){
                      flex: 1;
                      padding-right: 15px;
                      font-family: PingFangSC-Regular;
                      font-size: 14px;
                      color: #666666;
                      letter-spacing: 0;
                      text-align: left;
                      line-height:20px;
                      .expand{
                        cursor: pointer;
                        font-family: PingFangSC-Medium;
                        font-weight: 500;
                        font-size: 14px;
                        color: #333333;
                        letter-spacing: 0;
                        text-align: left;
                        line-height:20px;
                      }
                    }
                  }
                }
              }
            }
            .List_video{
              margin-top:55px;
              max-width:1178px ;
              padding:0 52px 0 50px;
              .List_video_title{
                height:30px;
                font-family: PingFangSC-Semibold;font-weight:600;
                font-size: 22px;
                color: #666666;
                letter-spacing: 0;
                text-align: left;
                line-height:30px;
              }
              .List_video_con{
                margin-top:31px;
                max-width: 1178px;
                display: flex;
                justify-content: space-between;
                flex-wrap: wrap;
                .List_video_item{
                  background: #FFFFFF;
                  box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
                  width: 272px;
                  height:215px;
                  position: relative;
                  div:nth-of-type(1){
                    width: 272px;
                    height:153px;
                    overflow: hidden;
                    img{
                      width: 272px;
                      height:153px;
                      object-fit:cover;
                      float: left;
                      transition: 0.2s;
                    }
                    img:hover{transform:scale(1.1);}
                  }
                  div:nth-of-type(2){
                    position: absolute;
                    top:131px;
                    right:0px;
                    height:22px;
                    width: 272px;
                    background-image: linear-gradient(0deg, #000000 0%, rgba(0,0,0,0.00) 100%);
                    font-size: 12px;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    text-align: right;
                    line-height:22px;
                    span{

                      height:22px;
                      width: auto;
                      position: absolute;
                      top:0;
                      right:4px;
                      font-size: 12px;
                      color: #FFFFFF;
                      letter-spacing: 0;
                      text-align: right;
                      line-height:22px;

                    }
                  }
                  div:nth-of-type(3){
                    margin-top:16px;
                    height:34px;
                    width: 272px;
                    text-indent: 15px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18px;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25px;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap
                  }
                }
              }
            }
            .pager{
              margin-top:77px;
              height:28px;
              width: 100%;
            }
          }
          .recommend{
            width: 100%;
            height:388px;
            background: #FFFFFF;
            border: 1px solid #EEEEEE;
            .recommend_title{
              height:81px;
              width: 100%;
              display: flex;
              border-bottom: 1px solid #DDDDDD;
              div:nth-of-type(1){
                margin-left:50px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20px;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:81px;
                position: relative;
                span{
                  position: absolute;
                  bottom: 0px;
                  left:0px;
                  width: 100%;
                  height:4px;
                  background: #BA0132;
                }
              }
              div:nth-of-type(2){
                margin-left:85px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 20px;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:81px;
                position: relative;
                span{
                  position: absolute;
                  bottom: 0px;
                  left:0px;
                  width: 100%;
                  height:4px;
                  background: #BA0132;
                }
              }
            }
            .recommend_list{
              margin-top:48px;
              margin-left:50px;
              max-width: 1178px;
              height:215px;
              display: flex;
              justify-content: space-between;
              .recommend_list_item{
                position: relative;
                background: #FFFFFF;
                box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
                width: 272px;
                height:215px;
                .imgUrl{
                  width: 272px;
                  height:153px;
                  overflow: hidden;
                  img{
                    width: 272px;
                    height:153px;
                    object-fit: cover;
                    float: left;
                    transition: 0.2s;
                  }
                  img:hover{transform:scale(1.1);}

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
                  bottom:75px;
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
                .recommnd_name{
                  margin-top:16px;
                  height:34px;
                  width: 272px;
                  text-indent: 15px;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 18px;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height:25px;
                  overflow:hidden;
                  text-overflow:ellipsis;
                  white-space:nowrap
                }
              }

            }
          }

        }
      }
      .blank1{
        height:72px
      }
      .blank2{
        height:20px;
      }
      .blank3{
        height:120px;
      }
    }
  }


</style>
