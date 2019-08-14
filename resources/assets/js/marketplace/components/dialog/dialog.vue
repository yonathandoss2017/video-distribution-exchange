<template>
  <div class="mainBox"  v-if="isShow">
    <div class="maincon" >
      <div class="container">
        <div class="title row1">
          <div>{{$t('requestList.RequestInformation')}}</div>
          <div @click="closeDialog"><img src="/static/icon/closed.png" alt=""></div>
        </div>
        <div class="row2">{{$t('requestList.Requester')}}</div>
        <div class="row3">{{user}}</div>
        <div class="row4">{{$t('requestList.request')}}</div>
        <div class="row5" @click.stop="openPull">
          <div class="spSelentClass" id="spSelentClass" :style="spLength>4?'overflow-y: scroll;':''">
            <div v-if="spLength>0" class="selectCon" :style="'background: #D8D8D8'" v-for="(item,index) in spSelectedList" :key="index">
              <div class="selectContext"  >{{item.name}}</div>
              <div class="selectConImg" @click.stop="emptySelect(item.id)"><img src="/static/icon/close.png" alt=""></div>
            </div>
            <div v-if="spLength==0||spLength==null" class="selectContext"  >{{defaultContent}}</div>
          </div>
          <div class="isOpen" v-if="!isOpen"><img src="/static/icon/down.png" alt=""></div>
          <div class="isOpen" v-else><img src="/static/icon/upward.png" alt=""></div>
        </div>
        <div class="row6">
          <div>{{$t('requestList.theme')}}</div>
          <div class="dropBox" v-if="isOpen">
            <div class="item" @click.stop="selectItem(index)" v-for="(item,index) in spList" :key="index">{{item.name}}</div>
          </div>
        </div>
        <input v-model="theme" class="row7" type="text">
        <div class="row8">{{$t('requestList.information')}}</div>
        <textarea v-model="info" class="row9" name="" id="" cols="30" rows="10"></textarea>
        <button  :style="submitBag" class="row10" @click="obtionSubmit" >{{$t('requestList.submit')}}</button>
      </div>

    </div>
  </div>
