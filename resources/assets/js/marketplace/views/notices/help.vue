<template>
  <div class="main">
    <universalTop :indexSign="indexSign" ></universalTop>
    <div class="blank1" style=""></div>
    <div class="container">
      <div class="title">{{$t('notices.usingHelp')}}</div>
      <div class="conBox">
        <div class="boxLeft">
          <div @click="itemSwitch(index)" class="item" v-for="(item,index) in declarationList1" :key="index">
            <div class="borderLeft" v-if="item.isShow"></div>
            {{$t(item.titleL)}}
          </div>
        </div>
        <div class="boxRight">
          <div class="title">{{title}}</div>
          <div class="article">
            <div v-for="(item,index) in article" :key="index">
              <div class="articleTitle">{{$t(item.titleL)}}</div>
              <div v-for="(item1,index1) in item.article" :key="index1">
                <div class="articleSubtitle">{{$t(item1.subTitleL)}}</div>
              </div>
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
        title:'',
        article:'',
        index:0,
        declarationList1:[
          {
            title:this.$t('notices.AaccountRegistrationAndLogin'),
            titleL:'notices.AaccountRegistrationAndLogin',
            subTitle:'',
            isShow:true,
            article:[
              {
                title:this.$t('help.use1_row1_title'),
                titleL:'help.use1_row1_title',
                article:[
                  {
                    subTitle:this.$t('help.use1_row1_subTitle'),
                    subTitleL:'help.use1_row1_subTitle',
                  }
                ]
              },
              {
                title:this.$t('help.use1_row2__title'),
                titleL:'help.use1_row2__title',
                article:[
                  {
                    subTitle:this.$t('help.use1_row2_row1_subTitle'),
                    subTitleL:'help.use1_row2_row1_subTitle',
                  },
                  {
                    subTitle:this.$t('help.use1_row2_row2_subTitle'),
                    subTitleL:'help.use1_row2_row2_subTitle',
                  },
                  {
                    subTitle:this.$t('help.use1_row2_row3_subTitle'),
                    subTitleL:'help.use1_row2_row3_subTitle',
                  }
                ]

              }
            ],
          },
          {
            title:this.$t('notices.OrganizationAccountSettings'),
            titleL:'notices.OrganizationAccountSettings',
            subTitle:'',
            isShow:false,
            article:[
              {
                title:this.$t('help.use2_row1_title'),
                titleL:'help.use2_row1_title',
                article:[
                  {
                    subTitle:this.$t('help.use2_row1_subTitle'),
                    subTitleL:'help.use2_row1_subTitle',
                  }
                ],
              },{
                title:this.$t('help.use2_row2_title'),
                titleL:'help.use2_row2_title',
                article:[
                  {
                    subTitle:this.$t('help.use2_row2_subTitle'),
                    subTitleL:'help.use2_row2_subTitle',
                  }
                ],
              },{
                title:this.$t('help.use2_row3_title'),
                titleL:'help.use2_row3_title',
                article:[
                  {
                    subTitle:this.$t('help.use2_row3_subTitle'),
                    subTitleL:'help.use2_row3_subTitle',
                  }
                ],
              },{
                title:this.$t('help.use2_row4_title'),
                titleL:'help.use2_row4_title',
                article:[
                  {
                    subTitle:this.$t('help.use2_row4_subTitle'),
                    subTitleL:'help.use2_row4_subTitle',
                  }
                ],
              }
            ]
          },
          {
            title:this.$t('notices.ContentPublishingAndVideoUploading'),
            titleL:'notices.ContentPublishingAndVideoUploading',
            subTitle:'',
            isShow:false,
            article:[
              {
                title:this.$t('help.use3_row1_title'),
                titleL:'help.use3_row1_title',
                article:[
                  {
                    subTitle:this.$t('help.use3_row1_subTitle'),
                    subTitleL:'help.use3_row1_subTitle',
                  }
                ],
              },{
                title:this.$t('help.use3_row2_title'),
                titleL:'help.use3_row2_title',
                article:[
                  {
                    subTitle:this.$t('help.use3_row2_subTitle'),
                    subTitleL:'help.use3_row2_subTitle',
                  }
                ],
              },{
                title:this.$t('help.use3_row3_title'),
                titleL:'help.use3_row3_title',
                article:[
                  {
                    subTitle:this.$t('help.use3_row3_row1_subTitle'),
                    subTitleL:'help.use3_row3_row1_subTitle',

                  },{
                    subTitle:this.$t('help.use3_row3_row2_subTitle'),
                    subTitleL:'help.use3_row3_row2_subTitle',

                  },{
                    subTitleL:'help.use3_row3_row3_subTitle',
                  },

                ],
              },{
                title:this.$t('help.use3_row4_title'),
                titleL:'help.use3_row4_title',
                article:[
                  {
                    subTitle:this.$t('help.use3_row4_row1_subTitle'),
                    subTitleL:'help.use3_row4_row1_subTitle'
                  },{
                    subTitle:this.$t('help.use3_row4_row2_subTitle'),
                    subTitleL:'help.use3_row4_row2_subTitle',
                  },{
                    subTitle:this.$t('help.use3_row4_row3_subTitle'),
                    subTitleL:'help.use3_row4_row3_subTitle',
                  },{
                    subTitle:this.$t('help.use3_row4_row4_subTitle'),
                    subTitleL:'help.use3_row4_row4_subTitle',
                  },{
                    subTitle:this.$t('help.use3_row4_row5_subTitle'),
                    subTitleL:'help.use3_row4_row5_subTitle',
                  },{
                    subTitle:this.$t('help.use3_row4_row6_subTitle'),
                    subTitleL:'help.use3_row4_row6_subTitle',
                  },{
                    subTitle:this.$t('help.use3_row4_row7_subTitle'),
                    subTitleL:'help.use3_row4_row7_subTitle',
                  },{
                    subTitle:this.$t('help.use3_row4_row8_subTitle'),
                    subTitleL:'help.use3_row4_row8_subTitle',
                  },{
                    subTitle:this.$t('help.use3_row4_row9_subTitle'),
                    subTitleL:'help.use3_row4_row9_subTitle',
                  },
                ]
              },{
                title:this.$t('help.use3_row5_title'),
                titleL:'help.use3_row5_title',
                article:[
                  {
                    subTitle:this.$t('help.use3_row5_row1_subTitle'),
                    subTitleL:'help.use3_row5_row1_subTitle',

                  },{
                    subTitle:this.$t('help.use3_row5_row2_subTitle'),
                    subTitleL:'help.use3_row5_row2_subTitle',

                  },
                ]
              },{
                title:this.$t('help.use3_row6_title'),
                titleL:'help.use3_row6_title',
                article:[
                  {
                    subTitle:this.$t('help.use3_row6_subTitle'),
                    subTitleL:'help.use3_row6_subTitle',

                  },
                ]
              },
              {
                title:this.$t('help.use3_row7_title'),
                titleL:'help.use3_row7_title',
                article:[
                  {
                    subTitle:this.$t('help.use3_row7_row1_subTitle'),
                    subTitleL:'help.use3_row7_row1_subTitle',

              },{
                subTitle:this.$t('help.use3_row7_row2_subTitle'),
                subTitleL:'help.use3_row7_row2_subTitle',

              }
            ]
          },
        ]
      },
        {
          title:this.$t('notices.ContentDistributionAndRequest'),
          titleL:'notices.ContentDistributionAndRequest',
          subTitle:'',
          isShow:false,
          article:[
            {
              title:this.$t('help.use4_row1_title'),
              titleL:'help.use4_row1_title',
              article:[
                {
                  subTitle:this.$t('help.use4_row1_row1_subTItle'),
                  subTitleL:'help.use4_row1_row1_subTItle',

                },{
                  subTitle:this.$t('help.use4_row1_row2_subTItle'),
                  subTitleL:'help.use4_row1_row2_subTItle',
                }

              ]
            },{
              title:this.$t('help.use4_row2_title'),
              titleL:'help.use4_row2_title',
              article:[
                {
                  subTitle:this.$t('help.use4_row2_subTitle'),
                  subTitleL:'help.use4_row2_subTitle',

                },
              ]
            },{
              title:this.$t('help.use4_row3_title'),
              titleL:'help.use4_row3_title',
              article:[
                {
                  subTitle:this.$t('help.use4_row3_subTitle'),
                  subTitleL:'help.use4_row3_subTitle',

                },
              ]
            },
          ]
        },
        {
          title:this.$t('notices.FeaturedFeatures'),
          titleL:'notices.FeaturedFeatures',
          subTitle:'',
          isShow:false,
          article:[
            {
              title:this.$t('help.use5_row1_title'),
              titleL:'help.use5_row1_title',
              article:[
                {
                  subTitle:this.$t('help.use5_row1_row1_subTitle'),
                  subTitleL:'help.use5_row1_row1_subTitle',
                },{
                  subTitle:this.$t('help.use5_row1_row2_subTitle'),
                  subTitleL:'help.use5_row1_row2_subTitle',

                },
              ]
            },{
              title:this.$t('help.use5_row2_title'),
              titleL:'help.use5_row2_title',
              article:[
                {
                  subTitle:this.$t('help.use5_row2_row1_subTitle'),
                  subTitleL:'help.use5_row2_row1_subTitle',
                },{
                  subTitle:this.$t('help.use5_row2_row2_subTitle'),
                  subTitleL:'help.use5_row2_row2_subTitle',
                },{
                  subTitle:this.$t('help.use5_row2_row3_subTitle'),
                  subTitleL:'help.use5_row2_row3_subTitle',

                },
              ]
            },
          ]
        },
        {
          title:this.$t('notices.FAQFrequentlyAskedQuestions'),
          titleL:'notices.FAQFrequentlyAskedQuestions',
          subTitle:'',
          isShow:false,
          article:[
            {
              title:this.$t('help.row6_row1_title'),
              titleL:'help.row6_row1_title',
              article:[
                {
                  subTitle:this.$t('help.row6_row1_subTitle'),
                  subTitleL:'help.row6_row1_subTitle',
                }
              ]
            },{
              title:this.$t('help.row6_row2_title'),
              titleL:'help.row6_row2_title',
              article:[
                {
                  subTitle:this.$t('help.row6_row2_subTitle'),
                  subTitleL:'help.row6_row2_subTitle',
                }
              ]
            },{
              title:this.$t('help.row6_row3_title'),
              titleL:'help.row6_row3_title',
              article:[
                {
                  subTitle:this.$t('help.row6_row3_subTitle'),
                  subTitleL:'help.row6_row3_subTitle',
                }
              ]
            },{
              title:this.$t('help.row6_row4_title'),
              titleL:'help.row6_row4_title',
              article:[
                {
                  subTitle:this.$t('help.row6_row4_subTitle'),
                  subTitleL:'help.row6_row4_subTitle',
                }
              ]
            },{
              title:this.$t('help.row6_row5_title'),
              titleL:'help.row6_row5_title',
              article:[
                {
                  subTitle:this.$t('help.row6_row5_subTitle'),
                  subTitleL:'help.row6_row5_subTitle',
                }
              ]
            },
          ]
        },
    ],
      }
    },
    computed:{
    },
    created(){
      this.title = this.declarationList1[0].title
      this.article = this.declarationList1[0].article
    },
    methods:{
      itemSwitch(index){
        if(index == this.index){
          return
        }else{
          this.title = this.declarationList1[index].title
          this.article = this.declarationList1[index].article
          this.index = index
          this.declarationList1.forEach((item,index1)=>{
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
              height:413/100rem;
              .item{
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
                position: relative;
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
                height:60/100rem;
              }
              .article{
                width: 848/100rem;
                min-height: 400/100rem;
                font-family: PingFangSC-Regular;
                font-size: 16/100rem;
                color: #666666;
                letter-spacing: 0;
                line-height:22/100rem;

                .articleTitle:first-child{
                  /*margin-top:0;*/
                }
                .articleTitle{
                  font-family: PingFangSC-Medium;
                  font-weight:600;
                  font-size: 16/100rem;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height: 22/100rem;
                  margin-top:50/100rem;
                }
                .articleSubtitle{
                  font-family: PingFangSC-Regular;
                  font-size: 16/100rem;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  margin-top:30/100rem;
                  line-height:22/100rem;
                }
                .articleImg{
                  margin-top:50/100rem;
                  display: flex;
                  justify-content: center;
                  div{
                    max-width: 800/100rem;
                    img{
                      width: 100%;
                      display: block;
                    }
                  }
                }
                .articleImgTitle{
                  margin-top:25/100rem;
                  width: 100%;
                  font-family: PingFangSC-Regular;
                  font-size: 14/100rem;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: center;
                  line-height:20/100rem;
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
              height:413px;
              .item{
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
                position: relative;
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
                height:60px;
              }
              .article{
                width: 848px;
                min-height: 400px;
                font-family: PingFangSC-Regular;
                font-size: 16px;
                color: #666666;
                letter-spacing: 0;
                line-height:22px;

                .articleTitle:first-child{
                  /*margin-top:0;*/
                }
                .articleTitle{
                  font-family: PingFangSC-Medium;
                  font-weight:600;
                  font-size: 16px;
                  color: #333333;
                  letter-spacing: 0;
                  text-align: left;
                  line-height: 22px;
                  margin-top:50px;
                }
                .articleSubtitle{
                  font-family: PingFangSC-Regular;
                  font-size: 16px;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: left;
                  margin-top:30px;
                  line-height:22px;
                }
                .articleImg{
                  margin-top:50px;
                  display: flex;
                  justify-content: center;
                  div{
                    max-width: 800px;
                    img{
                      width: 100%;
                      display: block;
                    }
                  }
                }
                .articleImgTitle{
                  margin-top:25px;
                  width: 100%;
                  font-family: PingFangSC-Regular;
                  font-size: 14px;
                  color: #666666;
                  letter-spacing: 0;
                  text-align: center;
                  line-height:20px;
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
      }
    }
  }
</style>
