<template>
  <div class="main">
    <loadingPage :showLoading="showLoading"></loadingPage>
    <universalTop :indexSign="indexSign" ></universalTop>
    <div class="blank1" style=""></div>
    <div class="search">
      <universalSearch :title="$t(searchTitle)" :subTitle="searchSubTitle"
                       :seachPlaceholder="seachPlaceholder" :isSearchShow="false"></universalSearch>
    </div>
    <div class="container">
      <div class="titleNav">
        <div @click="selectNav(index)" v-for="(item,index) in navList" :key="index">
          {{$t(item.lanage)}}
          <span v-if="item.isShow"></span>
        </div>
      </div>

      <div class="topNav">
        <div >{{$t('requestList.total')}}</div>
        <div >{{navList[0].isShow?notRequestList.length:requestListTotal}} {{$t('requestList.contentList')}}</div>
        <div >{{$t('requestList.contentCategory')}}</div>
        <div >{{$t('requestList.numberOfContent')}}</div>
        <div >{{$t('requestList.effectiveTime')}}</div>
        <div v-if="navList[0].isShow" >{{$t('requestList.joinTime')}}</div>
        <div v-else-if="navList[1].isShow" >{{$t('requestList.purchaseTime')}}</div>
      </div>
      <div class="emptyBox" v-if="navList[0].isShow&&isnotRequestListEmpty">
        <div class="conBox">
          <div class="imgBox"><img src="/static/icon/Empty.png" alt=""></div>
          <div class="emptyText">{{$t('requestList.emptyContent')}}</div>
        </div>
      </div>

      <div class="itemBox" v-if="navList[0].isShow&&!isnotRequestListEmpty" >
        <div class="requestItem" v-for="(item,index) in notRequestList" :key="index">
          <div class="leftBox">
            <div  style="" class="selectBox" @click.stop="toSelect(index)">
              <div :class="item.selected?'itemSelect1':'itemSelect2'"><img src="/static/icon/checkNumber.png" alt=""></div>
            </div>
            <div style="" @click="toConDetails(item.id)" class="itemimgUrl"><img :src="item.playlist.thumbnail_url" alt=""></div>
            <div style="" class="itemSource">
              <div @click="toConDetails(item.id)">{{item.playlist.name}}</div>
              <div>{{$t('requestList.source')}} <span @click="toChannelDetails(item.playlist.property_id)">{{item.playlist.property_name}}</span><span>{{item.playlist.organization_name}}</span> </div>
            </div>
          </div>

          <div style="" class="itemCategory">{{$t(item.playlist.genre)}}</div>
          <div style="" class="itemNumber">{{item.playlist.entries_count}}</div>
          <div style="" class="validityPeriod">
            <div>{{item.playlist.end_date==null?$t('requestList.everlasting'):item.playlist.end_date}}</div>
          </div>
          <div  style="" class="validityPeriod"><div>{{item.created_at}}</div></div>
        </div>
      </div>
      <div class="summary"  v-if="navList[0].isShow&&!isnotRequestListEmpty">
        <div class="summarySelectBox" @click="selectAll">
          <div v-if="isAll" class="itemSelect1"><img src="/static/icon/checkNumber.png" alt=""></div>
          <div v-else class="itemSelect2"><img src="/static/icon/checkNumber.png" alt=""></div>
        </div>
        <div class="selectAll">{{$t('requestList.selectAll')}}</div>
        <div class="selected">{{$t('requestList.selected')}}<span>{{nums}}</span>{{$t('requestList.contentList')}}</div>
        <div :style="nums==0?' cursor: not-allowed':''" class="delete" @click="deleteClick">{{$t('requestList.delete')}}</div>
        <div :style="nums==0?' cursor: not-allowed':''" class="obtain" @click="submitClick">{{$t('requestList.getContent')}}</div>
      </div>

      <div class="emptyBox" v-if="!navList[0].isShow&&isRequestListEmpty">
        <div class="conBox">
          <div class="imgBox"><img src="/static/icon/Empty.png" alt=""></div>
          <div class="emptyText">{{$t('requestList.RequestedEmpty')}}</div>
        </div>
      </div>
      <div class="itemBox" v-if="!navList[0].isShow&&!isRequestListEmpty" style="min-height: 150px">
        <div class="requestItem" v-for="(item,index) in requestList" :key="index">
          <div class="leftBox">
            <div class="subselectBox" ></div>
            <div style="" style="" @click="toConDetails(item.id)" class="itemimgUrl"><img :src="item.playlist.thumbnail_url" alt=""></div>
            <div style="" class="itemSource">
              <div style="" @click="toConDetails(item.id)">{{item.playlist.name}}</div>
              <div>{{$t('requestList.source')}} <span @click="toChannelDetails(item.playlist.property_id)">{{item.playlist.property_name}}</span><span>{{item.playlist.organization_name}}</span> </div>
            </div>
          </div>

          <div style="" class="itemCategory">{{$t(item.playlist.genre)}}</div>
          <div style="" class="itemNumber">{{item.playlist.entries_count}}</div>
          <div style="" class="validityPeriod">
            <div>{{item.playlist.end_date==null?$t('requestList.everlasting'):item.playlist.end_date}}</div>
          </div>
          <div  style="" class="validityPeriod"><div>{{item.requested_at}}</div></div>
        </div>
      </div>
      <div class="Pagination" v-if="navList[1].isShow&&all_count>7&&requestList.length!=0">
        <Pagenation style="background: #f9f9f9;" :allCount='all_count' :allPage='allPage' :countPagea="countPage" :meicount="page_count" :jumpcounta="jumpCount" @nextPage="nextPage"></Pagenation>
      </div>
      <div v-else class="Pagination"></div>
    </div>
    <universalBottom></universalBottom>
    <deleteDialog :isShow="isDeleteDialogShow" @deleteClose="deleteClose" @deleteSubmit="deleteSubmit"></deleteDialog>
    <dialogDemo  :isShow ='isSubmitDialogShow' @closeDialog="closeDialog" @obtionSubmit="obtionSubmit"></dialogDemo>
  </div>
