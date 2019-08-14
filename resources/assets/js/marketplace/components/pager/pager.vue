
<template>
  <div id="page">
    <ul class="pagenation" style="margin-left: 0px; padding-inline-start: 0px;    margin-block-start: 0em;
    margin-block-end: 0em;">
      <li class="page_first">
        <button :class="{disable_button:countPage == 1}" :disabled="countPage == 1" @click="up_page()">
          <img v-if="countPage == 1" class="arrowImg" src="/static/icon/Pathleft.png" alt="">
          <img v-else class="arrowImg" src="/static/icon/Pathleft_b.png" alt="">
        </button>
      </li>
      <li v-if='pageArr[0]>1' @click="bit_num(1,0)">1</li>
      <li v-if='pageArr[0]>1'>...</li>
      <li :class="{active:countPage == item}" @click="bit_num(item,index)" v-for='(item,index) in pageArr' :key='index'>{{item}}</li>
      <li v-if='(countPage<allPage-4) && allPage>9'>...</li>
      <li class="page_last">
        <button :class="{disable_button:countPage == allPage}" :disabled='countPage == allPage' @click="next_page()">
          <img v-if="countPage == allPage" class="arrowImg" src="/static/icon/Pathright.png" alt="">
          <img v-else class="arrowImg" src="/static/icon/Pathright_b.png" alt="">
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
  export default {
    name: "page",
    props: {
      allCount: {
        type: Number,
        default: 0
      },
      allPage: {
        type: Number,
        default: 80
      },
      countPagea: {
        type: Number,
        default: 1
      },
      jumpcounta: {
        type: Number,
        default: 1
      },
      meicount: {
        type: Number,
        default: 15
      }
    },
    data() {
      return {
        pageArr: [1, 2, 3, 4, 5, 6, 7, 8, 9],
        countPage: this.countPagea,
        jump_count: this.jumpcounta
      };
    },
    watch: {
      countPagea(){
        this.countPage = this.countPagea
      },
      allPage(index){
        if (this.allPage <= 8) {
          var arr = [];
          for (var i = 0; i < this.allPage; i++) {
            arr.push(i + 1);
          }
          this.pageArr = arr;
        }else{
          this.pageArr = [1, 2, 3, 4, 5, 6, 7, 8, 9]
        }

      }
    },
    computed:{

    },
    created() {
      if (this.allPage <= 8) {
        var arr = [];
        for (var i = 0; i < this.allPage; i++) {
          arr.push(i + 1);
        }
        this.pageArr = arr;
      }
    },
    methods: {
      next_page() {
        if (this.allPage <= 8) {
          this.countPage++;
          //当前页小于5的话就++，页码不需要加
        } else if (this.countPage < 5) {
          this.countPage++;
        } else {
          //当分页器翻到最后几页时，页码不在增加。
          if (this.pageArr[8] == this.allPage) {
            this.countPage++;
            //当分页器最后数字小于总页码的时候，页码增加
          } else if (this.pageArr[8] < this.allPage) {
            //获取最后一个数字
            var lastNum = this.pageArr[this.pageArr.length - 1];
            this.pageArr.push(lastNum + 1);
            this.pageArr.splice(0, 1);
            this.countPage++;
          }
        }
        this.jump_count = this.countPage
        this.$emit("nextPage", this.countPage);
      },
      up_page() {
        if (this.allPage <= 8) {
          this.countPage--;
          //当翻到最前面几页时
        } else if (this.countPage <= 5 && this.pageArr[0] == 1) {
          this.countPage--;
        } else {
          //当前翻页器最后一页等于总页码，并且当前页大于总页码减去3，也是为了保证当前页在中间
          if (
            this.pageArr[8] == this.allPage &&
            this.countPage >= this.allPage - 3
          ) {
            this.countPage--;
            // 当前页小于总页码减去3，也是为了保证当前页在中间，页码增加
          } else if (this.countPage < this.allPage - 3) {
            var firstNum = this.pageArr[0];
            this.pageArr.unshift(firstNum - 1);
            this.pageArr.splice(this.pageArr.length - 1, 1);
            this.countPage--;
          }
        }
        this.jump_count = this.countPage
        this.$emit("nextPage", this.countPage);
      },
      bit_num(item, index) {
        if (this.allPage <= 8) {
          this.countPage = item;
        } else {
          if (item < 5) {
            var arra = [];
            for (var i = 0; i <= 8; i++) {
              arra.push(1 + i);
            }
            this.pageArr = arra;
          }
          if (item >= this.allPage - 4) {
            var arrb = [];
            for (var i = 0; i <= 8; i++) {
              arrb.unshift(this.allPage - i);
            }
            this.pageArr = arrb;
          }
          if (
            item < 5 ||
            (this.pageArr[8] == this.allPage && item >= this.allPage - 4)
          ) {
            this.countPage = item;
          } else if (item >= 5 && item < this.allPage - 3) {
            var arr = [];
            for (var i = 0; i <= 8; i++) {
              var num = item - 4;
              arr.push(num + i);
            }
            this.countPage = item;
            this.pageArr = arr;
          } else if (item > this.allPage - 5) {
            this.countPage = item;
          }
        }
        this.jump_count = item
        this.$emit("nextPage", item);
      },
      jump() {
        this.jump_count >= this.allPage
          ? (this.jump_count = this.allPage)
          : this.jump_count;
        this.jump_count <= 1
          ? this.jump_count = 1
          : this.jump_count;
        this.bit_num(this.jump_count);
      }
    },

    mounted() {
      if (this.allPage <= 8) {
        var arr = [];
        for (var i = 0; i < this.allPage; i++) {
          arr.push(i + 1);
        }
        this.pageArr = arr;
      }
    }
  };
