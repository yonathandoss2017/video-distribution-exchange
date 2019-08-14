<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
    <link href="/vendor/font-awesome/css/font-awesome.css" rel="stylesheet">
    <title>iVideoExchange | CP Request Logs</title>
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- <link href="/css/style.css?v=2" rel="stylesheet">
    <link href="/css/custom.css?v=2" rel="stylesheet"> -->
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">

    <meta name="csrf-token" content="nRJQUZGlCoQQsubfGRTzUOhT6rolbIMyM6XmGfpU">

    <script>
        window.params = {"csrfToken":"nRJQUZGlCoQQsubfGRTzUOhT6rolbIMyM6XmGfpU"}    </script>
  </head>
  <body cz-shortcut-listen="true">
    <div id="wrapper-dashboard">
      <div class="header">
        <div class="main-menu desktop">
          <div class="top-menu">
            <div class="logo-navi">
              <span class="helper"></span><a href="https://exchange.ivideocloud.cn/manage"><img src="/images/logo.png" alt="home"></a>
            </div>

            <div class="service-platform">
              <a href="https://exchange.ivideocloud.cn/manage">8sian</a>
              <i class="fa fa-angle-right" aria-hidden="true"></i>
            </div>

            <div class="service-p active dropdown">
              <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">8sian <small class="label cp">CP</small>
                <i class="fa fa-caret-down" aria-hidden="true"></i>
              </a>
              <ul class="dropdown-menu">
                <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/playlists">8sian <small class="label cp">CP</small></a></li>
                <li><a href="https://exchange.ivideocloud.cn/manage/26/sp/playlists">8sian <small class="label sp-wp">SP</small></a></li>
              </ul>
            </div>

            <div class="user dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin<i class="fa fa-caret-down" aria-hidden="true"></i></a>
              <ul class="dropdown-menu">
                <li><a href="https://exchange.ivideocloud.cn/manage/profile">Profile</a></li>
                <li><a href="https://exchange.ivideocloud.cn/logout">Logout</a></li>
              </ul>
            </div>

            <div class="user locale">
              <a href="https://exchange.ivideocloud.cn/set_locale/zh"><img class="language-img" src="/images/chinese.png">&nbsp;&nbsp;中文</a>
            </div>
            <div class="user locale">
              <a href="https://exchange.ivideocloud.cn/set_locale/en"><img class="language-img" src="/images/english.png">&nbsp;&nbsp;EN</a>
            </div>
            <div class="float-right"><a href="/marketplace" class="btn btn-normal marketplace">Marketplace</a></div>
            <div class="float-right">
              <a href="https://exchange.ivideocloud.cn/manage/25/cp/start" class="btn btn-normal get-start">Get Started</a>
            </div>
            <div class="float-right">
              <a href="/ivxadmin" class="btn btn-normal get-start">IvxAdmin</a>
            </div>
          </div>
          <div class="navi">
            <ul class="nav">
              <li class="dropdown main-nav-menu">
                  <a class="dropdown-toggle  { active " data-toggle="dropdown" href="#" aria-expanded="true">Contents
                    <span class="label label-license">2</span><i class="fa fa-caret-down" aria-hidden="true"></i></a>
                  <ul class="dropdown-menu">
                    <li><a class="" href="https://exchange.ivideocloud.cn/manage/25/cp/playlists">Playlists</a></li>
                    <li><a class="" href="https://exchange.ivideocloud.cn/manage/25/cp/videos">Videos</a></li>
                    <li><a class="active" href="#">Request Logs <span class="label label-license">2</span></a></li>
                  </ul>
              </li>

              <li class="dropdown main-nav-menu">
                  <a class="dropdown-toggle " data-toggle="dropdown" href="#" aria-expanded="true">Sources<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                  <ul class="dropdown-menu">
                      <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/oauths">OAuth Settings</a></li>
                      <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/sources">Youtube Sources</a></li>
                  </ul>
              </li>

              <li class="dropdown main-nav-menu">
                  <a class="dropdown-toggle   " data-toggle="dropdown" href="#" aria-expanded="true">Exchange <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                  <ul class="dropdown-menu exchange">
                      <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/exchange/distribution">Terms of Distribution</a></li>
                      <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/exchange/marketplace-terms">Marketplace Terms</a></li>
                      <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/exchange/request-logs">Request Logs </a></li>
                  </ul>
              </li>

              <li class="dropdown main-nav-menu">
                  <a class="dropdown-toggle " data-toggle="dropdown" href="#" aria-expanded="true">Settings <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                  <ul class="dropdown-menu">
                      <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/settings">Property Settings</a></li>
                      <li><a href="https://exchange.ivideocloud.cn/manage/25/cp/exchange/notification-settings">Notification Settings</a></li>
                  </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Begin page content -->
      <div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="title-header">
                <div class="min-menu row">
                    <div class="col-md-3">
                        <div class="title">Request Logs</div>
                    </div>
                    <div class="col-md-9">
                        <div class="right-panel">
                            <form method="GET" action="http://ivx.local/manage/3000004/cp/exchange/request-logs" accept-charset="UTF-8" class="playlist-search">
                            <div class="input-group sr">
                                <input type="text" placeholder="Search" class="input-sm form-control" name="search" value="">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </span>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-12 search-result">
                                            </div>
                </div>
            </div> <!-- /.title-header -->
            <div class="ibox">
                <div class="ibox-content">
                  <div class="video-list spwp">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="image">Thumbnail</th>
                          <th class="playlist-title pl">Title</th>
                          <th>Requested By</th>
                          <th>Entry / Playlist</th>
                          <th class="censorship">{{ __('manage/cp/contents/videos.ai_censorship') }}</th>
                          <th class="date">Date</th>
                          <th class="text-right">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="image">
                            <div class="video-img">
                              <a href="#">
                                <img src="https://ivxchina-oss.oss-cn-shanghai.aliyuncs.com/13/25/entry_814/1536917310.jpg?OSSAccessKeyId=LTAI3SAwivyxdsb1&Expires=1538107524&Signature=bmOvGfKZ%2B%2BEAl8fnE%2Ft6dJUDB%2FM%3D" alt="">
                              </a>
                            </div>
                          </td>
                          <td class="playlist-title">
                            <a href="#">test</a>
                          </td>
                          <td class="playlist-title">
                            <a>yanan</a> <br>
                                <small>yanan.liu@ucastglobal.com.cn </small>
                          </td>
                          <td>Playlist</td>
                          <td class="features">-</td>
                          <td>
                              <div id="">May 7, 2018</div>
                              <small class="timestamp" id="2" dt="1525662031">11:00  GMT +0800</small>
                          </td>
                          <td class="playlist-actions">
                            <div class="action-1 dropdown">
                              <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">Actions <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                  <li class="group">
                                    <h5>Manage</h5>
                                    <a href="#">Approve</a>
                                    <a href="#">Reject</a>
                                  </li>
                                  <li>
                                    <a href="#">View</a>
                                  </li>
                                </ul>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="image">
                            <div class="video-img">
                              <a href="#">
                                <img src="https://ivxchina-oss.oss-cn-shanghai.aliyuncs.com/13/25/entry_811/3705bf7793d4e473f798e743f1f13856.jpg?OSSAccessKeyId=LTAI3SAwivyxdsb1&Expires=1538107524&Signature=xFJwi4%2FNO1gSNAO%2F2byYtP4StOQ%3D" alt="">
                              </a>
                            </div>
                          </td>
                          <td class="playlist-title">
                            <a href="#">test</a>
                          </td>
                          <td class="playlist-title">
                            <a>admin</a> <br>
                                <small>admin@svc.com</small>
                          </td>
                          <td>Entry</td>
                          <td class="features">
                            <i class="fa fa-info-circle text-orange"></i>{{ __('manage/cp/contents/request_logs.ai_result_review') }}
                          </td>
                          <td>
                              <div id="">May 7, 2018</div>
                              <small class="timestamp" id="2" dt="1525662031">11:00  GMT +0800</small>
                          </td>
                          <td class="playlist-actions">
                            <div class="action-1 dropdown">
                              <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">Actions <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                  <li class="group">
                                    <h5>Manage</h5>
                                    <a href="#">Approve</a>
                                    <a href="#">Reject</a>
                                  </li>
                                  <li>
                                    <a href="#">View</a>
                                  </li>
                                </ul>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="image">
                            <div class="video-img">
                              <a href="#">
                                <img src="https://ivxchina-oss.oss-cn-shanghai.aliyuncs.com/13/25/entry_814/1536917310.jpg?OSSAccessKeyId=LTAI3SAwivyxdsb1&Expires=1538107524&Signature=bmOvGfKZ%2B%2BEAl8fnE%2Ft6dJUDB%2FM%3D" alt="">
                              </a>
                            </div>
                          </td>
                          <td class="playlist-title">
                            <a href="#">test</a>
                          </td>
                          <td class="playlist-title">
                            <a>yanan</a> <br>
                                <small>yanan.liu@ucastglobal.com.cn </small>
                          </td>
                          <td>Entry</td>
                          <td class="features"><i class="fa fa-times-circle text-red"></i>{{ __('manage/cp/contents/request_logs.ai_result_block') }}</td>
                          <td>
                              <div id="">May 7, 2018</div>
                              <small class="timestamp" id="2" dt="1525662031">11:00  GMT +0800</small>
                          </td>
                          <td class="playlist-actions">
                            <div class="action-1 dropdown">
                              <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">Actions <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                  <li class="group">
                                    <h5>Manage</h5>
                                    <a href="#">Approve</a>
                                    <a href="#">Reject</a>
                                  </li>
                                  <li>
                                    <a href="#">View</a>
                                  </li>
                                </ul>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="image">
                            <div class="video-img">
                              <a href="#">
                                <img src="https://ivxchina-oss.oss-cn-shanghai.aliyuncs.com/13/25/entry_811/3705bf7793d4e473f798e743f1f13856.jpg?OSSAccessKeyId=LTAI3SAwivyxdsb1&Expires=1538107524&Signature=xFJwi4%2FNO1gSNAO%2F2byYtP4StOQ%3D" alt="">
                              </a>
                            </div>
                          </td>
                          <td class="playlist-title">
                            <a href="#">test</a>
                          </td>
                          <td class="playlist-title">
                            <a>admin</a> <br>
                                <small>admin@svc.com</small>
                          </td>
                          <td>Entry</td>
                          <td class="features"><i class="fa fa-check-circle text-navy"></i>{{ __('manage/cp/contents/request_logs.ai_result_pass') }}</td>
                          <td>
                              <div id="">May 7, 2018</div>
                              <small class="timestamp" id="2" dt="1525662031">11:00  GMT +0800</small>
                          </td>
                          <td class="playlist-actions">
                            <div class="action-1 dropdown">
                              <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">Actions <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                  <li class="group">
                                    <h5>Manage</h5>
                                    <a href="#">Approve</a>
                                    <a href="#">Reject</a>
                                  </li>
                                  <li>
                                    <a href="#">View</a>
                                  </li>
                                </ul>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="image">
                            <div class="video-img">
                              <a href="#">
                                <img src="https://ivxchina-oss.oss-cn-shanghai.aliyuncs.com/13/25/entry_814/1536917310.jpg?OSSAccessKeyId=LTAI3SAwivyxdsb1&Expires=1538107524&Signature=bmOvGfKZ%2B%2BEAl8fnE%2Ft6dJUDB%2FM%3D" alt="">
                              </a>
                            </div>
                          </td>
                          <td class="playlist-title">
                            <a href="#">test</a>
                          </td>
                          <td class="playlist-title">
                            <a>yanan</a> <br>
                                <small>yanan.liu@ucastglobal.com.cn </small>
                          </td>
                          <td>Entry</td>
                          <td class="features"><i class="fa fa-spinner text-orange"></i>{{ __('manage/cp/contents/videos.ai_censoring') }}</td>
                          <td>
                              <div id="">May 7, 2018</div>
                              <small class="timestamp" id="2" dt="1525662031">11:00  GMT +0800</small>
                          </td>
                          <td class="playlist-actions">
                            <div class="action-1 dropdown">
                              <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">Actions <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                  <li class="group">
                                    <h5>Manage</h5>
                                    <a href="#">Approve</a>
                                    <a href="#">Reject</a>
                                  </li>
                                  <li>
                                    <a href="#">View</a>
                                  </li>
                                </ul>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="row">
                            <div class="col-sm-5">
                              <div class="dataTables_info">Showing 1 to 2 of 2 logs
                              </div>
                              <!-- the content is "No Logs" when there's no requests -->
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                </div>
                            </div>
                    </div>
                  </div>
                </div>
            </div> <!-- /.ibox -->
        </div>
    </div>
</div>

      <footer class="footer">
        <div class="container">
          <center>© 2018 iVideoExchange. All Right Reserved.</center>
        </div>
      </footer>
  </div>

  <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- Moved up for HS Editor -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha384-3ceskX3iaEnIogmQchP8opvBy3Mi7Ce34nWjpBIwVTHfGYWQS9jwHDVRnpKKHJg7" crossorigin="anonymous"></script>

    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.6.1/clipboard.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
    <script src="/js/scripts.js?v=3"></script>

  </body>
</html>
