<template>
  <div class="main">
    <loadingPage :showLoading="showLoading"></loadingPage>
    <universalTop :indexSign="indexSign" ></universalTop>
    <div class="topBlank" style="width: 100%;"></div>
    <div class="container">
      <div class="searchBox" v-if="searchkeyWords==''||searchkeyWords==null">
        <universalSearch :isSearchShow="false" :inputBgd="searchBag" :title="$t(searchTitle)" :subTitle="$t(searchSubTitle)" :seachPlaceholder="seachPlaceholder"></universalSearch>
      </div>
      <div class="searchBox1" v-else></div>
      <div class="maxWidth">
        <div class="typeListBox">
          <div class="typeList">
            <div class="type_list_row1 type_list_row">
              <span class="row1_div1" style="">{{$t('AllContentCategories.types')}}：</span>
              <span :style="typeStyle == 0?'color: #BB0737':''"  class="row1_div2 row1_1" @click="changeStyle(0)"  >{{$t('AllContentCategories.unlimited')}}</span>
              <span :style="typeStyle == 1?'color: #BB0737':''"  class="row1_div3 row1_1"  @click="changeStyle(1)">{{$t('AllContentCategories.list_only')}}</span>
              <span :style="typeStyle == 2?'color: #BB0737':''"  class="row1_div4 row1_1"  @click="changeStyle(2)">{{$t('AllContentCategories.video_only')}}</span>
            </div>
            <div class="type_list_row2 type_list_row">
              <div class="row2_div1"  style="">{{$t('AllContentCategories.types')}}：</div>
              <div class="row2_div2" :style="typeId == 0?'color: #BB0737':''"  @click="changeTypeId(0)"  >{{$t('AllContentCategories.unlimited')}}</div>
              <div style="cursor:pointer" class="row2_div3"  >
                <div :style=" typeId == item.id?'color: #BB0737':''" v-if="index>0" v-for="(item,index) in typeList" @click="changeTypeId(index)" :key="index">{{$t(item.lanage)}}</div>
              </div>
              <div :style=" typeId == 17?'color: #BB0737':''" class="type-cncoriginal" @click="changeTypeId(17)">{{$t(cncoriginal.lanage)}}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="searchBoxText"v-if="searchkeyWords!=''&&searchkeyWords!=null">
        <div class="searchClass">{{$t('AllContentCategories.about')}}“<span style="color: #BA0132;">{{searchkeyWords}}</span>”{{$t('AllContentCategories.of')}}，{{$t('AllContentCategories.total')}}<span> {{searchTotal}}</span>{{$t('AllContentCategories.result')}}</div>
      </div>


      <div class="contentListBox">
        <div class="contentList">
          <div class="hoverItem" v-for="(item,index) in mediaList" :key="index" >
            <!--<div class="shadow"></div>-->
            <div v-if="item.type == 'playlist'" class="content_list">{{$t('AllContentCategories.content_list')}}</div>
            <div v-if="item.type == 'playlist'" class="list_number">
              <img src="/static/icon/Group4.png" alt="">
              <span>{{item.videos_count}}{{$t('listDetails.one')}}</span>
            </div>
            <div v-if="item.type == 'video'" class="duration">
              {{item.newduration}}
            </div>
            <div class="mediaItem">
              <div class="bottomSharow"></div>
              <div class="itemImg" style="cursor:pointer"  @click="toDetails(item.type,item.ivx_id)"><img :src="item.cover_image[0]" alt=""></div>
              <div class="itemName">{{item.title[0]}}</div>
              <div class="itemDescription">{{$t(item.newgener)}}</div>
              <div class="source" style="cursor:pointer"  @click="toChannelDetail(item.property_id)">
                <div class="sourceImg"><img :src="item.property_logo[0]" alt=""></div>
                <div class="sourceName">{{item.property_name[0]}}</div>
              </div>
            </div>
          </div>
          <div class="hoverItem" v-for="(item,index) in (4 - mediaList.length%4)" v-if="mediaList.length%4>0"></div>
        </div>
      </div>

      <div class="bottomBlank" style="width: 100%; background: #f9f9f9;"></div>
      <div class="Pagination" v-if="all_count>16">
        <Pagenation style="background: #f9f9f9;" :allCount='all_count' :allPage='allPage' :countPagea="countPage" :meicount="page_count" :jumpcounta="jumpCount" @nextPage="nextPage"></Pagenation>
      </div>
      <div class="containerPadding"  style="width: 100%; background: #f9f9f9;"></div>
    </div>
    <universalBottom></universalBottom>

  </div>
