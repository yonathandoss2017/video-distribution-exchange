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
  <title>全球视频聚合分发平台 | 新视频</title>
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
                  <div class="title">新视频</div>
              </div>
              <div class="ibox">
                  <div class="ibox-title">
                      <h5>视频上传</h5>
                  </div>
                  <div class="ibox-content">
                      <div class="form-group row">
                          <label class="col-md-3 control-label">
                              上传方式*
                              <input type="hidden" name="direct_upload" id="directUpload" value="">
                          </label>
                          <div class="col-md-9">
                              <div class="custom-controls-stacked">
                                  <label class="custom-control custom-radio m-b-15">
                                      <input name="video_method" type="radio" id="direct_oss" class="custom-control-input video_method http_upload" value="direct_oss" tabindex="10" required="">
                                      <span class="custom-control-indicator"></span>
                                      <span class="custom-control-description">直接上传</span>
                                  </label>
                                  <div class="form-group row direct-upload">
                                      <div class="col-md-12 maxWidth-475">
                                          <input type="hidden" value="" name="video_name" id="video-name">
                                          <input type="hidden" value="" name="video_path" id="video-path">
                                          <div class="qq-gallery qq-uploader">
                                              <div class="qq-upload-button-selector" id="qq-upload-button-selector">
                                              <a id="selectfile" href="javascript:void(0);" class="d-flex flex-column justify-content-center align-items-center w-100 h-100" style="color: inherit; position: relative; z-index: 1;">
                                                  <span>在此放入文件或者点击上传</span>
                                                  <span class="format">支持格式: .MP4, .MPG</span>
                                              </a>
                                              <div id="html5_1danhpar1d1m13if2hf1lsvv053_container" class="moxie-shim moxie-shim-html5" style="position: absolute; top: 0px; left: 0px; width: 445px; height: 150px; overflow: hidden; z-index: 0;"><input id="html5_1danhpar1d1m13if2hf1lsvv053" type="file" style="font-size: 999px; opacity: 0; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%;" accept="video/mp4,video/mpeg"></div></div>
                                              <ul class="qq-upload-list" id="qq-upload-list"></ul>
                                          </div>
                                          <div class="modal fade" id="upload-alert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                              <div class="modal-dialog s-width" role="document">
                                              <div class="modal-content">
                                                  <div class="modal-body"></div>
                                                  <div class="modal-footer">
                                                  <button type="button" class="btn btn-normal" data-dismiss="modal">关闭</button>
                                                  </div>
                                              </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <label class="custom-control custom-radio m-b-15">
                                      <input name="video_method" type="radio" id="oss_url" class="custom-control-input video_method upload_url" value="oss_url" tabindex="11" required="">
                                      <span class="custom-control-indicator"></span>
                                      <span class="custom-control-description">通过URL上传</span>
                                  </label>
                                  <div class="form-group row url">
                                      <div class="col-md-12 maxWidth-475">
                                          <input type="text" id="video_url" class="form-control video_url" name="video_url" value="">
                                          <div class="description">
                                              <small>* 支持格式: .MP4, .MPG</small>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
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
                              <input type="text" class="form-control vidtitle" name="title" value="" tabindex="1" required="">
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

                              <select class="select2 form-control" id="id_label_multiple" multiple="" required="true" name="playlist[]" tabindex="-1" aria-hidden="true"><option value="42">deletett</option><option value="40">plthumb</option><option value="39">上链测试</option><option value="35">pltest</option><option value="31">showTest</option><option value="23">Uploadtest2</option><option value="22">uploadpl2</option><option value="21">uploadpl</option><option value="3">stone-test</option><option value="1">xh-test</option></select>
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
                              <input type="text" class="form-control">
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

                      <div class="form-group row">
                          <label class="col-md-3 control-label">上传方式*</label>
                          <div class="col-md-9">
                              <label class="custom-control custom-radio">
                                  <input id="img-auto" name="img_method" type="radio" class="custom-control-input img-method" value="img_auto" tabindex="7" required="">
                                  <span class="custom-control-indicator"></span>
                                  <span class="custom-control-description">从视频中自动生成</span>
                              </label>
                              <label class="custom-control custom-radio">
                                  <input id="img-upload" name="img_method" type="radio" class="custom-control-input img-method" value="img_direct" tabindex="5" required="">
                                  <span class="custom-control-indicator"></span>
                                  <span class="custom-control-description">直接上传</span>
                              </label>
                              <div id="error_img_method" class=""></div>
                          </div>
                      </div>

                      <div class="img-direct-upload-container" style="display: none;">
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
                                    <img id="preview_image" src="" alt="">
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
              <div class="form-save">
                  <button type="submit" class="btn btn-primary">存储</button>
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
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
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
    $('.select2').select2({
      placeholder: "-- 选择内容列表 --"
    });
    $('.input-group.date').datepicker({
      todayBtn: "linked",
      language: "{{ Session::get('locale') }}",
      keyboardNavigation: false,
      calendarWeeks: true
    });
    </script>
</body>
</html>
