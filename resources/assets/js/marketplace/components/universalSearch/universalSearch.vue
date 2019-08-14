<template>
  <div class="main">
    <div class="topSearch">
      <div class="searchTitle">
        <div class="spanTitle">{{title}}</div>
        <div class="spanSubtitle">{{subTitle}}</div>
      </div>
      <div v-if="isSearchShow" class="searchInput" :style="isFocus == true? 'border: 1px solid #BA0132;':'border: 1px solid #DDDDDD;' ">
        <input :style="inputBgd"  @blur="animateWidth('blur')" @focus="animateWidth('focus')" class="input" type="text" :placeholder="isFocus == true? '': seachPlaceholder  ">
        <div class="searchIcon">
          <img v-if="isFocus" src="/static/icon/searchSelected.png" alt="">
          <img v-else src="/static/icon/search.png" alt="">
        </div>
      </div>
      <div v-else class="searchAlternatives"></div>
    </div>
  </div>
</template>
<script>
  export default {
    data(){
      return{
        isFocus:false
      }
    },
    props:{
      title:{
        type: String,
      },
      subTitle:{
        type: String,

      },
      seachPlaceholder:{
        type: String,
      },
      inputBgd:{
        type: String,
        default(){
          return 'background:#f9f9f9'
        }
      },
      isSearchShow:{
        type: Boolean,
        default(){
          return true
        }
      },
    },
    methods:{
      animateWidth(type){
        if(type == 'focus'){
          this.isFocus = true
        }else{
          this.isFocus = false
        }
      },

    }
  }
</script>
<style scoped lang="less">
  @media screen {
    @media  (max-width:1300px){
      .topSearch{
        width: 100%;
        height:42/100rem;
        padding:42/100rem 0 53/100rem 0;
        display: flex;
        justify-content: space-between;
        .searchTitle{
          display: flex;
          align-items: flex-end;
          .spanTitle{
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 30/100rem;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:42/100rem;
          }
          .spanSubtitle{
            font-family: PingFangSC-Regular;
            font-size: 14/100rem;
            color: #999999;
            letter-spacing: 0;
            text-align: left;
            line-height:20/100rem;
            margin-left:12/100rem;
          }
        }

      }
      .searchAlternatives{
        width: 321/100rem;
        height:42/100rem;
      }
      .searchInput{
        width: 321/100rem;
        height:42/100rem;
        /*background: gray;*/
        border: 1/100rem solid #DDDDDD;
        border-radius: 4/100rem;
        display: flex;
        .input{
          height:39/100rem;
          width: 279/100rem;
          border-radius: 4/100rem;

          /*background: yellow;*/
          line-height: 42/100rem;
          text-indent: 12/100rem;
          /*border: 0/100rem;*/
          border:none;
          outline-style: none ;
          outline-width: 0/100rem ;
          text-shadow: none ;
          -webkit-appearance: none ;
          -webkit-user-select: text ;
          outline-color: transparent ;
          box-shadow: none;
          /*background: #f9f9f9;*/

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
                width: 20/100rem;
                height:19/100rem;
                /*float: left;*/
              }
            }
      }
    }
    @media  (min-width:1301px){
      .topSearch{
        width: 100%;
        height:42px;
        padding:42px 0 53px 0;
        display: flex;
        justify-content: space-between;
        .searchTitle{
          display: flex;
          align-items: flex-end;
          .spanTitle{
            font-family: PingFangSC-Medium;font-weight:500;
            font-size: 30px;
            color: #333333;
            letter-spacing: 0;
            text-align: left;
            line-height:42px;
          }
          .spanSubtitle{
            height:20px;
            font-family: PingFangSC-Regular;
            font-size: 14px;
            color: #999999;
            letter-spacing: 0;
            text-align: left;
            line-height:20px;
            margin-left:12px;
          }
        }
      }
      .searchAlternatives{
        width: 321px;
        height:42px;
      }
      .searchInput{
        width: 321px;
        height:42px;
        /*background: gray;*/
        border: 1px solid #DDDDDD;
        border-radius: 4px;
        display: flex;
        .input{
          height:39px;
          width: 279px;
          border-radius: 4px;

          /*background: yellow;*/
          line-height: 42px;
          text-indent: 12px;
          /*border: 0px;*/
          border:none;
          outline-style: none ;
          outline-width: 0px ;
          text-shadow: none ;
          -webkit-appearance: none ;
          -webkit-user-select: text ;
          outline-color: transparent ;
          box-shadow: none;
          /*background: #f9f9f9;*/

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
                width: 20px;
                height:19px;
                /*float: left;*/
              }
            }
      }
    }
  }





</style>
