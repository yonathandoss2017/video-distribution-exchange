<template>
  <div class="mainBox" @click="mainClick" :style="indexSign == true?'background:transparent':'background:#ffffff;box-shadow: 0 2px 12px 0 rgba(211,211,211,0.50);'">
    <div class="main">
      <div class="topLeft">
        <div class="sign" @click="toIndex" style="cursor:pointer;">
          <div  v-if="indexSign"><img src="/static/img/topLogWhite.png" alt=""></div>
          <div v-else> <img  src="/static/img/topLogBlue.png" alt=""></div>
        </div>
        <div class="search" v-if="isSearchSHow" @click.stop="" :style="isTopSearchShow?'border: 1px solid #BA0132;':'border: 1px solid #ffffff;'">
          <input v-if="isTopSearchShow" v-model="searchInput" ref="inputVal" type="text"/>
          <div v-else class="inputAlternative"></div>
          <div class="searchIcon" @click.stop="searchClick" @mouseover="mouseover" @mouseleave="mouseLeave"><img :src="searchIcon" alt=""></div>
        </div>
        <div v-else class="searchAlternatives"></div>
      </div>

      <div class="topRight">
        <div class="shopingCar" @click="toRequestList" style="cursor: pointer">
          <div class="carIcon" v-if="indexSign == false" @mouseover="mouseoverCar" @mouseleave="mouseLeaveCar"><img  :src="shopCarIcon" alt=""></div>
          <div class="carIcon" v-else><img  src="/static/img/shopingCar.png" alt=""></div>
          <div class="carNumber" v-if="purchaseQuantity&&purchaseQuantity!=0">{{purchaseQuantity}}</div>
        </div>
        <div class="lanageSelection" @click.stop="lanageClick" style="cursor:pointer">
          <div class="flag">
            <div v-if="ischinese"><img src="/static/img/chinese.png" alt=""></div>
            <div v-else><img src="/static/img/english.png" alt=""></div>
          </div>
          <div id="lanage" class="lanage" :style="indexSign == true?'color:#ffffff':'color:#666666'">
            <span v-if="ischinese">中文</span>
            <span v-else>English</span>
          </div>
          <div v-if="isLanageShow" class="lanageArrow">
            <img v-if="indexSign" src="/static/img/CombinedShapeUp.png" alt="">
            <img v-else src="/static/icon/upward.png" alt="">
          </div>
          <div v-else class="lanageArrow">
            <img v-if="indexSign" src="/static/img/CombinedShapeDown.png" alt="">
            <img v-else src="/static/icon/down.png" alt="">
          </div>
          <div class="lanageDropDown" v-if="isLanageShow">
            <div class="redborder" style="background: #BA0132;"></div>
            <div class="DropDownItem" v-for="(item,index) in lanageDropDownList" :key="index" @click="changeLanage(item.name)">
              {{item.name}}
            </div>
          </div>
        </div>
        <div class="account" style="cursor:pointer">
          <div class="accountText" @click.stop="accountClick" :style="indexSign == true?'color:#ffffff':'color:#666666'">
            <span class="div">
              {{$t('global.account')}}
              <div class="DropDownBox" v-if="isDropDownBoxShow">
                <div class="redborder" style="background: #BA0132;"></div>
                <div class="DropDownItem" @click="myClick(index)" v-for="(item,index) in accountDropDownList" :key="index">
                  {{$t(item.lanage)}}
                </div>
              </div>
            </span>

          </div>
          <div v-if="isDropDownBoxShow == false"  @click.stop="accountClick"  class="accontImg">
            <img v-if="indexSign" src="/static/img/CombinedShapeDown.png" alt="">
            <img v-else src="/static/icon/down.png" alt="">
          </div>
          <div v-else class="accontImg" @click.stop="accountClick">
            <img v-if="indexSign" src="/static/img/CombinedShapeUp.png" alt="">
            <img v-else src="/static/icon/upward.png" alt="">
          </div>

        </div>
      </div>


    </div>
  </div>