</template>
<script>
  import universalTop from '../components/universalTop'
  import universalBottom from '../components/universalBottom'
  import Pagenation from '../components/pager/pager.vue'
  import universalSearch from '../components/universalSearch/universalSearch.vue'
  import { getPlayList, } from '@/utils/global/axios.js'
  import loadingPage from '../components/loading/loading.vue'
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
        searchBag:'background:#ffffff',
        searchTitle:'AllContentCategories.title',
        searchSubTitle:'AllContentCategories.sub_title',
        seachPlaceholder:this.$t('AllContentCategories.seach_placeholder'),
        all_count:0,//总条数
        allPage:0,//总页数
        countPage:1,//当前页
        page_count:16,//每页多少条
        jumpCount:1,//跳转页
        indexSign:false,
        showLoading:true,
        cncoriginal:{
          name:this.$t('index.cncoriginal'),
          typeId:'cncoriginal',
          lanage:'index.cncoriginal',
          id:17,
        },
        typeList:[
          {
            name:this.$t('index.all'),
            lanage:'index.all',
            typeId:'',
            id:0,
          },
          {
            //时政
            name:this.$t('index.politics'),
            typeId:'politics',
            lanage:'index.politics',
            id:1,
          },{
            //军事
            name:this.$t('index.military'),
            typeId:'military',
            lanage:'index.military',
            id:2,
          },{
            //财经
            name:this.$t('index.finance'),
            typeId:'finance',
            lanage:'index.finance',
            id:3,
          },{
            //科技
            name:this.$t('index.science'),
            typeId:'science',
            lanage:'index.science',
            id:4,
          },{
            //社会
            name:this.$t('index.society'),
            typeId:'society',
            lanage:'index.society',
            id:5,
          },{
            //人物
            name:this.$t('index.character'),
            typeId:'character',
            lanage:'index.character',
            id:6,
          },{
            //文化
            name:this.$t('index.cultrue'),
            typeId:'cultrue',
            lanage:'index.cultrue',
            id:7,
          },{
            //教育
            name:this.$t('index.education'),
            typeId:'education',
            lanage:'index.education',
            id:8,
          },{
            //自然
            name:this.$t('index.nature'),
            typeId:'nature',
            lanage:'index.nature',
            id:9,
          },{
            //美食
            name:this.$t('index.food'),
            typeId:'food',
            lanage:'index.food',
            id:10,
          },{
            //健康
            name:this.$t('index.health'),
            typeId:'health',
            lanage:'index.health',
            id:11,
          },{
            //旅游
            name:this.$t('index.tourism'),
            typeId:'travel',
            lanage:'index.tourism',
            id:12,
          },{
            //体育
            name:this.$t('index.sports'),
            typeId:'sports',
            lanage:'index.sports',
            id:13,
          },{
            //汽车
            name:this.$t('index.automobile'),
            typeId:'automobile',
            lanage:'index.automobile',
            id:14,
          },{
            //娱乐：暂时未原创，等待hcl方确认
            name:this.$t('index.entertainment'),
            typeId:'entertainment',
            lanage:'index.entertainment',
            id:15,
          },{
            //其他
            name:this.$t('index.others'),
            typeId:'others',
            lanage:'index.others',
            id:16,
          },
        ],
        mediaList:[],
        searchkeyWords:'',
        searchFilter:'',
        searchGenre:0,
        searchTotal:0,
      }
    },
    created(){
      this.searchkeyWords = this.$route.query.keyWoeds
      this.searchFilter = this.$route.query.filter
      if(this.searchkeyWords!=undefined){
        this.getSearchList(this.searchFilter,this.searchGenre,0)
      }else{
        this.getList(this.typeStyle,this.typeId,0)
      }
    },
    computed:{
      typeId(){
        return this.$store.state.typeId
      },
      typeStyle(){
        return this.$store.state.typeStyle
      },
      searchFilterShow(){
        if(this.searchFilter == 'all'){
          return ''
        }else if(this.searchFilter == 'playlist'){
          return 'AllContentCategories.playlist'
        }else if(this.searchFilter == 'video'){
          return 'AllContentCategories.video'
        }
      }
    },

    methods:{
      getSearchList(searchFilter,searchGenre,start){
        let paramsFilter = ''
        let paramsGenre = ''
        if(searchFilter == 'all'){
          paramsFilter = ''
        }else if(searchFilter == 'playlist'){
          paramsFilter = '&filter=playlist'
        }else if(searchFilter == 'video'){
          paramsFilter = '&filter=video'
        }
        if(searchGenre == 0){
          paramsGenre = ''
        }else if(searchGenre == 17){
          paramsGenre = '&genre=cncoriginal'
        }else{
          paramsGenre = '&genre='+this.typeList[searchGenre].typeId
        }
        let  params ='?keywords='+this.searchkeyWords+'&start='+start+'&limit=16'+paramsFilter+paramsGenre
        getPlayList(params).then(res=>{
          if(res.data){
            this.ReturnParameter(res)
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },

      getList(typeStyle,typeId,val){
        let parameterStyle = ''
        let parameterType = ''
        if(typeStyle == 0){
          parameterStyle = ''
        }else if(typeStyle == 1){
          parameterStyle = '&filter=playlist'
        }else if(typeStyle == 2){
          parameterStyle = '&filter=video'
        }
        if(typeId==0){
          parameterType = ''
        }else if(typeId==17){
          parameterType = '&genre=cncoriginal'
        }else{
          parameterType = '&genre='+this.typeList[typeId].typeId
        }
        let params = '?limit=16&start='+val+parameterStyle+parameterType
        getPlayList(params).then(res=>{
          if(res.data){
            this.ReturnParameter(res)
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      nextPage(val){
        if(this.searchkeyWords!=undefined){
          this.showLoading = true
          let newval = (val-1)*16
          this.getSearchList(this.searchFilter,this.searchGenre,newval)
        }else{
          this.showLoading = true
          let newval = (val-1)*16
          this.getList(this.typeStyle,this.typeId,newval)
        }



      },
      changeStyle(index){
        if(this.searchkeyWords!=undefined){
          let filterArr = [
            {
              name:'all',
            },
            {
              name:'playlist',
            },{
              name:'video',
            }
          ]
          if(this.searchFilter ==  filterArr[index].name){
            return
          }else {
            this.searchFilter = filterArr[index].name
            this.$store.commit('newTypeStyle',index)
            this.getSearchList(this.searchFilter,this.searchGenre,0)
            this.countPage = 1
          }
        }else{
          if(this.typeStyle == index){
            return
          }else{
            this.$store.commit('newTypeStyle',index)
            this.getList(this.typeStyle,this.typeId,0)
            this.countPage = 1
            this.jumpCount = 1
          }
        }


      },
      changeTypeId(index){
        if(this.searchkeyWords!=undefined){ //搜索页
          if(this.searchGenre == index){
            return
          }else{
            this.searchGenre = index
            this.$store.commit('newTypeId',index)
            this.getSearchList(this.searchFilter,this.searchGenre,0)
            this.countPage = 1
          }
        }else{ //所有内容分类页
          if(this.typeId == index){
            return
          }else{
              this.$store.commit('newTypeId',index)
              this.getList(this.typeStyle,this.typeId,0)
              this.countPage = 1
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
      ReturnParameter(res){
        this.all_count = res.data.response.numFound
        this.allPage = Math.ceil(res.data.response.numFound/16)
        this.countPage = res.data.response.start/16 + 1
        this.mediaList = res.data.response.docs
        this.searchTotal = res.data.response.numFound
        this.mediaList.forEach((item,index)=>{
          item.newduration = this.formatSeconds(item.entry_duration)
          this.Gloal.playlistType.forEach((item1,index1)=>{
            if(item.genre[0] == item1.typeId){
              item.genre[0] = item1.name
            }
          })
          item.newgener = item.genre.join('/')
        })
        this.showLoading = false
      },

      toDetails(type,id){
        if(type == 'playlist'){
          this.$router.push({name: 'ContentListDetails',query:{playlistId:id,typeId:this.typeId,typeStyle:this.typeStyle}})
        }else{
          this.$router.push({name: 'videoDetails',query:{videoId:id,typeId:this.typeId,typeStyle:this.typeStyle}})
        }
      },
      toChannelDetail(id){
        this.$router.push({name: 'channelDetails',query:{channelId:id}})
      },
    }

  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .main{
        margin:0 auto;
        width:100%;
        position: relative;
        background: #ffffff;
        .container{
          margin:0 auto;
          width: 100%;
          .searchBox{
            width: 1282/100rem;
            margin:0 auto;
          }
          .searchBox1{
            height:100/100rem;
            width: 100%;
          }
          .topSearch{
            width: 100%;
            height:42/100rem;
            padding:42/100rem 0 50/100rem 0;
            display: flex;
            justify-content: space-between;
            span:nth-of-type(1){
              font-family:PingFangSC-Medium;
              font-size: 30/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:42/100rem;
            }
            span:nth-of-type(2){
              font-family: PingFangSC-Regular;
              font-size: 14/100rem;
              color: #999999;
              letter-spacing: 0;
              text-align: left;
              line-height:20/100rem;
              margin-left:12/100rem;
            }
          }
          .searchInput{
            width: 321/100rem;
            height:42/100rem;
            border: 1/100rem solid #DDDDDD;
            border-radius: 4/100rem;
            display: flex;
            .input{
              height:40/100rem;
              width: 279/100rem;
              border-radius: 4/100rem;

              line-height: 42/100rem;
              text-indent: 12/100rem;
              border:none;
              outline-style: none ;
              outline-width: 0/100rem ;
              text-shadow: none ;
              -webkit-appearance: none ;
              -webkit-user-select: text ;
              outline-color: transparent ;
              box-shadow: none;

            }
            input::-webkit-input-placeholder {
              color: #999999;
              font-size:14/100rem;
            } 
             input:-moz-placeholder {
               color: #999999;
               font-size:14/100rem;
             } 
              input::-moz-placeholder {
                color:#999999;
                font-size:14/100rem;
              } 
               input:-ms-input-placeholder {
                 color: #999999;
                 font-size:14/100rem;
               } 
                .searchIcon{
                  width: 42/100rem;
                  height:42/100rem;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  img{
                    width: auto;
                    height:auto;
                  }
                }
          }
          .searchBoxText{
            width: 100%;
            padding-top: 42/100rem;
            background: #f9f9f9;
            .searchClass{
              width: 1282/100rem;
              margin:0 auto;
              background: #f9f9f9;
              font-family: PingFangSC-Regular;
              font-size: 14/100rem;
              color: #666666;
              letter-spacing: 0;
              text-align: center;
              line-height:20/100rem;
            }
          }

          .maxWidth{
            width: 100%;
            background: -webkit-repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            background: -o-repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            background: -moz-repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            background: repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            .typeListBox{
              width: 1282/100rem;
              height:150/100rem;
              margin:0 auto;
              padding-top:31/100rem;
              position: relative;
              background: #ffffff;
              box-shadow: 0 0 12/100rem 0 rgba(0,0,0,0.05);
              .typeList{
                width: 1282/100rem;
                position: absolute;
                top:31/100rem;
                left:0;
                z-index:199;
                background: #ffffff;

                .type_list_row{
                  width: 100%;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 16/100rem;
                  color: #333333;
                  letter-spacing: 1.23/100rem;
                  line-height:22/100rem;
                  display: flex;
                }
                .type_list_row1{
                }
                .type_list_row2{
                  margin-top:28/100rem;
                  width: 100%;
                  position: relative;
                  .type-cncoriginal{
                    position: absolute;
                    top: 50/100rem;
                    left:133/100rem;
                    font-size: 16/100rem;
                    color: #333333;
                    letter-spacing: 1.23/100rem;
                    text-align: left;
                    font-family: PingFangSC-Medium;
                    font-weight: 500;
                    cursor: pointer;
                  }
                }
              }
            }
          }
          .contentListBox{
            width: 100%;
            background:#f9f9f9;
            padding-top:10/100rem;
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
                .mediaItem{
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
                    position: absolute;
                    top:1000/100rem;
                    left:12/100rem;
                    width: 280/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 14/100rem;
                    color: rgba(255,255,255,0.78);
                    letter-spacing: 0;
                    text-align: left;
                    word-wrap:break-word
                  }
                  .hoverImg{
                    -webkit-transition: 0.2s linear;
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
                      object-fit:cover;
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

          .Pagination{
            width: 100%;
            height: 28/100rem;
            text-align: center;
          }
        }
        .topBlank{
          height:76/100rem
        }
        .bottomBlank{
          height:61/100rem;
        }
        .containerPadding{
          height:150/100rem;
        }
        .row1_div1 , .row2_div1{
          width:83/100rem;
          text-align: right;
        }
        .row1_div2  , .row2_div2{
          width: 35/100rem;
          margin-left: 50/100rem;
          text-align: left;
          cursor:pointer
        }
        .row1_div4{
          margin-left: 50/100rem;
          text-align: left;
          cursor:pointer
        }
        .row1_1{
          cursor:pointer
        }
        .row1_div3{
          margin-left: 97/100rem;
        }
        .row2_div3{
          height:72/100rem;
          margin-left: 97/100rem;
          width: 920/100rem;
          display: flex;
          flex-wrap: wrap;
          flex: 1;
          justify-content: space-between;
          align-content:space-between;
          line-height:22/100rem;
          font-size: 16/100rem;
          color: #333333;
          letter-spacing: 1.23/100rem;
          text-align: left;
          font-family: PingFangSC-Medium;
          font-weight: 500;
          div{
            width: 115/100rem;
          }
        }

      }
    }
    @media  (min-width:1301px){
      .main{
        margin:0 auto;
        width:100%;
        position: relative;
        background: #ffffff;
        .container{
          margin:0 auto;
          width: 100%;
          .searchBox{
            width: 1282px;
            margin:0 auto;
          }
          .searchBox1{
            height:100px;
            width: 100%;
          }
          .topSearch{
            width: 100%;
            height:42px;
            padding:42px 0 50px 0;
            display: flex;
            justify-content: space-between;
            span:nth-of-type(1){
              font-family:PingFangSC-Medium;
              font-size: 30px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:42px;
            }
            span:nth-of-type(2){
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #999999;
              letter-spacing: 0;
              text-align: left;
              line-height:20px;
              margin-left:12px;
            }
          }
          .searchInput{
            width: 321px;
            height:42px;
            border: 1px solid #DDDDDD;
            border-radius: 4px;
            display: flex;
            .input{
              height:40px;
              width: 279px;
              border-radius: 4px;

              line-height: 42px;
              text-indent: 12px;
              border:none;
              outline-style: none ;
              outline-width: 0px ;
              text-shadow: none ;
              -webkit-appearance: none ;
              -webkit-user-select: text ;
              outline-color: transparent ;
              box-shadow: none;
            }
            input::-webkit-input-placeholder {
              color: #999999;
              font-size:14px;
            } 
             input:-moz-placeholder {
               color: #999999;
               font-size:14px;
             } 
              input::-moz-placeholder {
                color:#999999;
                font-size:14px;
              } 
               input:-ms-input-placeholder {
                 color: #999999;
                 font-size:14px;
               } 
                .searchIcon{
                  width: 42px;
                  height:42px;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  img{
                    width: auto;
                    height:auto;
                  }
                }
          }
          .searchBoxText{
            width: 100%;
            padding-top: 42px;
            background: #f9f9f9;
            .searchClass{
              width: 1282px;
              margin:0 auto;
              background: #f9f9f9;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #666666;
              letter-spacing: 0;
              /*text-align: left;*/
              line-height:20px;
              text-align: center;
            }
          }

          .maxWidth{
            width: 100%;
            background: -webkit-repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            /* Opera 11.1 - 12.0 */
            background: -o-repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            /* Firefox 3.6 - 15 */
            background: -moz-repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            /* 标准的语法 */
            background: repeating-linear-gradient(180deg,#ffffff, #ffffff 50%, #f9f9f9 50%, #f9f9f9 100%);
            .typeListBox{
              width: 1282px;
              height:150px;
              margin:0 auto;
              padding-top:31px;
              position: relative;
              background: #ffffff;
              box-shadow: 0 0 12px 0 rgba(0,0,0,0.05);
              .typeList{
                width: 1282px;
                position: absolute;
                top:31px;
                left:0;
                z-index:199;
                background: #ffffff;

                .type_list_row{
                  width: 100%;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 16px;
                  color: #333333;
                  letter-spacing: 1.23px;
                  line-height:22px;
                  display: flex;
                }
                .type_list_row1{
                }
                .type_list_row2{
                  margin-top:28px;
                  width: 100%;
                  position: relative;
                  .type-cncoriginal{
                    position: absolute;
                    top: 50px;
                    left:133px;
                    font-size: 16px;
                    color: #333333;
                    letter-spacing: 1.23px;
                    text-align: left;
                    font-family: PingFangSC-Medium;
                    font-weight: 500;
                    cursor: pointer;
                  }
                }
              }
            }
          }
          .contentListBox{
            width: 100%;
            background:#f9f9f9;
            padding-top:10px;
            .contentList{
              margin:0 auto;
              max-width: 1282px;
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
                  height:309px;
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
                  .hoverDescription{
                    -webkit-transition: 0.2s linear;
                    position: absolute;
                    top:1000px;
                    left:12px;
                    width: 280px;
                    font-family: PingFangSC-Regular;
                    font-size: 14px;
                    color: rgba(255,255,255,0.78);
                    letter-spacing: 0;
                    text-align: left;
                    word-wrap:break-word
                  }
                  .hoverImg{
                    -webkit-transition: 0.2s linear;
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
                    left: -280px;
                    height:25px;
                    width:280px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18px;
                    color: #FFFFFF;
                    letter-spacing: 0;
                    line-height:25px;
                    text-align: left;

                  }
                  .hoverMinor{
                    position: absolute;
                    top:193px;
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

          .Pagination{
            width: 100%;
            height: 28px;
            text-align: center;
          }
        }
        .topBlank{
          height:76px
        }
        .bottomBlank{
          height:61px;
        }
        .containerPadding{
          height:150px;
        }
        .row1_div1 , .row2_div1{
          width:83px;
          text-align: right;
        }
        .row1_div2  , .row2_div2{
          width: 35px;
          margin-left: 50px;
          text-align: left;
          cursor:pointer
        }
        .row1_div4{
          margin-left: 50px;
          text-align: left;
          cursor:pointer
        }
        .row1_1{
          cursor:pointer
        }
        .row1_div3{
          margin-left: 97px;
        }
        .row2_div3{
          height:72px;
          margin-left: 97px;
          width: 920px;
          display: flex;
          flex-wrap: wrap;
          flex: 1;
          justify-content: space-between;
          align-content:space-between;
          line-height:22px;
          font-size: 16px;
          color: #333333;
          letter-spacing: 1.23px;
          text-align: left;
          font-family: PingFangSC-Medium;
          font-weight: 500;
          div{
            width: 115px;
          }
        }

      }
    }
  }



</style>
