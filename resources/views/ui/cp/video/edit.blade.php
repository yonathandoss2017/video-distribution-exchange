<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="/images/app/favicon.ico">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
  <link href="/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
  <title>全球视频聚合分发平台 | 编辑视频</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
  <!-- Bootstrap core CSS -->
  <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/datapicker/datepicker3.css">
  <!-- Custom styles for this template -->
  <!-- <link href="/css/style.css?v=2" rel="stylesheet">
  <link href="/css/custom.css?v=2" rel="stylesheet"> -->
  <link href="/css/style.css" rel="stylesheet">
  <link href="/css/custom.css" rel="stylesheet">
  <script src="/vendor/jquery/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
  <meta name="csrf-token" content="zG0sB6369kgiU68B80H101RLGr3cfWZmuktjardJ">
  <script>window.params = {"csrfToken":"zG0sB6369kgiU68B80H101RLGr3cfWZmuktjardJ"}</script>
</head>

<body cz-shortcut-listen="true">
  <div id="wrapper-dashboard">
    <div class="header">
      <div class="main-menu desktop">
        <div class="top-menu">
          <div class="logo-navi">
            <span class="helper"></span><a href="http://cnctest.ivideocloud.cn/manage"><img src="http://cnctest.ivideocloud.cn/images/app/logo-dashboard.png" alt="home"></a>
          </div>
          <div class="service-platform dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">CNC
              <i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu">
              <li><a href="http://cnctest.ivideocloud.cn/manage/organization/select/2">cp-test</a></li>
              <li><a href="http://cnctest.ivideocloud.cn/manage/organization/select/6">青岛百灵信息科技</a></li>
              <li><a href="http://cnctest.ivideocloud.cn/manage/organization/select/8">deletetest</a></li>
            </ul>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
          </div>
          <div class="service-p dropdown active">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">CNC <small class="label cp">CP</small>
              <i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu">
              <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/playlists">CNC <small class="label cp">CP</small></a></li>
              <li><a href="http://cnctest.ivideocloud.cn/manage/2/sp/playlists">CNC <small class="label sp-wp">SP</small></a></li>
            </ul>
          </div>
          <div class="user dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin<i class="fa fa-caret-down" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
              <li><a href="http://cnctest.ivideocloud.cn/manage/profile">账户信息</a></li>
              <li><a href="http://cnctest.ivideocloud.cn/logout">登出</a></li>
            </ul>
          </div>
          <div class="user locale"><a href="http://cnctest.ivideocloud.cn/set_locale/zh"><img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文</a></div>
          <div class="user locale"><a href="http://cnctest.ivideocloud.cn/set_locale/en"><img class="language-img" src="/images/english.png">&nbsp;&nbsp;EN</a></div>
          <div class="float-right"><a href="/marketplace" class="btn btn-normal marketplace">聚合分发平台</a></div>
          <div class="float-right"><a href="/ivxadmin" class="btn btn-normal get-start">超级管理员</a></div>
        </div>
        <div class="navi">
          <ul class="nav">
            <li class="dropdown main-nav-menu">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">内容 <span class="label label-license">10</span><i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a class="" href="http://cnctest.ivideocloud.cn/manage/1/cp/playlists">列表</a></li>
                <li><a class="" href="http://cnctest.ivideocloud.cn/manage/1/cp/videos">视频</a></li>
                <li><a class="" href="http://cnctest.ivideocloud.cn/manage/1/cp/request-logs">请求通知 <span class="label label-license">10</span></a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <a class="dropdown-toggle " data-toggle="dropdown" href="#" aria-expanded="true">内容源站<i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/oauths">OAuth设置</a></li>
              </ul>
            </li>
            <li>
              <a class="" href="http://cnctest.ivideocloud.cn/manage/1/cp/dpp">产品植入技术</a>
            </li>
            <li class="dropdown main-nav-menu">
              <a class="dropdown-toggle   " data-toggle="dropdown" href="#" aria-expanded="true">内容交换 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu exchange">
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/exchange/distribution">分销权益条款</a></li>
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/exchange/marketplace-terms">聚合分发平台条款</a></li>
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/exchange/request-logs">请求通知 </a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <a class=" dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">报表 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/analytics" class="">数据分析</a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <a class=" dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">版权管理 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/block-chain" class="">华视链专区</a></li>
                <li><a href="#">版权保护管理条款</a></li>
                <li><a href="#">版权维护委托条款</a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <a class="dropdown-toggle " data-toggle="dropdown" href="#" aria-expanded="true">设置 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/settings">账户设置</a></li>
                <li><a href="http://cnctest.ivideocloud.cn/manage/1/cp/exchange/notification-settings">通知设置</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Begin page content -->
    <div class="container">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <form method="POST" action="http://cnctest.ivideocloud.cn/manage/1/cp/videos/add" accept-charset="UTF-8" id="video_form" class="form-horizontal" enctype="multipart/form-data"><input name="_token" type="hidden" value="YhgXCPBLOtYhyxQn9qsHYv5d85tqo5IX25qEWLp7">
            <div class="form">
              <div class="title-header ">
                <a href="http://cnctest.ivideocloud.cn/manage/1/cp/videos" class="btn btn-normal btn-m">回到视频</a>
                <div class="title">编辑视频</div>
              </div>
              <div class="top-video" style="margin-top: 20px">
                <div class="video-grid embed-responsive embed-responsive-16by9" id="video"></div>
                <div class="download-video text-center">
                  <a href="javascript:download(0);" id="download_video_0" url="http://cnctest.ivideocloud.cn/manage/1/cp/videos/293/download" class="btn btn-normal float-none text-uppercase">下载视频</a>
                </div>
              </div>
              <div class="ibox">
                <div class="ibox-title">
                  <h5>视频信息</h5>
                </div>
                <div class="ibox-content">
                  <div class="form-group row">
                    <label class="col-md-3 control-label">名称*</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control vidtitle" name="title" value="bunny" tabindex="1" required="">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 control-label">描述</label>
                    <div class="col-md-9">
                      <textarea class="form-control-area" id="exampleTextarea" rows="3" name="description" tabindex="2"></textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 control-label">列表*</label>
                    <div class="col-md-9">
                      <select class="select2 form-control" id="id_label_multiple" multiple="" required="true" name="playlist[]" tabindex="-1" aria-hidden="true">
                        <option value="42">deletett</option>
                        <option value="40" selected>plthumb</option>
                        <option value="39">上链测试</option>
                        <option value="35">pltest</option>
                        <option value="31">showTest</option>
                        <option value="23">Uploadtest2</option>
                        <option value="22">uploadpl2</option>
                        <option value="21">uploadpl</option>
                        <option value="3">stone-test</option>
                        <option value="1">xh-test</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 control-label">标签</label>
                    <div class="col-md-9">
                      <input type="text" class="form-control" name="tags" value="" tabindex="3">
                      <div class="description">使用逗号以添加多个标签</div>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-md-3 control-label">制作时间</label>
                    <div class="col-md-9">
                      <div class="input-group date">
                        <input type="text" class="form-control" value="2019年05月13日">
                        <span class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="ibox">
                <div class="ibox-title">
                  <h5>封面图上传</h5>
                  <input type="hidden" id="keyNameObj" value="">
                </div>
                <div class="ibox-content">
                  <div class="img-direct-upload-container">
                    <div class="form-group row img-direct-upload" style="">
                      <label class="col-md-3 control-label">图片</label>
                      <div class="col-md-9">
                        <label class="custom-file">
                          <input type="file" name="imagefile" id="imagefile" class="custom-file-input" value="">
                          <span class="custom-file-control"></span>
                        </label>
                        <div class="description">
                          <small>
                            * 自定义视频封面，仅支持JPEG/PNG格式，最大1MB，不超过1920*1080px.<br>
                          </small>
                        </div>
                        <div class="creative-image">
                          <img id="preview_image" src="http://cnctest.ivideocloud.cn/serve/image/1/video/293/1555657561?width=780" alt="">
                        </div>
                      </div>
                    </div>
                    <script type="text/javascript" src="/js/featuredimage.js"></script>
                    <script>
                      bindImageFileChange();
                          </script>
                  </div>
                </div>
              </div>
              <div class="ibox">
                <div class="ibox-title">
                  <h5>视频定价</h5>
                </div>
                <div class="ibox-content">
                  <div class="form-group row">
                    <label class="col-md-3 control-label">清晰度</label>
                    <div class="col-md-9">
                      <div class="row resolution">
                        <div class="col-md-6 d-flex">
                          <label class="control-label t-a-l m-r">4K</label>
                          <input class="form-control short-3" name="price" value="" type="number" min="0.00" step="0.01">
                          <label class="control-label t-a-l">￥</label>
                        </div>
                        <div class="col-md-6 d-flex">
                          <label class="control-label t-a-l m-r">2K</label>
                          <input class="form-control short-3" name="price" value="" type="number" min="0.00" step="0.01">
                          <label class="control-label t-a-l">￥</label>
                        </div>
                      </div>

                      <div class="row resolution">
                        <div class="col-md-6 d-flex">
                          <label class="control-label t-a-l m-r">超清</label>
                          <input class="form-control short-3" name="price" value="" type="number" min="0.00" step="0.01">
                          <label class="control-label t-a-l">￥</label>
                        </div>
                        <div class="col-md-6 d-flex">
                          <label class="control-label t-a-l m-r">高清</label>
                          <input class="form-control short-3" name="price" value="" type="number" min="0.00" step="0.01">
                          <label class="control-label t-a-l">￥</label>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6 d-flex">
                          <label class="control-label t-a-l m-r resolution">标清</label>
                          <input class="form-control short-3" name="price" value="" type="number" min="0.00" step="0.01">
                          <label class="control-label t-a-l">￥</label>
                        </div>
                        <div class="col-md-6 d-flex">
                          <label class="control-label t-a-l m-r resolution">流畅</label>
                          <input class="form-control short-3" name="price" value="" type="number" min="0.00" step="0.01">
                          <label class="control-label t-a-l">￥</label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-3 control-label">注释</label>
                    <div class="col-md-9">
                      <textarea class="form-control-area" id="" rows="3" name="comment" tabindex="2"></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <p>&nbsp;</p>
              <div class="title-header">
                <div class="title">视频审核</div>
              </div>
              <div class="ibox">
                <div class="ibox-title">
                  <h5>审核意见</h5>
                </div>
                <div class="ibox-content">
                  <div class="form form-horizontal">
                    <div class="form-group row">
                      <div class="col-md-12 control-label t-a-l">第一条修改意见 第两条修改意见</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-save">
                <button type="submit" class="btn btn-primary">存储</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">存储并提交</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <footer class="footer">
      <div class="container">
        <center>© 2019 VideoPace.版权所有</center>
      </div>
    </footer>
  </div>
  <!-- Bootstrap core JavaScript
    ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <!-- Moved up for HS Editor -->
  <!-- <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script> -->
  <script>
  window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.6.1/clipboard.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <script src="/js/ie10-viewport-bug-workaround.js"></script>
  <script src="/js/scripts.js?v=3.1"></script>
  <script type="text/javascript" src="http://cnctest.ivideocloud.cn/js/format_timezones.js"></script>
  <script type="text/javascript" src="http://cnctest.ivideocloud.cn/js/cp_video/new_video.js"></script>
  <script type="text/javascript" src="/js/datapicker/bootstrap-datepicker.js"></script>
  <script type="text/javascript" src="/js/datapicker/bootstrap-datepicker.zh.min.js"></script>
  <script>
    $( document ).ready(function() {
      $('.select2').select2({
        placeholder: "-- 选择内容列表 --"
      });
      $('.input-group.date').datepicker({
        todayBtn: "linked",
        language: "{{ Session::get('locale') }}",
        keyboardNavigation: false,
        calendarWeeks: true
      });
    });

    function checknum(obj)
   {   
     if(/^\d+\.?\d{0,2}$/.test(obj.value)){
        obj.value = obj.value;
     }else{
        obj.value = obj.value.substring(0,obj.value.length-1);
     }     
   }
  
  </script>
  <link rel="stylesheet" href="https://g.alicdn.com/de/prismplayer/2.8.1/skins/default/aliplayer-min.css">
  <link rel="stylesheet" href="/css/player.css">
  <script charset="utf-8" type="text/javascript" src="/js/aliplayer-min.js"></script>
  <script>
  var lang = "zh" == 'zh' ? 'zh-cn' : 'en-us';
  var player = new Aliplayer({
    id: "video",
    height: "100%",
    width: "100%",
    preload: false,
    qualitySort: "desc",
    language: lang,
    encryptType: 1,
    autoplay: true,
    skinLayout: [{
        "name": "bigPlayButton",
        "align": "cc"
      },
      {
        "name": "H5Loading",
        "align": "cc"
      },
      {
        "name": "errorDisplay",
        "align": "tlabs",
        "x": 0,
        "y": 0
      },
      {
        "name": "infoDisplay"
      },
      {
        "name": "tooltip",
        "align": "blabs",
        "x": 0,
        "y": 56
      },
      {
        "name": "thumbnail"
      },
      {
        "name": "controlBar",
        "align": "blabs",
        "x": 0,
        "y": 0,
        "children": [{
            "name": "progress",
            "align": "blabs",
            "x": 0,
            "y": 44
          },
          {
            "name": "playButton",
            "align": "tl",
            "x": 15,
            "y": 12
          },
          {
            "name": "timeDisplay",
            "align": "tl",
            "x": 10,
            "y": 7
          },
          {
            "name": "fullScreenButton",
            "align": "tr",
            "x": 10,
            "y": 12
          },
          {
            "name": "setting",
            "align": "tr",
            "x": 10,
            "y": 12
          },
          {
            "name": "volume",
            "align": "tr",
            "x": 10,
            "y": 10
          }
        ]
      }
    ],
    vid: "2a2205414a104dd4a6913c0e27981a53",
    playauth: "eyJTZWN1cml0eVRva2VuIjoiQ0FJUzN3SjFxNkZ0NUIyeWZTaklyNG43Zk5uQmk0a1Q1cVdHTldYWG5FNEFTY1I4bUlMQXB6ejJJSHBLZVhkdUFlQVhzL28wbW1oWjcvWVlsck1xRThVWUh4R2NNSnNydHM4S3FGTHdKcExGc3QySjZyOEpqc1Yva2VJRnlWdXBzdlhKYXNEVkVma3VFNVhFTWlJNS8wMGU2TC8rY2lyWVhEN0JHSmFWaUpsaFE4MEtWdzJqRjFSdkQ4dFhJUTBRazYxOUszemRaOW1nTGlidWkzdnhDa1J2MkhCaWptOHR4cW1qL015UTV4MzFpMXYweStCM3dZSHRPY3FjYThCOU1ZMVdUc3Uxdm9oemFyR1Q2Q3BaK2psTStxQVU2cWxZNG1YcnM5cUhFa0ZOd0JpWFNaMjJsT2RpTndoa2ZLTTNOcmRacGZ6bjc1MUN0L2ZVaXA3OHhtUW1YNGdYY1Z5R0ZOLzducFNVUmJ2M2I0eGxMZXVrQVJtWGpJRFRiS3VTbWhnL2ZIY1dPRGxOZjljY01YSnFBWFF1TUdxQWRQTDVwZ2lhTTFyOUVQYmRnZmhtaTRBSjVsSHA3TWVNR1YrRGVMeVF5aDBFSWFVN2EwNDR4ckRoRzVnS3NNUWFnQUVsREJ1UU5PT0JQQUl3c3VZdW1XcE5mT3NPNC95L2VpT1hnOTF6U3ltMzU1eTNyOUNFWXh0YXdEb0ZnS3VMSXk2cHFsZXVQRHJyV0J0a01HM0R6Wmk5cjdtM1BFaXRiSlh1SUtHSzhGM2hBaTB4Z2ZuMUlpN0drMkVuTE5YM24xMll4UmlnL3pZWWhvZUxJbi9yVlNDR2pjSVhYejZjSUVrWDJoVkkxUHozTlE9PSIsIkF1dGhJbmZvIjoie1wiQ0lcIjpcIm5hTDRCTW83K01wM2FwKzdVTjNJbWhCQXRPamNlUDgxWHAzVTVXZ2RTemw0RmhRaXdjUFRwWTNqejdjNGxqK1NlWEZwaDJiYnR1Y1dcXHJcXG5jLzRZdDlKY210UXFIMThMSGU2bkZ2eEpmR25JS1RZPVxcclxcblwiLFwiQ2FsbGVyXCI6XCJSNFZWc0F2T2lsOUROWWRGbGZJdHpqVUpreUowejV1RStad0R6TFdwbURVPVxcclxcblwiLFwiRXhwaXJlVGltZVwiOlwiMjAxOS0wNS0xM1QwNjowOTozOFpcIixcIk1lZGlhSWRcIjpcIjJhMjIwNTQxNGExMDRkZDRhNjkxM2MwZTI3OTgxYTUzXCIsXCJQbGF5RG9tYWluXCI6XCJicm9hZGNhc3QuaXZpZGVvY2xvdWQuY25cIixcIlNpZ25hdHVyZVwiOlwidDMwVnhvdzVZVUVnQU5tZmo3S2MzTU5EcXo0PVwifSIsIlZpZGVvTWV0YSI6eyJTdGF0dXMiOiJOb3JtYWwiLCJWaWRlb0lkIjoiMmEyMjA1NDE0YTEwNGRkNGE2OTEzYzBlMjc5ODFhNTMiLCJUaXRsZSI6ImJ1bm55IiwiQ292ZXJVUkwiOiJodHRwczovL2Jyb2FkY2FzdC5pdmlkZW9jbG91ZC5jbi9pbWFnZS9kZWZhdWx0LzMwNjMyMUU2RjdDRDREQzhBRjdCQ0RFNjRGMDc4ODk4LTYtMi5qcGciLCJEdXJhdGlvbiI6NjAuMDk1fSwiQWNjZXNzS2V5SWQiOiJTVFMuTkpON2N1ZlYyUWdtN0NmeE5VRUtQd0drRSIsIlBsYXlEb21haW4iOiJicm9hZGNhc3QuaXZpZGVvY2xvdWQuY24iLCJBY2Nlc3NLZXlTZWNyZXQiOiJCdHprOUhFcjE5OGVRSmV1cE5IcmJ2WEZ6RDFTVXVvRTlTTkE3NnpEeTc4ZyIsIlJlZ2lvbiI6ImNuLXNoYW5naGFpIiwiQ3VzdG9tZXJJZCI6MTAyNTI1MDU4MzAzNTE5OX0="
  });
  </script>
</body>

</html>