</template>
<script>
  import {  setLocale,Car } from '@/utils/global/axios.js'
  export default {
    inject:['reload'],
    data(){
      return {
        searchIcon:'/static/icon/topSearchBlack.png',
        shopCarIcon:'/static/icon/shopCarBlick.png',
        isTopSearchShow:false,
        isDropDownBoxShow:false,
        isLanageShow:false,
        searchInput:'',
        accountDropDownList:[
          {
            name:this.$t('global.content_management'),
            lanage:'global.content_management'
          },{
            name:this.$t('global.subscription'),
            lanage:'global.subscription',
          },{
            name:this.$t('global.account_infor'),
            lanage:'global.account_infor',
          },{
            name:this.$t('global.drop_out'),
            lanage:'global.drop_out',
          },
        ],
        lanageDropDownList:[
          {
            name:'中文'
          },
          {
            name:'English'
          },
        ]
      }
    },

    props: {
      indexSign:{
        type: Boolean,
        default(){
          return true
        }
      },
      isSearchSHow:{
        type: Boolean,
        default(){
          return true
        }
      },
      backStyle:{
        type: String,
        default(){
          return 'background:transparent'
        }
      },
    },
    watch:{
      clickAll(item){
        if(this.clickAll){
          this.isDropDownBoxShow = false
          this.isLanageShow = false
        }
      },
    },
    computed:{
      backgroundColoe:function () {
        return indexSign == true? 'background:transparent':'background:#ffffff'
      },
      ischinese:function () {
        return this.$i18n.locale == 'zh'?  true : false
      },
      purchaseQuantity(){
        return this.$store.state.purchaseQuantity
      },
    },
    created(){
    },
    mounted(){
      document.querySelector('body').addEventListener('click', this.mainClick);
      if(this.purchaseQuantity == null){
        this.getThePurchaseQuantity()
      }
    },
    updated(){
    },
    beforeDestroy(){
      document.querySelector('body').removeEventListener('click', this.mainClick);
    },
    methods:{
      //获取购物车内列表个数
      getThePurchaseQuantity(){
        let params = ''
        Car(params).then(res=>{
          if(res.data.status=='success'){
            let number  = res.data.data.length
            this.$store.commit('newPurchaseQuantity',number)
          }
        }).catch(err=>{
        })
      },
      mouseover(){
        this.searchIcon = '/static/icon/topSearchRed.png'
      },
      mouseLeave(){
        if(this.isTopSearchShow){
          this.searchIcon = '/static/icon/topSearchRed.png'
        }else{
          this.searchIcon = '/static/icon/topSearchBlack.png'
        }

      },
      mouseoverCar(){
        this.shopCarIcon = '/static/icon/shopCarRed.png'
      },
      mouseLeaveCar(){
        this.shopCarIcon = '/static/icon/shopCarBlick.png'
      },
      searchBoxClick(){
        if(this.isTopSearchShow == false){
          this.isTopSearchShow = true
        }
      },
      searchClick(){
        if(this.isTopSearchShow == false){
          this.isTopSearchShow = true
          this.$nextTick(function () {
            //DOM 更新了
            this.$refs.inputVal.focus()
          })
        }else{
          if(this.searchInput!=''){
            let keyWords = this.searchInput
            this.$store.commit('newTypeId',0)
            this.$store.commit('newTypeStyle',0)
            this.$router.push({name: 'allContentCategories',query:{keyWoeds:keyWords}})
          }
        }
      },
      mainClick(){
        let that = this
        this.isDropDownBoxShow = false
        this.isLanageShow = false
        this.isTopSearchShow = false
        setTimeout(function () {
          that.searchIcon = '/static/icon/topSearchBlack.png'
        },300)

      },
      accountClick(){
        this.isDropDownBoxShow = !this.isDropDownBoxShow
        if(this.isDropDownBoxShow){
          this.isLanageShow = false
        }
      },
      lanageClick(){
        this.isLanageShow = !this.isLanageShow
        if(this.isLanageShow){
          this.isDropDownBoxShow = false
        }
      },
      changeLanage(lanage){
        let that = this
        if(lanage == '中文'){
          this.$i18n.locale = 'zh'
          let lanage = 'zh'
          this.changeLocale(lanage)
        }else{
          this.$i18n.locale = 'en'
          this.$store.commit('newLanage','en')
          let lanage = 'en'
          this.changeLocale(lanage)
        }
      },
      toRequestList(){
        this.$router.push({name: 'requestList'})
      },
      myClick(index){
        if(index == 1){
          this.$router.push({name: 'mySubscription'})
        } else if (index == 0) {
          window.location.href="/manage"; 
        } else if (index == 2) {
          window.location.href="/manage/profile"; 
        } else if (index == 3) {
          window.location.href="/logout"; 
        }

      },
      toIndex(){
        this.$router.push({name: 'index'})
      },
      changeLocale(lanage){
        let that = this
        let params = '/'+lanage
        setLocale(params).then(res=>{
          if(res.data.status == 'success'){
            that.$store.commit('newLanage',lanage)
          }
        }).catch(err=>{

        })
      },
    }
  }