</template>
<script>
  import {  getSPuser} from '@/utils/global/axios.js'
  export default {
    data(){
      return{
        isSubmit:false,
        isOpen:false,
        user:'CNC<admin@cnc.com>',
        defaultContent:this.$t('requestList.PleaseSelectSpUser'),
        selectCon:'',
        theme:'',
        info:'',
        sp_id:'',
        spSelectedList:[],
        spList:[],
        userList:[],
        spLength:null,
      }
    },
    props: {
      isShow:{
        type: Boolean,
        default(){
          return false
        }
      },
    },
    computed:{
      submitBag(){
        if(this.spLength>0 && this.info!='' && this.theme!=''){
          return 'background:#BA0132;cursor: pointer'
        }else{
          return 'background: #D8D8D8;cursor: not-allowed;'
        }
      }
    },
    mounted(){
      document.querySelector('body').addEventListener('click', this.mainClick);
    },
    updated(){
    },
    beforeDestroy(){
      document.querySelector('body').removeEventListener('click', this.mainClick);
    },
    watch:{
      submitBag(item){
        if(item == 'background:#BA0132;cursor: pointer'){
          this.isSubmit = true
        }else{
          this.isSubmit = false
        }
      },
      spSelectedList(){
        this.spLength = this.spSelectedList.length
      },
      spLength(){
        this.$nextTick(() => {
          var div = document.getElementById('spSelentClass')
          if(div!=null){
            if(div.scrollTop !=null){
              div.scrollTop = div.scrollHeight
            }
          }

        })
      },
      isShow(){
        this.spSelectedList = []
        this.theme = ''
        this.info = ''
        this.spList.forEach((item2,index2)=>{
          item2.selected = false
        })
      },
    },

    created(){
      this.getSPList()
    },
    methods:{


      mainClick(){
        this.isOpen = false
      },
      getSPList(){
        let params = ''
        getSPuser(params).then(res=>{
          let userList = res.data.data
          userList.forEach((item,index)=>{
            item.isSelected = false
          })
          this.spList = userList
        }).catch(err=>{

        })
      },
      emptySelect(index){
        this.spSelectedList.forEach((item1,index1)=>{
          if(item1.id == index){
            this.spSelectedList.splice(index1,1)
          }
        })
        this.spList.forEach((item2,index2)=>{
          if(item2.id == index){
            item2.selected = false
          }
        })
      },
      openPull(){
        this.isOpen = !this.isOpen
      },
      selectItem(index){
        if(this.spList[index].selected != true){
          this.spSelectedList.push(this.spList[index])
          this.spList[index].selected = true
        }
        this.isOpen = true
      },
      closeDialog(){
        this.$emit('closeDialog')
      },
      obtionSubmit(){
        if(this.isSubmit = true){
          let arr = []
          this.spSelectedList.forEach((item,index)=>{
            arr.push(item.id)
          })
          let sp_ids = arr.join(',')
          let obtionInfo = {
            theme:this.theme,
            info:this.info,
            sp_ids:sp_ids
          }
          this.$emit('obtionSubmit',obtionInfo)
        }

      },
    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .mainBox{
        z-index: 200;
        position: fixed;
        top:0;
        left:0;
        background: rgba(0,0,0,0.20);
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        .maincon{
          padding:40/100rem 70/100rem 40/100rem 70/100rem;
          width: 788/100rem;
          background: #FFFFFF;
          box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
          position: relative;
          .container{
            width: 788/100rem;
            .row1{
              width: 788/100rem;
              height:42/100rem;
              display: flex;
              justify-content: space-between;
              div:nth-of-type(1){
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 30/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:42/100rem;
              }
              div:nth-of-type(2){
                width: 42/100rem;
                height:42/100rem;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                img{
                  width: 20/100rem;
                  height:20/100rem;
                }
              }
            }
            .row2{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22/100rem;
              margin-top:41/100rem;
            }
            .row3{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 18/100rem;
              color: #666666;
              letter-spacing: 0;
              text-align: left;
              line-height:25/100rem;
              margin-top:11/100rem;
            }
            .row4{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22/100rem;
              margin-top:18/100rem;
            }
            .row5{
              position: relative;
              margin-top:14/100rem;
              width: 411/100rem;
              min-height:46/100rem;
              border: 1/100rem solid #DDDDDD;
              display: flex;
              justify-content: space-between;
              align-items: center;

              .spSelentClass{
                width: 365/100rem;
                min-height:46/100rem;
                max-height: 80/100rem;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                .selectContext{
                  font-family: PingFangSC-Medium;
                  font-weight:500;
                  font-size: 14/100rem;
                  color: #999999;
                  letter-spacing: 0;
                  text-align: left;
                  padding: 0 0 0 10/100rem;
                  line-height:23/100rem;
                  display: flex;
                  align-items: center;
                }
                .selectCon{
                  height:23/100rem;
                  display: flex;
                  background: #D8D8D8;
                  border-radius: 6/100rem;
                  margin: 5/100rem 0 5/100rem 11/100rem;

                  .selectContext{
                    font-family: PingFangSC-Medium;
                    font-weight:500;
                    font-size: 14/100rem;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    padding: 0 0 0 10/100rem;
                    line-height:23/100rem;
                    display: flex;
                    align-items: center;
                  }
                  .selectConImg{
                    margin-left:7/100rem;
                    margin-right:7/100rem;
                    width: 23/100rem;
                    height:23/100rem;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    img{
                      width: 11/100rem;
                      height:11/100rem;
                    }
                  }

                }
              }

              .isOpen{
                width: 46/100rem;
                height:46/100rem;
                display: flex;
                justify-content: center;
                align-items: center;
                img{
                  width: 9/100rem;
                  height:5/100rem;
                }
              }
            }
            .row6{
              font-family: PingFangSC-Medium;
              font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22/100rem;
              margin-top:19/100rem;
              display: block;
              position: relative;
              .dropBox{
                position: absolute;
                top:-5/100rem;
                left:0;
                width: 390/100rem;
                height: 320/100rem;
                overflow-y: scroll;
                padding:11/100rem;
                background: #f9f9f9;
                box-shadow: 0 0 12/100rem 0 rgba(217,217,217,0.50);
                .item{
                  width: 380/100rem;
                  height:42/100rem;
                  border-radius: 2/100rem;
                  background: #f9f9f9;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 16/100rem;
                  color:#333333;;
                  letter-spacing: 0;
                  text-align: left;
                  text-indent: 15/100rem;
                  line-height:42/100rem;
                  cursor: pointer;
                }
                .item:hover{
                  background: #BA0132;
                  color: #ffffff;
                }
              }
            }
            .row7{
              margin-top:13/100rem;
              width: 768/100rem;
              padding:0 10/100rem;
              height:46/100rem;
              border: 1/100rem solid #DDDDDD;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14/100rem;
              color: #999999;
              letter-spacing: 0;
              text-align: left;
              display: block;
              /*text-indent: 25/100rem;*/
              /*line-height:25/100rem;*/
            }
            .row8{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16/100rem;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22/100rem;
              margin-top:19/100rem;
            }
            .row9{
              margin-top:13/100rem;
              width: 768/100rem;
              padding:10/100rem;
              height:120/100rem;
              border: 1/100rem solid #DDDDDD;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14/100rem;
              color: #999999;
              letter-spacing: 0;
              text-align: left;
              /*text-indent: 25/100rem;*/
              padding-top:10/100rem;
              line-height:25/100rem;
              display: block;
            }
            .row10{
              display: block;
              border:none;
              outline: none;
              margin-top:20/100rem;
              width: 111/100rem;
              height:46/100rem;
              border-radius: 4/100rem;
              font-family: PingFangSC-Regular;
              font-size: 14/100rem;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: center;
              line-height:46/100rem;
              background: #D8D8D8;

            }
          }
        }
      }
    }
    @media  (min-width:1301px){
      .mainBox{
        z-index: 200;
        position: fixed;
        top:0;
        left:0;
        background: rgba(0,0,0,0.20);
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        .maincon{
          padding:50px 70px 40px 70px;
          width: 788px;
          background: #FFFFFF;
          box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
          position: relative;
          .container{
            width: 788px;
            .row1{
              width: 788px;
              height:42px;
              display: flex;
              justify-content: space-between;
              div:nth-of-type(1){
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 30px;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:42px;
              }
              div:nth-of-type(2){
                width: 42px;
                height:42px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                img{
                  width: 20px;
                  height:20px;
                }
              }
            }
            .row2{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22px;
              margin-top:41px;
            }
            .row3{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 18px;
              color: #666666;
              letter-spacing: 0;
              text-align: left;
              line-height:25px;
              margin-top:11px;
            }
            .row4{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22px;
              margin-top:18px;
            }
            .row5{
              position: relative;
              margin-top:14px;
              width: 411px;
              min-height:46px;
              border: 1px solid #DDDDDD;
              display: flex;
              justify-content: space-between;
              align-items: center;

              .spSelentClass{
                width: 365px;
                min-height:46px;
                max-height: 80px;
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                .selectContext{
                  font-family: PingFangSC-Medium;
                  font-weight:500;
                  font-size: 14px;
                  color: #999999;
                  letter-spacing: 0;
                  text-align: left;
                  padding: 0 0 0 10px;
                  line-height:23px;
                  display: flex;
                  align-items: center;
                }
                .selectCon{
                  height:23px;
                  display: flex;
                  background: #D8D8D8;
                  border-radius: 6px;
                  margin: 5px 0 5px 11px;

                  .selectContext{
                    font-family: PingFangSC-Medium;
                    font-weight:500;
                    font-size: 14px;
                    color: #999999;
                    letter-spacing: 0;
                    text-align: left;
                    padding: 0 0 0 10px;
                    line-height:23px;
                    display: flex;
                    align-items: center;
                  }
                  .selectConImg{
                    margin-left:7px;
                    margin-right:7px;
                    width: 23px;
                    height:23px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    img{
                      width: 11px;
                      height:11px;
                    }
                  }

                }
              }

              .isOpen{
                width: 46px;
                height:46px;
                display: flex;
                justify-content: center;
                align-items: center;
                img{
                  width: 9px;
                  height:5px;
                }
              }
            }
            .row6{
              font-family: PingFangSC-Medium;
              font-weight:500;
              font-size: 16px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22px;
              margin-top:19px;
              display: block;
              position: relative;
              .dropBox{
                position: absolute;
                top:-5px;
                left:0;
                width: 390px;
                height: 320px;
                overflow-y: scroll;
                padding:11px;
                background: #f9f9f9;
                box-shadow: 0 0 12px 0 rgba(217,217,217,0.50);
                .item{
                  width: 380px;
                  height:42px;
                  border-radius: 2px;
                  background: #f9f9f9;
                  font-family: PingFangSC-Medium;font-weight:500;
                  font-size: 16px;
                  color:#333333;;
                  letter-spacing: 0;
                  text-align: left;
                  text-indent: 15px;
                  line-height:42px;
                  cursor: pointer;
                }
                .item:hover{
                  background: #BA0132;
                  color: #ffffff;
                }
              }
            }
            .row7{
              margin-top:13px;
              width: 768px;
              padding:0 10px;
              height:46px;
              border: 1px solid #DDDDDD;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14px;
              color: #999999;
              letter-spacing: 0;
              text-align: left;
              display: block;
              /*text-indent: 25px;*/
              /*line-height:25px;*/
            }
            .row8{
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 16px;
              color: #333333;
              letter-spacing: 0;
              text-align: left;
              line-height:22px;
              margin-top:19px;
            }
            .row9{
              margin-top:13px;
              width: 768px;
              padding:10px;
              height:140px;
              border: 1px solid #DDDDDD;
              font-family: PingFangSC-Medium;font-weight:500;
              font-size: 14px;
              color: #999999;
              letter-spacing: 0;
              text-align: left;
              /*text-indent: 25px;*/
              padding-top:10px;
              line-height:25px;
              display: block;
            }
            .row10{
              display: block;
              border:none;
              outline: none;
              margin-top:20px;
              width: 111px;
              height:46px;
              border-radius: 4px;
              font-family: PingFangSC-Regular;
              font-size: 14px;
              color: #FFFFFF;
              letter-spacing: 0;
              text-align: center;
              line-height:46px;
              background: #D8D8D8;

            }
          }
        }
      }
    }
  }

</style>
