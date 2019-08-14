<template>
  <div class="main">
    <universalTop :indexSign="indexSign" ></universalTop>
    <div class="blank1" style=""></div>
    <div class="container">
      <div class="title">{{$t('notices.userAgreement')}}</div>
      <div class="conBox">
        <div class="boxLeft">

          <div @click="itemSwitch(index)" class="item" v-for="(item,index) in declarationList" :key="index">
            <div class="borderLeft"  v-if="item.isShow"></div>
            {{$t(item.titleL)}}
          </div>
        </div>
        <div class="boxRight">
          <div class="title">{{$t(title)}}</div>
          <div class="article">
            <div class="articleItem" v-for="(item,index) in article" :key="index">
              <div class="articleTitle">{{$t(item.titleL)}}</div>
              <div class="articlesubTitle">{{$t(item.subtitleL)}}</div>
              <div class="articleitem1" v-for="(item1,index1) in item.items" :key="index1">{{$t(item1)}}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="blank3" style="width: 100%;"></div>
    <universalBottom></universalBottom>
  </div>
</template>
<script>
  import universalTop from '../../components/universalTop'
  import universalBottom from '../../components/universalBottom'
  export default {
    components:{
      universalTop,
      universalBottom,
    },
    data(){
      return{
        indexSign:false,
        declarationList:[
          {
            isShow:true,
            title:this.$t('notices.disclaimer'),
            titleL:'notices.disclaimer',
            article:[
              {
                title:this.$t('userAgreement.agr1_row1_title'),
                titleL:'userAgreement.agr1_row1_title',
                subtitle:this.$t('userAgreement.agr1_row1_subtitle'),
                subtitleL:'userAgreement.agr1_row1_subtitle',
                items:[
                  'userAgreement.agr1_row1_arrItem1',
                ]
              },{
                title:this.$t('userAgreement.agr1_row2_title'),
                titleL:'userAgreement.agr1_row2_title',
                subtitle:this.$t('userAgreement.agr1_row2_subtitle'),
                subtitleL:'userAgreement.agr1_row2_subtitle',
                items:[
                  'userAgreement.agr1_row2_arrItem1',
                  'userAgreement.agr1_row2_arrItem2',
                  'userAgreement.agr1_row2_arrItem3',
                  'userAgreement.agr1_row2_arrItem4',
                ],
              },{
                title: this.$t('userAgreement.agr1_row3_title'),
                titleL:'userAgreement.agr1_row3_title',
                subtitle:'',
                items:[
                  'userAgreement.agr1_row3_arrItem1',
                  'userAgreement.agr1_row3_arrItem2',
                  'userAgreement.agr1_row3_arrItem3',
                  'userAgreement.agr1_row3_arrItem4',
                ],
              },{
                title: this.$t('userAgreement.agr1_row4_title'),
                titleL:'userAgreement.agr1_row4_title',
                subtitle:'',
                items:[
                  'userAgreement.agr1_row4_arrItem1',
                ]
              },{
                title: this.$t('userAgreement.agr1_row5_title'),
                titleL:'userAgreement.agr1_row5_title',
                subtitle: this.$t('userAgreement.agr1_row5_subtitle'),
                subtitleL:'userAgreement.agr1_row5_subtitle',
                items:['']
              },

            ]
          },          {
            isShow:false,
            title:this.$t('notices.copyrightNotice'),
            titleL:'notices.copyrightNotice',
            article:[
              {
                title:this.$t('userAgreement.agr2_row1_title'),
                titleL:'userAgreement.agr2_row1_title',
                subtitle:this.$t('userAgreement.agr2_row1_subtitle'),
                subtitleL:'userAgreement.agr2_row1_subtitle',
                items:[
                  this.$t('userAgreement.agr2_row1_arrItem1'),
                  'userAgreement.agr2_row1_arrItem1',

                ]
              },{
                title:this.$t('userAgreement.agr2_row2_title'),
                titleL:'userAgreement.agr2_row2_title',
                subtitle:'',
                items:[
                  'userAgreement.agr2_row2_arrItem1',
                  'userAgreement.agr2_row2_arrItem2',
                  'userAgreement.agr2_row2_arrItem3',
                  'userAgreement.agr2_row2_arrItem4',

                ]
              },{
                title:this.$t('userAgreement.agr2_row3_title'),
                titleL:'userAgreement.agr2_row3_title',
                subtitle:this.$t('userAgreement.agr2_row3_subtitle'),
                subtitleL:'userAgreement.agr2_row3_subtitle',
                items:['']
              },
            ],
          },          {
            isShow:false,
            title:this.$t('notices.privacyStatement'),
            titleL:'notices.privacyStatement',
            article:[
              {
                title:this.$t('userAgreement.agr3_row1_title'),
                titleL:'userAgreement.agr3_row1_title',
                subtitle:this.$t('userAgreement.agr3_row1_subtitle'),
                subtitleL:'userAgreement.agr3_row1_subtitle',
                items:[
                  'userAgreement.agr3_row1_arrItem1',
                ]
              },{
                title:this.$t('userAgreement.agr3_row2_title'),
                titleL:'userAgreement.agr3_row2_title',
                subtitle:this.$t('userAgreement.agr3_row2_subtitle'),
                subtitleL:'userAgreement.agr3_row2_subtitle',
                items:['']
              },{
                title:this.$t('userAgreement.agr3_row3_title'),
                titleL:'userAgreement.agr3_row3_title',
                subtitle:'',
                items:[
                  'userAgreement.agr3_row3_arrItem1',
                  'userAgreement.agr3_row3_arrItem2',
                  'userAgreement.agr3_row3_arrItem3',
                ]
              },{
                title:this.$t('userAgreement.agr3_row4_title'),
                titleL:'userAgreement.agr3_row4_title',
                subtitle:this.$t('userAgreement.agr3_row4_subtitle'),
                subtitleL:'userAgreement.agr3_row4_subtitle',
                items:[
                  'userAgreement.agr3_row4_arrItem1',
                  'userAgreement.agr3_row4_arrItem2',
                  'userAgreement.agr3_row4_arrItem3',
                  'userAgreement.agr3_row4_arrItem4',
                  'userAgreement.agr3_row4_arrItem5',
                  'userAgreement.agr3_row4_arrItem6',
                ]
              },
            ],
          }, {
            isShow:false,
            title:this.$t('notices.antiClaimStatement'),
            titleL:'notices.antiClaimStatement',
            article:[
              {
                title:this.$t('userAgreement.agr4_row1_title'),
                titleL:'userAgreement.agr4_row1_title',
                subtitle:this.$t('userAgreement.agr4_row1_subtitle'),
                subtitleL:'userAgreement.agr4_row1_subtitle',
                items:[
                  'userAgreement.agr4_row1_arrItem1',
                ]
              },{
                title:this.$t('userAgreement.agr4_row2_title'),
                titleL:'userAgreement.agr4_row2_title',
                subtitle:this.$t('userAgreement.agr4_row2_subtitle'),
                subtitleL:'userAgreement.agr4_row2_subtitle',
                items:['']
              },{
                title:this.$t('userAgreement.agr4_row3_title'),
                titleL:'userAgreement.agr4_row3_title',
                subtitle:'',
                items:[
                  'userAgreement.agr4_row3_arrItem1',
                  'userAgreement.agr4_row3_arrItem2',
                  'userAgreement.agr4_row3_arrItem3',
                  'userAgreement.agr4_row3_arrItem4',
                  'userAgreement.agr4_row3_arrItem5',
                ]
              },{
                title:'',
                subtitle:this.$t('userAgreement.agr4_row4_subtitle'),
                subtitleL:'userAgreement.agr4_row4_subtitle',
                items:[
                  'userAgreement.agr4_row4_arrItem1',
                ]
              },
            ],
          },
        ],
        title:'',
        article:[],
        index:0,
      }
    },
    computed:{

    },
    created(){
      this.title = this.declarationList[0].title
      this.article = this.declarationList[0].article
    },
    methods:{
      itemSwitch(index){
        if(index == this.index){
          return
        }else{
          this.title = this.declarationList[index].title
          this.article = this.declarationList[index].article
          this.index = index
          this.declarationList.forEach((item,index1)=>{
            if(index1 == index){
              item.isShow = true
            }else{
              item.isShow = false
            }

          })

        }
      }
    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .main{
        width: 100%;
        .container{
          width: 1276/100rem;
          background: #f9f9f9;
          margin:0 auto;
          .title{
            height:113/100rem;
            width: 100%;
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 30/100rem;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:113/100rem;
          }
          .conBox{
            width: 1276/100rem;
            display: flex;
            justify-content: space-between;
            .boxLeft{
              width: 295/100rem;
              background: #ffffff;
              box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
              position: relative;
              height:275/100rem;

              .item{
                position: relative;
                cursor: pointer;
                width: 295/100rem;
                height:68/100rem;
                border-bottom: 1/100rem solid #EEEEEE;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:68/100rem;
                text-indent: 21/100rem;
                .borderLeft{
                  position: absolute;
                  left:0;
                  top:0;
                  width: 3/100rem;
                  height:69/100rem;
                  background: #BA0132;
                  transition: 0.2s;
                }
              }
              .item:last-child{
                border:none;
              }
            }
            .boxRight{
              box-shadow: 0 0 12/100rem 0 rgba(223,223,223,0.50);
              width: 848/100rem;
              padding:54/100rem 57/100rem 50/100rem 53/100rem;
              background: #ffffff;
              .title{
                width: 848/100rem;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18/100rem;
                color: #333333;
                letter-spacing: 0;
                text-align: center;
                line-height:25/100rem;
                height:80/100rem;
              }
              .article{
                width: 848/100rem;
                min-height: 500/100rem;
                font-family: PingFangSC-Regular;
                font-size: 16/100rem;
                color: #666666;
                letter-spacing: 0;
                line-height:22/100rem;
                .articleItem:first-child{
                  margin-top: 0/100rem;
                }
                .articleItem{
                  margin-top: 40/100rem;
                  .articleTitle{
                    font-family: PingFangSC-Medium;
                    font-weight: 600;
                    font-size: 16/100rem;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 22/100rem;

                  }
                  .articlesubTitle{
                    margin-top:20/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 16/100rem;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 22/100rem;
                  }
                  .articleitem1{
                    margin-top:20/100rem;
                    font-family: PingFangSC-Regular;
                    font-size: 16/100rem;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 30/100rem;
                  }
                }

              }
            }
          }
        }
      }
      .blank1{
        height:72/100rem;
      }
      .blank3{
        height:140/100rem;
        /*background: pink;*/
      }
    }
    @media  (min-width:1301px){
      .main{
        width: 100%;
        .container{
          width: 1276px;
          background: #f9f9f9;
          margin:0 auto;
          .title{
            height:113px;
            width: 100%;
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 30px;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:113px;
          }
          .conBox{
            width: 1276px;
            display: flex;
            justify-content: space-between;
            .boxLeft{
              width: 295px;
              background: #ffffff;
              box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
              position: relative;
              height:275px;

              .item{
                position: relative;
                cursor: pointer;
                width: 295px;
                height:68px;
                border-bottom: 1px solid #EEEEEE;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18px;
                color: #333333;
                letter-spacing: 0;
                text-align: left;
                line-height:68px;
                text-indent: 21px;
                .borderLeft{
                  position: absolute;
                  left:0;
                  top:0;
                  width: 3px;
                  height:69px;
                  background: #BA0132;
                  transition: 0.2s;
                }
              }
              .item:last-child{
                border:none;
              }
            }
            .boxRight{
              box-shadow: 0 0 12px 0 rgba(223,223,223,0.50);
              width: 848px;
              padding:54px 57px 50px 53px;
              background: #ffffff;
              .title{
                width: 848px;
                font-family: PingFangSC-Medium;font-weight:500;
                font-size: 18px;
                color: #333333;
                letter-spacing: 0;
                text-align: center;
                line-height:25px;
                height:80px;
              }
              .article{
                width: 848px;
                min-height: 500px;
                font-family: PingFangSC-Regular;
                font-size: 16px;
                color: #666666;
                letter-spacing: 0;
                line-height:22px;
                .articleItem:first-child{
                  margin-top: 0px;
                }
                .articleItem{
                  margin-top: 40px;
                  .articleTitle{
                    font-family: PingFangSC-Medium;
                    font-weight: 600;
                    font-size: 16px;
                    color: #333333;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 22px;

                  }
                  .articlesubTitle{
                    margin-top:20px;
                    font-family: PingFangSC-Regular;
                    font-size: 16px;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 22px;
                  }
                  .articleitem1{
                    margin-top:20px;
                    font-family: PingFangSC-Regular;
                    font-size: 16px;
                    color: #666666;
                    letter-spacing: 0;
                    text-align: left;
                    line-height: 30px;
                  }
                }

              }
            }
          }
        }
      }
      .blank1{
        height:72px;
      }
      .blank3{
        height:140px;
        /*background: pink;*/
      }
    }
  }
</style>