</template>
<script>
  import Vue from 'vue'
  import universalTop from '../../components/universalTop'
  import universalBottom from '../../components/universalBottom'
  import universalSearch from '../../components/universalSearch/universalSearch.vue'
  import Pagenation from '../../components/pager/pager.vue'
  import dialogDemo from '../../components/dialog/dialog'
  import deleteDialog from '../../components/dialog/deleteDialog'
  import loadingPage from '../../components/loading/loading.vue'
  import {  Car, CarDelete,CarCheckout,getSPuser} from '@/utils/global/axios.js'
  export default {
    components:{
      universalTop,
      universalBottom,
      universalSearch,
      dialogDemo,
      deleteDialog,
      Pagenation,
      loadingPage,
    },
    inject:['reload'],
    data(){
      return{
        all_count:0,//总条数
        allPage:0,//总页数
        countPage:1,//当前页
        page_count:7,//每页多少条
        jumpCount:1,//跳转页
        all_count2:0,//总条数
        allPage2:0,//总页数
        countPage2:1,//当前页
        page_count2:16,//每页多少条
        jumpCount2:1,//跳转页
        requestListTotal:0,
        isSubmitDialogShow:false,
        isDeleteDialogShow:false,
        indexSign:false,
        showLoading:true,
        searchTitle:'requestList.title',
        searchSubTitle:this.$t('requestList.sub_title'),
        seachPlaceholder:this.$t('requestList.seach_placeholder'),
        totalList:[],
        notRequestList:[],
        requestList:[],
        navList:[
          {
            title: this.$t('requestList.notPurchased'),
            lanage:'requestList.notPurchased',
            isShow:true,
          },
          {
            title:this.$t('requestList.bought'),
            lanage:'requestList.bought',
            isShow:false,
          },
        ],
        selectedNum:1,
        isAll:false,
        userList:'',
        isnotRequestListEmpty:false,
        isRequestListEmpty:false,
      }
    },
    watch:{
      isAll(){
      },
      nums(index){
        if(this.notRequestList.length == index){
          this.isAll = true
        }else{
          this.isAll = false
        }
      },
      notRequestList(){
        if(this.notRequestList.length == 0 || this.notRequestList==null){
          this.isnotRequestListEmpty = true
        }else{
          this.isnotRequestListEmpty = false
        }
      },
      requestList(){
        if(this.requestList.length == 0 || this.requestList==null){
          this.isRequestListEmpty = true
        }else{
          this.isRequestListEmpty = false
        }
      },
    },
    computed:{
      nums(){
        let that = this
        let count = 0
        that.notRequestList.forEach((item,index)=>{
          if(item.selected){
            count += 1
          }
        })
        return count
      }
    },
    created(){
      this.getNotRequestedlist()
    },
    methods:{
      toSelect(index){
        let that = this
        that.notRequestList[index].selected = !that.notRequestList[index].selected
      },
      selectAll(){
        this.isAll = !this.isAll
        this.notRequestList.forEach((item,index)=>{
          item.selected = this.isAll
        })
      },

      selectNav(index){
        if(this.navList[index].isShow == true){
          return
        }else{
          this.navList.forEach((item,index1)=>{
            item.isShow = false
          })
          this.navList[index].isShow = true
          if(index == 1){
            this.showLoading = true
            this.getRequestedlist(0)
          }

        }

      },

      //获取未请求内容
      getNotRequestedlist(){
        let params = ''
        Car(params).then(res=>{
          if(res.data.status=='success'){
            this.notRequestList = res.data.data
            let number = res.data.data.length
            this.$store.commit('newPurchaseQuantity',number)
            this.notRequestList.forEach((item,index)=>{
              Vue.set( item, 'selected', false )
              item.requested_at = this.getDate(item.requested_at)
              item.created_at = this.getDate(item.created_at)
              item.playlist.genre = this.changeGenre(item.playlist.genre)
            })
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },
      //获取请求内容
      getRequestedlist(start){
        let params = '?requested=1&limit=7&start='+start
        Car(params).then(res=>{
          if(res.data.status == 'success'){
            this.all_count = res.data.total_count
            this.allPage = Math.ceil(res.data.total_count/7)
            this.requestList = res.data.data
            this.requestListTotal = res.data.total_count
            this.requestList.forEach((item,index)=>{
              item.requested_at = this.getDate(item.requested_at)
              item.created_at = this.getDate(item.created_at)
              item.playlist.genre = this.changeGenre(item.playlist.genre)
            })
          }
          this.showLoading = false
        }).catch(err=>{
          this.showLoading = false
        })
      },

      nextPage(val){
        let start = (val-1)*7
        this.showLoading = true
        this.getRequestedlist(start)
      },
      //删除按钮
      deleteClick(){
        if(this.nums == 0){
        }else{
          this.isDeleteDialogShow = true
        }
      },

      //删除dialog的叉号
      deleteClose(){
        this.isDeleteDialogShow = false
      },
      //删除dialog的确定按钮
      deleteSubmit(){
        let that = this
        this.showLoading = true
        let arr = []
        this.notRequestList.forEach((item,index)=>{
          if(item.selected){
            arr.push(item.id)
          }
        })
        let ids = arr.join(',')
        let params = {
          ids:ids
        }
        CarDelete(params).then(res=>{
          if(res.data.status == 'success'){
            let params1 = ''
            return  Car(params1)
          }
          this.showLoading = false
        })
        .then(res1=>{
          if(res1.data.status =='success'){
            this.isDeleteDialogShow = false
            this.notRequestList = (res1.data.data)
            let number = res1.data.data.length
            this.$store.commit('newPurchaseQuantity',number)
            this.notRequestList.forEach((val,index)=>{
              Vue.set( val, 'selected', false )
              val.requested_at = this.getDate(val.requested_at)
              val.created_at = this.getDate(val.created_at)
              val.playlist.end_date = this.getDate(val.playlist.end_date)
              val.playlist.genre = this.changeGenre(val.playlist.genre)
            })
          }
          this.showLoading = false
        })
        .catch(err=>{
          this.showLoading = false
        })

      },

      //获取内容按钮
      submitClick(){
        if(this.nums == 0){
        }else{
          this.isSubmitDialogShow = true
        }

      },
      //获取内容按钮关闭按钮
      closeDialog(){
        this.isSubmitDialogShow = false
      },
      //点击获取按钮
      obtionSubmit(obtionInfo){
        if(obtionInfo.theme!=''&&obtionInfo.info!=''&&obtionInfo.sp_ids!=''){
          this.showLoading = true
          let arr = []
          this.notRequestList.forEach((item,index)=>{
            if(item.selected){
              arr.push(item.id)
            }
          })
          let params = {
            subject:obtionInfo.theme,
            message:obtionInfo.info,
            sp_ids:obtionInfo.sp_ids,
            ids:arr.join(','),
          }
          CarCheckout(params).then(res=>{
            if(res.data.status == 'success'){
              let params = ''
              return  Car(params)
            }
            this.showLoading = false
          })
          .then(res=>{
            if(res.data.status == 'success'){
              this.isSubmitDialogShow = false
              this.notRequestList = res.data.data
              let number = res.data.data.length
              this.$store.commit('newPurchaseQuantity',number)
              this.notRequestList.forEach((item,index)=>{
                Vue.set( item, 'selected', false )
                item.requested_at = this.getDate(item.requested_at)
                item.created_at = this.getDate(item.created_at)
                item.playlist.genre = this.changeGenre(item.playlist.genre)
              })
            }
            this.showLoading = false
          })
          .catch(err=>{
            this.showLoading = false
          })
        }
      },

      toChannelDetails(id){
        this.$router.push({name: 'channelDetails',query:{channelId:id}})
      },
      toConDetails(id){
        this.$router.push({name: 'ContentListDetails',query:{playlistId:id}})
      },

      changeGenre(genre){
        let newGenre = genre
        this.Gloal.playlistType.forEach((item1,index1)=>{
          if(genre == item1.typeId){
            newGenre =  item1.name
          }
        })
        return newGenre
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
    },

  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .main{
        position: relative;
        .search{
          width: 1287/100rem;
          margin:0 auto;
        }
        .container{
          width: 1287/100rem;
          margin:0 auto;
          background: #f9f9f9;
          .titleNav{
            height:50/100rem;
            margin-left:40/100rem;
            display: flex;
            align-items: flex-start;
            div{
              min-width: 80/100rem;
              padding:0 10/100rem;
              height:50/100rem;
              line-height:28/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 20/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: center;
              cursor: pointer;
              position: relative;
              span{
                position: absolute;
                bottom: 0;
                left:0;
                width: 100%;
                height:5/100rem;
                background: #BA0132;

              }
            }
            div:nth-of-type(2){
              margin-left: 50/100rem;
            }

          }
          .topNav{
            width: 1287/100rem;
            height:76/100rem;
            background: #ffffff;
            box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
            display: flex;
            div{
              height:76/100rem;
              letter-spacing: 0;
              text-align: left;
              line-height:76/100rem;
            }
            div:nth-of-type(1){
              width:52/100rem;
              margin-left:40/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
            }
            div:nth-of-type(2){
              width: 103/100rem;
              font-family: PingFangSC-Regular;
              font-size: 16/100rem;
              color: #2A2A2A;
            }
            div:nth-of-type(3){
              margin-left:379/100rem;
              width: 208/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              text-align: center;
            }
            div:nth-of-type(4){
              width:208/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              text-align: center;
            }
            div:nth-of-type(5){
              width: 208/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              text-align: center;
            }
            div:nth-of-type(6){
              width: 208/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              text-align: center;
            }
          }
          .emptyBox{
            margin-top:10/100rem;
            width: 1287/100rem;
            height: 600/100rem;
            display: flex;
            justify-content: center;
            background: #fff;
            box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
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
          .itemBox{
            width: 1287/100rem;
            background: #f9f9f9;
            margin-top:10/100rem;
            padding-bottom: 10/100rem;
            .requestItem{
              width: 1287/100rem;
              height:136/100rem;
              background: #ffffff;
              display: flex;
              align-items: center;
              margin-bottom: 15/100rem;
              box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
              .leftBox{
                display: flex;
                width: 555/100rem;
                /*flex: 1;*/
                align-items: center;

                .selectBox{
                  width: 20/100rem;
                  height:20/100rem;
                  margin-left:40/100rem;
                  cursor: pointer;
                  .itemSelect1{
                    width:18/100rem ;
                    height:18/100rem;
                    border:1/100rem solid #BA0132;
                    background: #BA0132;
                    border-radius: 2/100rem;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    img{
                      width:16/100rem ;
                      height:12/100rem;
                    }
                  }
                  .itemSelect2{
                    width:18/100rem ;
                    height:18/100rem;
                    border:1/100rem solid #999999;
                    background: #ffffff;
                    border-radius: 2/100rem;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    img{
                      width:16/100rem ;
                      height:12/100rem;
                    }
                  }
                }
                .subselectBox{
                  width: 60/100rem;
                  height:20/100rem;
                }
                .itemimgUrl{
                  width: 160/100rem;
                  height:90/100rem;
                  margin-left:18/100rem;
                  cursor: pointer;
                  overflow: hidden;
                  img{
                    width: 160/100rem;
                    height:90/100rem;
                    object-fit:cover;
                    float: left;
                  }
                }
                .itemSource{
                  width: 297/100rem;
                  height:136/100rem;
                  margin-left:16/100rem;
                  div:nth-of-type(1){
                    cursor: pointer;
                    margin-top:33/100rem;
                    width: 297/100rem;
                    height:42/100rem;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18/100rem;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25/100rem;
                  }
                  div:nth-of-type(2){
                    width: 297/100rem;
                    margin-top:4/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 14/100rem;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:20/100rem;
                    span:nth-of-type(1){
                      color: #151515;
                      cursor: pointer;
                    }
                    span:nth-of-type(2){
                      margin-left:20/100rem;
                    }
                  }
                }
              }

              .itemCategory{
                width: 208/100rem;
                height:20/100rem;
                font-family: PingFangSC-Regular;
                font-size: 14/100rem;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                line-height:20/100rem;
              }
              .itemNumber{
                width: 208/100rem;
                height:20/100rem;
                font-size: 14/100rem;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                line-height:20/100rem;
              }
              .validityPeriod{
                width: 208/100rem;
                height:136/100rem;
                font-family: PingFangSC-Regular;
                font-size: 14/100rem;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                div{
                  display: block;
                  width: 170/100rem;
                  text-align: center;
                  word-break: break-all;
                  word-wrap: break-word;
                  white-space: pre-wrap;
                }
              }
            }
          }
          .Pagination{
            width: 1287/100rem;
            margin:34/100rem auto 117/100rem auto;
          }
          .summary{
            background: #FFFFFF;
            box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
            width: 1287/100rem;
            height:123/100rem;
            margin:0 auto 117/100rem auto;
            display: flex;
            align-items: center;
            .summarySelectBox{
              cursor: pointer;
              margin-left:42/100rem;
              width: 20/100rem;
              height:20/100rem;
              .itemSelect1{
                width:18/100rem ;
                height:18/100rem;
                border:1/100rem solid #BA0132;
                background: #BA0132;
                border-radius: 2/100rem;
                display: flex;
                justify-content: center;
                align-items: center;
                img{
                  width:16/100rem ;
                  height:12/100rem;
                }
              }
              .itemSelect2{
                width:18/100rem ;
                height:18/100rem;
                border:1/100rem solid #999999;
                background: #ffffff;
                border-radius: 2/100rem;
                display: flex;
                justify-content: center;
                align-items: center;
                img{
                  width:16/100rem ;
                  height:12/100rem;
                }
              }
              /*.summarySelect{*/
              /*margin-left:42/100rem;*/
              /*width: 20/100rem;*/
              /*height:20/100rem;*/
              /*display: flex;*/
              /*justify-content: center;*/
              /*align-items: center;*/
              /*img{*/
              /*width: 16/100rem;*/
              /*height:12/100rem;*/
              /*}*/
              /*}*/
            }

            .selectAll{
              width: 87/100rem;
              height:22/100rem;
              margin-left:22/100rem;
              font-family: PingFangSC-Regular;
              font-size: 16/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22/100rem;
            }
            .selected{
              width: 832/100rem;
              height:22/100rem;
              font-family: PingFangSC-Regular;
              font-size: 16/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22/100rem;
              span{
                color: #BA0132;
                margin-left:15/100rem;
                margin-right:3/100rem;
              }
            }
            .delete{
              width: 111/100rem;
              height:46/100rem;
              border: 1/100rem solid #999999;
              border-radius: 4/100rem;
              font-family: PingFangSC-Regular;
              font-size: 14/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: center;
              line-height:46/100rem;
              cursor: pointer;
            }
            .delete:hover{
              background: rgb(232,232,232);
            }
            .obtain{
              margin-left:23/100rem;
              width: 111/100rem;
              height:46/100rem;
              background: #BA0132;
              border-radius: 4/100rem;
              font-family: PingFangSC-Regular;
              font-size: 14/100rem;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: center;
              line-height:46/100rem;
              cursor: pointer;
            }
            .obtain:hover{
              background: #C1204A;
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
        position: relative;
        .search{
          width: 1287px;
          margin:0 auto;
        }
        .container{
          width: 1287px;
          margin:0 auto;
          background: #f9f9f9;
          .titleNav{
            height:50px;
            margin-left:40px;
            display: flex;
            align-items: flex-start;
            div{
              min-width: 80px;
              padding:0 10px;
              height:50px;
              line-height:28px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 20px;
              color: #333333;
              letter-spacing: 0;
              text-align: center;
              cursor: pointer;
              position: relative;
              span{
                position: absolute;
                bottom: 0;
                left:0;
                width: 100%;
                height:5px;
                background: #BA0132;

              }
            }
            div:nth-of-type(2){
              margin-left: 50px;
            }

          }
          .topNav{
            width: 1287px;
            height:76px;
            background: #ffffff;
            box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
            display: flex;
            div{
              height:76px;
              letter-spacing: 0;
              text-align: left;
              line-height:76px;
            }
            div:nth-of-type(1){
              width:52px;
              margin-left:40px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
            }
            div:nth-of-type(2){
              width: 103px;
              font-family: PingFangSC-Regular;
              font-size: 16px;
              color: #2A2A2A;
            }
            div:nth-of-type(3){
              margin-left:379px;
              width: 208px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              text-align: center;
            }
            div:nth-of-type(4){
              width:208px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              text-align: center;
            }
            div:nth-of-type(5){
              width: 208px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              text-align: center;
            }
            div:nth-of-type(6){
              width: 208px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              text-align: center;
            }
          }
          .emptyBox{
            margin-top:10px;
            width: 1287px;
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
          .itemBox{
            width: 1287px;
            background: #f9f9f9;
            margin-top:10px;
            padding-bottom: 10px;
            .requestItem{
              width: 1287px;
              height:136px;
              background: #ffffff;
              display: flex;
              align-items: center;
              margin-bottom: 15px;
              box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
              .leftBox{
                display: flex;
                width: 555px;
                /*flex: 1;*/
                align-items: center;

                .selectBox{
                  width: 20px;
                  height:20px;
                  margin-left:40px;
                  cursor: pointer;
                  .itemSelect1{
                    width:18px ;
                    height:18px;
                    border:1px solid #BA0132;
                    background: #BA0132;
                    border-radius: 2px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    img{
                      width:16px ;
                      height:12px;
                    }
                  }
                  .itemSelect2{
                    width:18px ;
                    height:18px;
                    border:1px solid #999999;
                    background: #ffffff;
                    border-radius: 2px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    img{
                      width:16px ;
                      height:12px;
                    }
                  }
                }
                .subselectBox{
                  width: 60px;
                  height:20px;
                }
                .itemimgUrl{
                  width: 160px;
                  height:90px;
                  margin-left:18px;
                  cursor: pointer;
                  overflow: hidden;
                  img{
                    width: 160px;
                    height:90px;
                    object-fit:cover;
                    float: left;
                  }
                }
                .itemSource{
                  width: 297px;
                  height:136px;
                  margin-left:16px;
                  div:nth-of-type(1){
                    cursor: pointer;
                    margin-top:33px;
                    width: 297px;
                    height:42px;
                    font-family: PingFangSC-Medium;font-weight:500;
                    font-size: 18px;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:25px;
                  }
                  div:nth-of-type(2){
                    width: 297px;
                    margin-top:4px;
                    font-family: PingFangSC-Regular;
                    font-size: 14px;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:20px;
                    span:nth-of-type(1){
                      color: #151515;
                      cursor: pointer;
                    }
                    span:nth-of-type(2){
                      margin-left:20px;
                    }
                  }
                }
              }

              .itemCategory{
                width: 208px;
                height:20px;
                font-family: PingFangSC-Regular;
                font-size: 14px;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                line-height:20px;
              }
              .itemNumber{
                width: 208px;
                height:20px;
                font-size: 14px;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                line-height:20px;
              }
              .validityPeriod{
                width: 208px;
                height:136px;
                font-family: PingFangSC-Regular;
                font-size: 14px;
                color: #666666;
                letter-spacing: 0;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                div{
                  display: block;
                  width: 170px;
                  text-align: center;
                  word-break: break-all;
                  word-wrap: break-word;
                  white-space: pre-wrap;
                }
              }
            }
          }
          .Pagination{
            width: 1287px;
            margin:34px auto 117px auto;
          }
          .summary{
            background: #FFFFFF;
            box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
            width: 1287px;
            height:123px;
            margin:0 auto 117px auto;
            display: flex;
            align-items: center;
            .summarySelectBox{
              cursor: pointer;
              margin-left:42px;
              width: 20px;
              height:20px;
              .itemSelect1{
                width:18px ;
                height:18px;
                border:1px solid #BA0132;
                background: #BA0132;
                border-radius: 2px;
                display: flex;
                justify-content: center;
                align-items: center;
                img{
                  width:16px ;
                  height:12px;
                }
              }
              .itemSelect2{
                width:18px ;
                height:18px;
                border:1px solid #999999;
                background: #ffffff;
                border-radius: 2px;
                display: flex;
                justify-content: center;
                align-items: center;
                img{
                  width:16px ;
                  height:12px;
                }
              }
              /*.summarySelect{*/
              /*margin-left:42px;*/
              /*width: 20px;*/
              /*height:20px;*/
              /*display: flex;*/
              /*justify-content: center;*/
              /*align-items: center;*/
              /*img{*/
              /*width: 16px;*/
              /*height:12px;*/
              /*}*/
              /*}*/
            }

            .selectAll{
              width: 87px;
              height:22px;
              margin-left:22px;
              font-family: PingFangSC-Regular;
              font-size: 16px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22px;
            }
            .selected{
              width: 832px;
              height:22px;
              font-family: PingFangSC-Regular;
              font-size: 16px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22px;
              span{
                color: #BA0132;
                margin-left:15px;
                margin-right:3px;
              }
            }
            .delete{
              width: 111px;
              height:46px;
              border: 1px solid #999999;
              border-radius: 4px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #333333;
              letter-spacing: 0;
              text-align: center;
              line-height:46px;
              cursor: pointer;
            }
            .delete:hover{
              background: rgb(232,232,232);
            }
            .obtain{
              margin-left:23px;
              width: 111px;
              height:46px;
              background: #BA0132;
              border-radius: 4px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: center;
              line-height:46px;
              cursor: pointer;
            }
            .obtain:hover{
              background: #C1204A;
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
