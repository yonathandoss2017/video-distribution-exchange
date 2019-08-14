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
  <title>全球视频聚合分发平台 | 视频</title>
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
  <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css">
  <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
  <meta name="csrf-token" content="zG0sB6369kgiU68B80H101RLGr3cfWZmuktjardJ">
  <script>
  window.params = { "csrfToken": "zG0sB6369kgiU68B80H101RLGr3cfWZmuktjardJ" }
  </script>
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
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">CNC <small class="label sp-wp">SP</small>
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
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">内容<i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a class="" href="http://cnctest.ivideocloud.cn/manage/1/cp/playlists">列表</a></li>
                <li><a class="" href="http://cnctest.ivideocloud.cn/manage/1/cp/videos">视频</a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <span class="feature-label text-blue">app</span>
              <a class="dropdown-toggle " data-toggle="dropdown" href="#" aria-expanded="true">自建平台网站 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a href="http://cnctest.ivideocloud.cn/manage/225/sp/syndication/create">同步设置</a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <a class="dropdown-toggle " data-toggle="dropdown" href="#">内容交换<i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu exchange">
                <li>
                  <a href="http://cnctest.ivideocloud.cn/manage/225/sp/terms-of-distributions">分销权益条款</a>
                </li>
                <li><a href="http://cnctest.ivideocloud.cn/manage/225/sp/request-log">请求通知</a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <a class=" dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">报告 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a href="http://cnctest.ivideocloud.cn/manage/225/sp/analytics" class="">数据分析</a></li>
              </ul>
            </li>
            <li class="dropdown main-nav-menu">
              <a href="#" class="dropdown-toggle " data-toggle="dropdown">设置 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li>
                  <a href="http://cnctest.ivideocloud.cn/manage/225/sp/settings/property">账户设置</a>
                </li>
                <li>
                  <a href="http://cnctest.ivideocloud.cn/manage/225/sp/settings/notifications">通知设置</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Begin page content -->
    <div class="container">
      <div class="row">
        <div class="col-md-6 offset-md-3 text-center">
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="title-header ">
            <div class="min-menu row">
              <div class="col-md-1">
                <div class="title">视频</div>
              </div>
              <div class="col-md-11">
                <div class="right-panel">
                  <div class="status-float">
                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">批量操作 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                      <li><a href="javascript:void(0)" onclick="">下载</a></li>
                    </ul>
                  </div>
                  <div class="status-float m-r">
                    <div id="reportrange" class="form-control">
                      <i class="fa fa-calendar"></i>&nbsp;
                      <span>所有时间</span> <i class="fa fa-caret-down" aria-hidden="true"></i>
                    </div>
                    <script type="text/javascript">
                    $(function() {

                      function cb(start, end) {
                        $('#reportrange span').html(start.format('L') + ' - ' + end.format('L'));
                      }

                      $('#reportrange').daterangepicker({
                        maxDate: moment(),
                        locale: {
                          applyLabel: '确认',
                          cancelLabel: '取消',
                        },
                      }, cb);

                      // cb(start, end);

                    });
                    </script>
                  </div>
                  <div class="status-float">
                    <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">状态 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                    <ul class="dropdown-menu">
                      <li><a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos?playlist=&amp;search=&amp;sort=">所有</a></li>
                      <li><a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos?status=ready&amp;playlist=&amp;search=&amp;sort=">就绪</a></li>
                    </ul>
                  </div>
                  <form method="GET" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos" accept-charset="UTF-8" id="videos_form" style="display:inline-block">
                    <div class="input-group">
                      <input type="text" placeholder="搜索" class="input-sm form-control" name="search" value="">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                      </span>
                    </div>
                    <input type="hidden" class="date_sort" name="sort" value="">
                  </form>
                </div>
              </div>
              <div class="col-md-12 search-result">状态
                <div class="search-result-info filter-margin-right">
                  <span class="search-result-text">04/17/2019 - 05/24/2019</span>
                  <span class="search-close"><a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos?search=&amp;playlist=&amp;sort="><i class="fa fa-times" aria-hidden="true"></i></a></span>
                </div>
              </div>
            </div>
          </div>
          <div class="ibox">
            <div class="ibox-content">
              <div class="video-list spwp">
                <table class="table">
                  <thead>
                    <tr>
                      <th class="select-all">
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input">
                          <span class="custom-control-indicator"></span>
                        </label>
                      </th>
                      <th class="image">
                        <select class='select2' name="" id="">
                          <option value="">当前页面</option>
                          <option value="">所有页面</option>
                        </select>
                      </th>
                      <th class="video-name">名称 / 列表</th>
                      <th class="duration">时长</th>
                      <th class="date sorting" id="date_sort">发布于</th>
                      <th class="status">状态</th>
                      <th class="status text-right">操作</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input video-checkbox" value="2112">
                          <span class="custom-control-indicator"></span>
                        </label>
                      </td>
                      <td class="image">
                        <div class="video-img">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2112">
                            <img src="http://cnctest.ivideocloud.cn/serve/image/144/video/2112/1558073140?width=120" alt="" onclick="getPlayer('2112', '144')">
                          </a>
                        </div>
                      </td>
                      <td class="video-name editable">
                        <div class="editable-title">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2112" onclick="getPlayer('2112', '144')">习近平时间｜脱贫攻坚 咬定目标使劲干</a>
                          <i class="fa fa-edit"></i>
                        </div>
                        <div class="editable-title-input hidden">
                          <input type="hidden" value="2112">
                          <input class="form-control" type="text" value="">
                          <i class="fa fa-check"></i>
                          <i class="fa fa-times"></i>
                        </div>
                        <div class="playlist-small">
                          <small>
                            <ul>
                              <li>习近平时间</li>
                            </ul>
                          </small>
                        </div>
                      </td>
                      <td class="duration">03:30</td>
                      <td class="date">
                        <div id="2112-1558073132">2019年5月17日</div>
                        <small class="timestamp" id="2112" dt="1558073132">14:05 GMT +0800</small>
                      </td>
                      <td class="status">
                        <span class="label label-active">就绪</span>
                      </td>
                      <td class="playlist-actions">
                        <div class="action-1 dropdown">
                          <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">操作 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                          <ul class="dropdown-menu">
                            <li class="group">
                              <h5>管理</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/edit/2112">编辑</a>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2112/download">下载</a>
                            </li>
                            <li class="group">
                              <h5>AI智能</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2112/analyze" class="analyze-confirm" data-toggle="modal" data-target="#analyze">分析</a>
                              <a href="#" class="analyze-disable">结果</a>
                              <a href="#" class="analyze-disable">智能翻译</a>
                            </li>
                            <li>
                              <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="144_2112" onclick="setFormId(this);">
                                <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/delete/2112" accept-charset="UTF-8" id="144_2112"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">删除视频</form>
                              </a>
                            </li>
                          </ul>
                          <div class="alert alert-danger" role="alert" style="display:none;"></div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input video-checkbox" value="14321">
                          <span class="custom-control-indicator"></span>
                        </label>
                      </td>
                      <td class="image">
                        <div class="video-img">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video14321">
                            <img src="http://cnctest.ivideocloud.cn/serve/image/144/video/14321/1558073118?width=120" alt="" onclick="getPlayer('14321', '144')">
                          </a>
                        </div>
                      </td>
                      <td class="video-name editable">
                        <div class="editable-title">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video14321" onclick="getPlayer('14321', '144')">金句来习｜初心</a>
                          <i class="fa fa-edit"></i>
                        </div>
                        <div class="editable-title-input hidden">
                          <input type="hidden" value="14321">
                          <input class="form-control" type="text" value="">
                          <i class="fa fa-check"></i>
                          <i class="fa fa-times"></i>
                        </div>
                        <div class="playlist-small">
                          <small>
                            <ul>
                              <li>金句来习</li>
                            </ul>
                          </small>
                        </div>
                      </td>
                      <td class="duration">
                        05:37
                      </td>
                      <td class="date">
                        <div id="14321-1558073093">2019年5月17日</div>
                        <small class="timestamp" id="14321" dt="1558073093">14:04 GMT +0800</small>
                      </td>
                      <td class="status">
                        <span class="label label-active">就绪</span>
                      </td>
                      <td class="playlist-actions">
                        <div class="action-1 dropdown">
                          <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">操作 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                          <ul class="dropdown-menu">
                            <li class="group">
                              <h5>管理</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/edit/14321">编辑</a>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/14321/download">下载</a>
                            </li>
                            <li class="group">
                              <h5>AI智能</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/14321/analyze" class="analyze-confirm" data-toggle="modal" data-target="#analyze">分析</a>
                              <a href="#" class="analyze-disable">结果</a>
                              <a href="#" class="analyze-disable">智能翻译</a>
                            </li>
                            <li>
                              <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="144_14321" onclick="setFormId(this);">
                                <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/delete/14321" accept-charset="UTF-8" id="144_14321"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">删除视频</form>
                              </a>
                            </li>
                          </ul>
                          <div class="alert alert-danger" role="alert" style="display:none;"></div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input video-checkbox" value="2115">
                          <span class="custom-control-indicator"></span>
                        </label>
                      </td>
                      <td class="image">
                        <div class="video-img">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2115">
                            <img src="http://cnctest.ivideocloud.cn/serve/image/144/video/2115/1558073116?width=120" alt="" onclick="getPlayer('2115', '144')">
                          </a>
                        </div>
                      </td>
                      <td class="video-name editable">
                        <div class="editable-title">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2115" onclick="getPlayer('2115', '144')">习近平时间｜经济全球化是不可逆转的历史大势</a>
                          <i class="fa fa-edit"></i>
                        </div>
                        <div class="editable-title-input hidden">
                          <input type="hidden" value="2115">
                          <input class="form-control" type="text" value="">
                          <i class="fa fa-check"></i>
                          <i class="fa fa-times"></i>
                        </div>
                        <div class="playlist-small">
                          <small>
                            <ul>
                              <li>习近平时间</li>
                            </ul>
                          </small>
                        </div>
                      </td>
                      <td class="duration">
                        03:54
                      </td>
                      <td class="date">
                        <div id="2115-1558073082">2019年5月17日</div>
                        <small class="timestamp" id="2115" dt="1558073082">14:04 GMT +0800</small>
                      </td>
                      <td class="status">
                        <span class="label label-active">就绪</span>
                      </td>
                      <td class="playlist-actions">
                        <div class="action-1 dropdown">
                          <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">操作 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                          <ul class="dropdown-menu">
                            <li class="group">
                              <h5>管理</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/edit/2115">编辑</a>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2115/download">下载</a>
                            </li>
                            <li class="group">
                              <h5>AI智能</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2115/analyze" class="analyze-confirm" data-toggle="modal" data-target="#analyze">分析</a>
                              <a href="#" class="analyze-disable">结果</a>
                              <a href="#" class="analyze-disable">智能翻译</a>
                            </li>
                            <li>
                              <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="144_2115" onclick="setFormId(this);">
                                <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/delete/2115" accept-charset="UTF-8" id="144_2115"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">删除视频</form>
                              </a>
                            </li>
                          </ul>
                          <div class="alert alert-danger" role="alert" style="display:none;"></div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input video-checkbox" value="2119">
                          <span class="custom-control-indicator"></span>
                        </label>
                      </td>
                      <td class="image">
                        <div class="video-img">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2119">
                            <img src="http://cnctest.ivideocloud.cn/serve/image/144/video/2119/1558072930?width=120" alt="" onclick="getPlayer('2119', '144')">
                          </a>
                        </div>
                      </td>
                      <td class="video-name editable">
                        <div class="editable-title">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2119" onclick="getPlayer('2119', '144')">习近平时间｜信仰、信念、信心，任何时候都至关重要</a>
                          <i class="fa fa-edit"></i>
                        </div>
                        <div class="editable-title-input hidden">
                          <input type="hidden" value="2119">
                          <input class="form-control" type="text" value="">
                          <i class="fa fa-check"></i>
                          <i class="fa fa-times"></i>
                        </div>
                        <div class="playlist-small">
                          <small>
                            <ul>
                              <li>习近平时间</li>
                            </ul>
                          </small>
                        </div>
                      </td>
                      <td class="duration">
                        02:46
                      </td>
                      <td class="date">
                        <div id="2119-1558072910">2019年5月17日</div>
                        <small class="timestamp" id="2119" dt="1558072910">14:01 GMT +0800</small>
                      </td>
                      <td class="status">
                        <span class="label label-active">就绪</span>
                      </td>
                      <td class="playlist-actions">
                        <div class="action-1 dropdown">
                          <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">操作 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                          <ul class="dropdown-menu">
                            <li class="group">
                              <h5>管理</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/edit/2119">编辑</a>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2119/download">下载</a>
                            </li>
                            <li class="group">
                              <h5>AI智能</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2119/analyze" class="analyze-confirm" data-toggle="modal" data-target="#analyze">分析</a>
                              <a href="#" class="analyze-disable">结果</a>
                              <a href="#" class="analyze-disable">智能翻译</a>
                            </li>
                            <li>
                              <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="144_2119" onclick="setFormId(this);">
                                <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/delete/2119" accept-charset="UTF-8" id="144_2119"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">删除视频</form>
                              </a>
                            </li>
                          </ul>
                          <div class="alert alert-danger" role="alert" style="display:none;"></div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input video-checkbox" value="2118">
                          <span class="custom-control-indicator"></span>
                        </label>
                      </td>
                      <td class="image">
                        <div class="video-img">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2118">
                            <img src="http://cnctest.ivideocloud.cn/serve/image/144/video/2118/1558072930?width=120" alt="" onclick="getPlayer('2118', '144')">
                          </a>
                        </div>
                      </td>
                      <td class="video-name editable">
                        <div class="editable-title">
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video2118" onclick="getPlayer('2118', '144')">习近平时间｜习近平这样部署生态文明建设</a>
                          <i class="fa fa-edit"></i>
                        </div>
                        <div class="editable-title-input hidden">
                          <input type="hidden" value="2118">
                          <input class="form-control" type="text" value="">
                          <i class="fa fa-check"></i>
                          <i class="fa fa-times"></i>
                        </div>
                        <div class="playlist-small">
                          <small>
                            <ul>
                              <li>习近平时间</li>
                            </ul>
                          </small>
                        </div>
                      </td>
                      <td class="duration">
                        03:37
                      </td>
                      <td class="date">
                        <div id="2118-1558072901">2019年5月17日</div>
                        <small class="timestamp" id="2118" dt="1558072901">14:01 GMT +0800</small>
                      </td>
                      <td class="status">
                        <span class="label label-active">就绪</span>
                      </td>
                      <td class="playlist-actions">
                        <div class="action-1 dropdown">
                          <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">操作 <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                          <ul class="dropdown-menu">
                            <li class="group">
                              <h5>管理</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/edit/2118">编辑</a>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2118/download">下载</a>
                            </li>
                            <li class="group">
                              <h5>AI智能</h5>
                              <a href="http://cnctest.ivideocloud.cn/manage/144/cp/videos/2118/analyze" class="analyze-confirm" data-toggle="modal" data-target="#analyze">分析</a>
                              <a href="#" class="analyze-disable">结果</a>
                              <a href="#" class="analyze-disable">智能翻译</a>
                            </li>
                            <li>
                              <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="144_2118" onclick="setFormId(this);">
                                <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/delete/2118" accept-charset="UTF-8" id="144_2118"><input name="_method" type="hidden" value="DELETE"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">删除视频</form>
                              </a>
                            </li>
                          </ul>
                          <div class="alert alert-danger" role="alert" style="display:none;"></div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <!-- MODAL -->
                <div id="modal"></div>
                <div class="row">
                  <div class="col-md-12"><span>已选择 30 个视频</span></div>
                </div>
                <div class="row">
                  <div class="col-sm-5">
                    <div class="dataTables_info">显示 1 到 5 共有 5 视频</div>
                  </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers">
                      <ul class="pagination">
                        <li class="page-item disabled"><span class="page-link">«</span></li>
                        <li class="page-item disabled"><span class="page-link">‹</span></li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item"><a class="page-link" href="http://cnctest.ivideocloud.cn/manage/144/cp/videos?page=2" rel="next">›</a></li>
                        <li class="page-item"><a class="page-link" href="http://cnctest.ivideocloud.cn/manage/144/cp/videos?page=23" rel="next">»</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="analyze" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">是否分析这个视频?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div>此操作将产生费用，请确认你要继续</div>
                    <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos" accept-charset="UTF-8" id="video-analyze"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#video-analyze').submit();">继续</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">删除视频</h5>
                  </div>
                  <div class="modal-body">
                    <div>此操作无法撤销，是否继续？</div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">返回</button>
                    <button type="button" class="btn btn-secondary" data-type="single_delete" onclick="nextStep();">继续</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">删除视频</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div>提示: 删除此视频意味着与之相关的所有内容和信息都将被删除。</div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="delete-video-button" data-form-id="" onclick="deleteVideo(this);">删除视频</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="stop-livestream" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">停止视频直播</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div>此操作不可撤回.<br>
                      要继续已经停止的直播，请重新创建一个直播项目<br><br>
                      是否继续？</div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="stop-livestream-button" data-link="" onclick="stopLivestream(this);">停止视频直播</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="bulk-delete" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
              <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/delete/bulk" accept-charset="UTF-8"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">
                <input name="video_id" type="hidden" value="">
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-dialog s-width" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="modalLabel">删除内容列表</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div>提示: 删除此视频意味着与之相关的所有内容和信息都将被删除。</div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-secondary">删除视频</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <form method="post" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/review/bulk" id="approve-form">
              <input type="hidden" name="_token" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">
              <input type="hidden" name="video_ids" value="">
            </form>
            <form method="POST" action="http://cnctest.ivideocloud.cn/manage/144/cp/videos/export/bulk" accept-charset="UTF-8" id="export-form"><input name="_token" type="hidden" value="dhyPgEyob4DfoU4Feu59TxVtKsT8nDF3chb36anN">
              <input name="video_ids" type="hidden" value="">
            </form>
          </div>
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
  <script>
  $(".select-all input[type=checkbox]").click(function() {
    if ($(this).is(":checked")) {
      $("td input[type=checkbox]").prop("checked", true)
    } else {
      $("td input[type=checkbox]").prop("checked", false)
    }
  });
  $(document).ready(function() {
    $('.select2').select2({
      minimumResultsForSearch: Infinity,
    });
  });
  </script>
</body>

</html>