</script>
<style scoped lang="less">



  @media screen {
    @media  (max-width:1300px){
      .mainBox{
        z-index:200;
        width: 100%;
        height:76/100rem;
        position: fixed;
        top:0;

        /*background:gray;*/
      }

      .main{
        margin:0 auto;
        max-width: 1306/100rem;
        height:76/100rem;
        padding-left:6/100rem;
        display: flex;
        justify-content:space-between;
        /*background: pink;*/
        align-items: center;
        .topLeft{
          width: 786/100rem;
          height:76/100rem;
          display: flex;
          justify-content:space-between;
          /*background: pink;*/
          align-items: center;
          .sign{
            width: 148/100rem;
            height:30/100rem;
            div{
              width: 148/100rem;
              height:30/100rem;
              img{
                width: 100%;
                height:100%;
                float: left;
              }
            }
          }
          .searchAlternatives{
            width: 321/100rem;
            height:41/100rem;
          }
          .search{
            width: 321/100rem;
            height:41/100rem;
            border-radius: 4/100rem;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            -webkit-transition: 0.4s linear;
            input{
              width: 280/100rem;
              height:39/100rem;
              border: none;
              outline:medium;
              border-radius: 4/100rem;
              text-indent: 15/100rem;
            }
            .inputAlternative{
              width: 280/100rem;
              height:39/100rem;
            }
            .searchIcon{
              cursor: pointer;
              width: 41/100rem;
              height:41/100rem;
              display: flex;
              justify-content: center;
              align-items: center;
              img{
                width: 25/100rem;
                height:24/100rem;
              }
            }
          }
        }

        .topRight{
          width: 378/100rem;
          /*background: yellow;*/
          margin-right:80/100rem;
          display: flex;
          align-items: center;
          .shopingCar{
            width: 29/100rem;
            height:24/100rem;
            position: relative;
            .carIcon{
              width: 29/100rem;
              height:24/100rem;
              img{
                width:100%;
                height:100%;
                float: left;
              }
            }
            .carNumber{
              position: absolute;
              top:-8/100rem;
              right:-8/100rem;
              width: 18/100rem;
              height:18/100rem;
              border-radius: 18/100rem;
              background: #BA0132;
              font-family: PingFangSC-Medium;
              font-weight: 500;
              font-size: 12/100rem;
              color: #FFFFFF;
              text-align: center;
              line-height:18/100rem;
            }
          }
          .lanageSelection{
            margin-left:112/100rem;
            /*width:0.73rem;*/
            height:25/100rem;
            display: flex;
            /*background: pink;*/
            position: relative;
            /*justify-content: space-between;*/
            .lanageDropDown{
              position: absolute;
              left:0;
              top:30/100rem;
              width: 123/100rem;
              background: #FFFFFF;
              border-radius: 2/100rem;
              .DropDownItem:hover{
                background: #F6F6F6;
              }
              .redborder{
                width: 123/100rem;
                height: 4/100rem;
              }
              .DropDownItem{
                width: 123/100rem;
                height:48/100rem;
                font-family: PingFangSC-Regular;
                font-size: 14/100rem;
                color: #666666;
                letter-spacing: 0;
                text-align: left;
                line-height:48/100rem;
                text-indent: 22/100rem;
              }
            }
            .flag{
              width:25/100rem;
              height:25/100rem;
              div{
                width:25/100rem;
                height:25/100rem;
                img{
                  width: 100%;
                  height:100%;
                  float: left;
                }
              }
            }
            .lanage{
              margin-left:7/100rem;
              /*text-align: center;*/
              text-align: center;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14/100rem;
              color: #ffffff;
              line-height:25/100rem;
              /*background: pink;*/
            }
            .lanageArrow{
              /*background: yellow;*/
              margin-left:8/100rem;
              margin-top: 8/100rem;
              width: 12/100rem;
              height:12/100rem;
              display: flex;
              justify-content:center;
              align-items: flex-start;
              img{
                width:auto;
                height:auto;
                float: left;
              }
            }
          }
          .account{
            /*background: pink;*/
            position: relative;
            width:141/100rem;
            height:20/100rem;
            display: flex;
            justify-content: space-between;
            /*background: pink;*/
            /*background: pink;*/

            .accountText{
              width:121/100rem;
              height:20/100rem;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14/100rem;
              color: #ffffff;
              line-height:20/100rem;
              text-align: right;
              position: relative;
              .div{
                position: relative;
                .DropDownBox{
                  position: absolute;
                  left:0;
                  top:28/100rem;
                  background: #FFFFFF;
                  border-radius: 2/100rem;
                  .redborder{
                    width: 100%;
                    height: 4/100rem;
                  }
                  .DropDownItem:hover{
                    background: #F6F6F6;
                  }
                  .DropDownItem{
                    min-width: 103/100rem;
                    padding:0 20/100rem 0 20/100rem;
                    height:48/100rem;
                    white-space:nowrap;
                    font-family: PingFangSC-Regular;
                    font-size: 14/100rem;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:48/100rem;
                    /*text-indent: 22/100rem;*/
                  }
                }
              }
            }
            .accontImg{
              width:12/100rem;
              height:12/100rem;
              /*margin-top:5/100rem;*/
              /*background: red;*/
              margin-left:8/100rem;
              margin-top: 6/100rem;
              display: flex;
              justify-content: center;
              align-items: flex-start;
              img{
                width:auto;
                height:auto;
                float: left;
              }
            }
          }

        }
      }

    }
    @media  (min-width:1301px){
      .mainBox{
        z-index:200;
        width: 100%;
        height:76px;
        position: fixed;
        top:0;

        /*background:gray;*/
      }

      .main{
        margin:0 auto;
        max-width: 1306px;
        height:76px;
        padding-left:6px;
        display: flex;
        justify-content:space-between;
        /*background: pink;*/
        align-items: center;
        .topLeft{
          width: 786px;
          height:76px;
          display: flex;
          justify-content:space-between;
          /*background: pink;*/
          align-items: center;
          .sign{
            width: 148px;
            height:30px;
            div{
              width: 148px;
              height:30px;
              img{
                width: 100%;
                height:100%;
                float: left;
              }
            }
          }
          .searchAlternatives{
            width: 321px;
            height:41px;
          }
          .search{
            width: 321px;
            height:41px;
            border-radius: 4px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            -webkit-transition: 0.4s linear;
            input{
              width: 280px;
              height:39px;
              border: none;
              outline:medium;
              border-radius: 4px;
              text-indent: 15px;
            }
            .inputAlternative{
              width: 280px;
              height:39px;
            }
            .searchIcon{
              cursor: pointer;
              width: 41px;
              height:41px;
              display: flex;
              justify-content: center;
              align-items: center;
              img{
                width: 25px;
                height:24px;
              }
            }
          }
        }

        .topRight{
          width: 378px;
          /*background: yellow;*/
          margin-right:80px;
          display: flex;
          align-items: center;
          .shopingCar{
            width: 29px;
            height:24px;
            position: relative;
            .carIcon{
              width: 29px;
              height:24px;
              img{
                width:100%;
                height:100%;
                float: left;
              }
            }
            .carNumber{
              position: absolute;
              top:-8px;
              right:-8px;
              width: 18px;
              height:18px;
              border-radius: 18px;
              background: #BA0132;
              font-family: PingFangSC-Medium;
              font-weight: 500;
              font-size: 12px;
              color: #FFFFFF;
              text-align: center;
              line-height:18px;
            }
          }
          .lanageSelection{
            margin-left:112px;
            /*width:0.73rem;*/
            height:25px;
            display: flex;
            /*background: pink;*/
            position: relative;
            /*justify-content: space-between;*/
            .lanageDropDown{
              position: absolute;
              left:0;
              top:30px;
              width: 123px;
              background: #FFFFFF;
              border-radius: 2px;
              .DropDownItem:hover{
                background: #F6F6F6;
              }
              .redborder{
                width: 123px;
                height: 4px;
              }
              .DropDownItem{
                width: 123px;
                height:48px;
                font-family: PingFangSC-Regular;
                font-size: 14px;
                color: #666666;
                letter-spacing: 0;
                text-align: left;
                line-height:48px;
                text-indent: 22px;
              }
            }
            .flag{
              width:25px;
              height:25px;
              div{
                width:25px;
                height:25px;
                img{
                  width: 100%;
                  height:100%;
                  float: left;
                }
              }
            }
            .lanage{
              margin-left:7px;
              /*text-align: center;*/
              text-align: center;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14px;
              color: #ffffff;
              line-height:25px;
              /*background: pink;*/
            }
            .lanageArrow{
              /*background: yellow;*/
              margin-left:8px;
              margin-top: 8px;
              width: 12px;
              height:12px;
              display: flex;
              justify-content:center;
              align-items: flex-start;
              img{
                width:auto;
                height:auto;
                float: left;
              }
            }
          }
          .account{
            /*background: pink;*/
            position: relative;
            width:141px;
            height:20px;
            display: flex;
            justify-content: space-between;
            /*background: pink;*/
            /*background: pink;*/

            .accountText{
              width:121px;
              height:20px;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14px;
              color: #ffffff;
              line-height:20px;
              text-align: right;
              position: relative;
              .div{
                position: relative;
                .DropDownBox{
                  position: absolute;
                  left:0;
                  top:28px;
                  background: #FFFFFF;
                  border-radius: 2px;
                  .redborder{
                    width: 100%;
                    height: 4px;
                  }
                  .DropDownItem:hover{
                    background: #F6F6F6;
                  }
                  .DropDownItem{
                    min-width: 103px;
                    padding:0 20px 0 20px;
                    height:48px;
                    white-space:nowrap;
                    font-family: PingFangSC-Regular;
                    font-size: 14px;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height:48px;
                  }
                }
              }
            }
            .accontImg{
              width:12px;
              height:12px;
              /*margin-top:5px;*/
              /*background: red;*/
              margin-left:8px;
              margin-top: 6px;
              display: flex;
              justify-content: center;
              align-items: flex-start;
              img{
                width:auto;
                height:auto;
                float: left;
              }
            }
          }

        }
      }
    }
  }
</style>