</script>

<style lang="scss">
  .disable_button {
    cursor: not-allowed !important;
  }
  #page{
    /*background: pink;*/
  }
  @media screen {
    @media  (max-width:1300px){
      .pagenation {
        /*width: 100%;*/
        /*margin-top: 100rem;*/
        display: flex;
        /*background: yellow;*/
        justify-content: center;
        li:nth-of-type(1){
          margin-left: 0rem;
        }
        li {
          list-style:none;
          width: 0.28rem;
          height: 0.28rem;
          margin-left: 0.16rem;
          text-align: center;
          /*background: #409eff;*/
          background: #EFEFEF;
          border-radius: 0.02rem;
          font-family: PingFangSC-Regular;
          font-size: 0.14rem;
          color: #333333;
          letter-spacing: 0.0108rem;
          line-height:0.28rem;
          outline: none;
          cursor: pointer;
          button {
            width: 0.28rem;
            height: 0.28rem;
            outline: none;
            border: none;
            /*background: #BA0132;*/
            /*background: gray;*/
            color: #fff;
            background: #EFEFEF;
            border-radius: 0.02rem;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            img{
              width: auto;
              max-height: 0.28rem;
              float: left;
            }
          }
        }
        /*.page_first,*/
        /*.page_last {*/
        /*width: 80rem;*/
        /*height: 25rem;*/
        /*line-height: 25rem;*/
        /*margin-left: 10rem;*/
        /*text-align: center;*/
        /*!*background: #409eff;*!*/
        /*background: #BA0132;*/
        /*}*/
        .countPage,
        .allPage,
        .jump_count {
          width: 1.10rem;
          height: 0.25rem;
          line-height: 0.25rem;
          margin-left: 0.10rem;
          text-align: center;
          background: #409eff;
        }
        .allCount {
          width: 1.20rem;
          height: 0.25rem;
          line-height: 0.25rem;
          margin-left: 0.10rem;
          text-align: center;
          background: #409eff;
        }
        .jump_input {
          padding: 0.03rem;
          width: 0.40rem;
          border: none;
          outline: none;
          height: 0.20rem;
          line-height: 0.20rem;
          border-radius: 0.02rem;
          color: #409eff;
          font-size: 0.12rem;
        }
      }
    }
    @media  (min-width:1301px){
      .pagenation {
        /*margin-top: 100px;*/
        display: flex;
        /*background: yellow;*/
        justify-content: center;
        li {
          list-style:none;
          width: 28px;
          height: 28px;
          margin-left: 16px;
          text-align: center;
          /*background: #409eff;*/
          background: #EFEFEF;
          border-radius: 2px;
          font-family: PingFangSC-Regular;
          font-size: 14px;
          color: #333333;
          letter-spacing: 1.08px;
          line-height:28px;
          outline: none;
          cursor: pointer;
          button {
            width: 28px;
            height: 28px;
            outline: none;
            border: none;
            /*background: #BA0132;*/
            /*background: gray;*/
            color: #fff;
            background: #EFEFEF;
            border-radius: 2px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            img{
              width: auto;
              max-height: 28px;
              float: left;
            }
          }
        }
        /*.page_first,*/
        /*.page_last {*/
        /*width: 80px;*/
        /*height: 25px;*/
        /*line-height: 25px;*/
        /*margin-left: 10px;*/
        /*text-align: center;*/
        /*!*background: #409eff;*!*/
        /*background: #BA0132;*/
        /*}*/
        .countPage,
        .allPage,
        .jump_count {
          width: 110px;
          height: 25px;
          line-height: 25px;
          margin-left: 10px;
          text-align: center;
          background: #409eff;
        }
        .allCount {
          width: 120px;
          height: 25px;
          line-height: 25px;
          margin-left: 10px;
          text-align: center;
          background: #409eff;
        }
        .jump_input {
          padding: 3px;
          width: 40px;
          border: none;
          outline: none;
          height: 20px;
          line-height: 20px;
          border-radius: 2px;
          color: #409eff;
          font-size: 12px;
        }
      }
    }
  }



  .active {
    /*background: #fff !important;*/
    /*color: #409eff !important;*/
    /*border: 1px solid #409eff !important;*/
    background: #BA0132 !important;
    color: #ffffff !important;
  }
</style>
