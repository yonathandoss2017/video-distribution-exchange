@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/report/analytics.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-header">
                    <div class="min-menu row">
                        <div class="col-md-3">
                            <div class="title">{{ __('manage/sp/report/analytics.page_title') }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="right-panel">
                                <button onclick="exportExcel()" class="btn btn-normal btn-m">{{ __('manage/sp/report/analytics.export_to_xls') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-content">
                        <form name="analytics-filter" method="get" class="form-horizontal analytics-form" action="">
                            <div class="form-group row">
                                <label for="example-date-input" class="col-md-2 grid-c control-label">{{ __('manage/sp/report/analytics.date_from') }}</label>
                                <div class="col-md-4 grid-c ">
                                    <input class="form-control " type="date" name="start_date" value="2018-09-11" id="example-date-input">
                                </div>
                                <label for="example-date-input" class="col-md-2 grid-c control-label">{{ __('manage/sp/report/analytics.date_to') }}</label>
                                <div class="col-md-4 grid-c ">
                                    <input class="form-control " type="date" name="end_date" value="2018-09-20" id="example-date-input">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="form-save">
                    <button id="form-submit" type="submit" class="btn btn-secondary">{{ __('manage/sp/common.update') }}</button>
                </div>
            </div>

            <div class="col-md-4 m-t">
                <div class="ibox">
                    <div class="ibox-content row">
                        <div class="col-md-3">
                            <i class="fa fa-play-circle fa-a" aria-hidden="true"></i>
                        </div>
                        <div class="col-md-9 right">
                            {{ __('manage/sp/report/analytics.video_views') }}
                            <h2 class="font-bold text-navy">8,314</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 m-t">
                <div class="ibox">
                    <div class="ibox-content row">
                        <div class="col-md-3">
                            <i class="fa fa-wifi fa-a" aria-hidden="true"></i>
                        </div>
                        <div class="col-md-9 right">
                            {{ __('manage/sp/report/analytics.bandwidth') }} (GB)
                            <h2 class="font-bold text-navy">23</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 m-t">
                <div class="ibox">
                    <div class="ibox-content row">
                        <div class="col-md-3">
                            <i class="fa fa-hourglass-end fa-a" aria-hidden="true"></i>
                        </div>
                        <div class="col-md-9 right">
                            {{ __('manage/sp/report/analytics.average_view_time') }} (mm:ss)
                            <h2 class="font-bold text-navy">03:18</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ibox no-m-t">
                    <div class="ibox-content row">
                        <div class="col-md-3">
                            <i class="fa fa-users fa-a" aria-hidden="true"></i>
                        </div>
                        <div class="col-md-9 right">
                            {{ __('manage/sp/report/analytics.content_providers') }}
                            <h2 class="font-bold text-navy">187</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ibox no-m-t">
                    <div class="ibox-content row">
                        <div class="col-md-3">
                            <i class="fa fa-list-alt fa-a" aria-hidden="true"></i>
                        </div>
                        <div class="col-md-9 right">
                            {{ __('manage/sp/report/analytics.total_playlists') }}
                            <h2 class="font-bold text-navy">352</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="ibox no-m-t">
                    <div class="ibox-content row">
                        <div class="col-md-3">
                            <i class="fa fa-file-video-o fa-a" aria-hidden="true"></i>
                        </div>
                        <div class="col-md-9 right">
                            {{ __('manage/sp/report/analytics.total_videos') }}
                            <h2 class="font-bold text-navy">2,745</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div>
                            <canvas id="analytics" height="71"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-2">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/sp/report/analytics.new_returning') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="newvsreturn" height="250"></canvas>
                            <div class="m-t legend-width">
                                <div class="status-float m-r">
                                    <small><div class="square-box green"></div>{{ __('manage/sp/report/analytics.returning_visitor') }}</small>
                                </div>
                                <div class="">
                                    <small><div class="square-box grey"></div>{{ __('manage/sp/report/analytics.new_visitor') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-2">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/sp/report/analytics.age_groups') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="user" height="250"></canvas>
                            <div class="m-t legend-width">
                                <div class="status-float m-r">
                                    <small><div class="square-box green"></div>35-44</small>
                                </div>
                                <div class="status-float m-r">
                                    <small><div class="square-box grey"></div>45-54</small>
                                </div>
                                <div class="">
                                    <small><div class="square-box red"></div>25-34</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-2">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/sp/common.genders') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="gender" height="250"></canvas>
                            <div class="m-t legend-width">
                                <div class="status-float m-r">
                                    <small><div class="square-box green"></div>{{ __('manage/sp/common.male') }}</small>
                                </div>
                                <div class="">
                                    <small><div class="square-box grey"></div>{{ __('manage/sp/common.female') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-2">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/sp/report/analytics.device_categories') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="device" height="250"></canvas>
                            <div class="m-t legend-width">
                                <div class="status-float m-r">
                                    <small><div class="square-box green"></div>Desktop</small>
                                </div>
                                <div class="status-float m-r">
                                    <small><div class="square-box grey"></div>Mobile</small>
                                </div>
                                <div class="">
                                    <small><div class="square-box red"></div>Tablet</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 grid-2">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/sp/report/analytics.social_networks') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div>
                            <canvas id="social" height="250"></canvas>
                            <div class="m-t legend-width">
                                <div class="status-float m-r">
                                    <small><div class="square-box green"></div>Facebook</small>
                                </div>
                                <div class="status-float m-r">
                                    <small><div class="square-box grey"></div>Not Set</small>
                                </div>
                                <div class="">
                                    <small><div class="square-box red"></div>Google+</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 m-t-2"><div class="title-header"><div class="title">{{ __('manage/sp/common.overview') }}</div></div></div>

            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="playlist-title pl">{{ __('manage/sp/common.countries') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.views') }}</th>
                                    <th>{{ __('manage/sp/report/analytics.views') }} (%)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="playlist-title pl">Malaysia</td>
                                    <td class="right">7,270</td>
                                    <td class="action-right">87.44%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Singapore</td>
                                    <td class="right">593</td>
                                    <td class="action-right">7.13%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Australia</td>
                                    <td class="right">69</td>
                                    <td class="action-right">0.83%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">United States</td>
                                    <td class="right">43</td>
                                    <td class="action-right">0.52%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Brunei</td>
                                    <td class="right">36</td>
                                    <td class="action-right">0.43%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">United Kingdom</td>
                                    <td class="right">34</td>
                                    <td class="action-right">0.41%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Thailand</td>
                                    <td class="right">33</td>
                                    <td class="action-right">0.40%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">India</td>
                                    <td class="right">32</td>
                                    <td class="action-right">0.38%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Taiwan</td>
                                    <td class="right">24</td>
                                    <td class="action-right">0.29%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Hong Kong</td>
                                    <td class="right">23</td>
                                    <td class="action-right">0.28%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="playlist-title pl">{{ __('manage/sp/report/analytics.browsers') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.views') }}</th>
                                    <th>{{ __('manage/sp/report/analytics.views') }} (%)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="playlist-title pl">Android Webview</td>
                                    <td class="right">3,702</td>
                                    <td class="action-right">44.53%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Chrome</td>
                                    <td class="right">1,946</td>
                                    <td class="action-right">23.41%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Safari (in-app)</td>
                                    <td class="right">1,875</td>
                                    <td class="action-right">22.55%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Safari</td>
                                    <td class="right">223</td>
                                    <td class="action-right">2.68%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Firefox</td>
                                    <td class="right">153</td>
                                    <td class="action-right">1.84%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">com.asianmedia.ios</td>
                                    <td class="right">115</td>
                                    <td class="action-right">1.38%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Edge</td>
                                    <td class="right">85</td>
                                    <td class="action-right">1.02%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Samsung Internet</td>
                                    <td class="right">80</td>
                                    <td class="action-right">0.96%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">Internet Explorer</td>
                                    <td class="right">51</td>
                                    <td class="action-right">0.61%</td>
                                </tr>
                                <tr>
                                    <td class="playlist-title pl">UC Browser</td>
                                    <td class="right">32</td>
                                    <td class="action-right">0.38%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="right">#</th>
                                    <th class="playlist-title pl">{{ __('manage/sp/report/analytics.top_10_cp') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.video_views') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.video_views_midpoint') }}</th>
                                    <th>{{ __('manage/sp/report/analytics.video_views_complete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="right">1</td>
                                    <td class="playlist-title pl"><a href="#">www.pocketimes.my</a></td>
                                    <td class="right">7,962</td>
                                    <td class="right">6,141</td>
                                    <td class="right">1,374</td>
                                </tr>
                                <tr>
                                    <td class="right">2</td>
                                    <td class="playlist-title pl"><a href="#">www.sinchew.com.my</a></td>
                                    <td class="right">306</td>
                                    <td class="right">129</td>
                                    <td class="right">16</td>
                                </tr>
                                <tr>
                                    <td class="right">3</td>
                                    <td class="playlist-title pl"><a href="#">app.ad2engage.com</a></td>
                                    <td class="right">28</td>
                                    <td class="right">7</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">4</td>
                                    <td class="playlist-title pl"><a href="#">com.ivideosmart.refApp</a></td>
                                    <td class="right">12</td>
                                    <td class="right">1</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">5</td>
                                    <td class="playlist-title pl"><a href="#">com.ivideosmart.playersdkdemo</a></td>
                                    <td class="right">1</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">6</td>
                                    <td class="playlist-title pl"><a href="#">demo.ivideosmart.com</a></td>
                                    <td class="right">2</td>
                                    <td class="right">1</td>
                                    <td class="right">1</td>
                                </tr>
                                <tr>
                                    <td class="right">7</td>
                                    <td class="playlist-title pl"><a href="#">dev.ad2engage.com</a></td>
                                    <td class="right">2</td>
                                    <td class="right">1</td>
                                    <td class="right">1</td>
                                </tr>
                                <tr>
                                    <td class="right">8</td>
                                    <td class="playlist-title pl"><a href="#">com.ivideosmart.refApp.ivx</a></td>
                                    <td class="right">1</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">9</td>
                                    <td class="playlist-title pl"><a href="#">sith.ivideosmart.com</a></td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">10</td>
                                    <td class="playlist-title pl"><a href="#">ivxtest.ivideocloud.com</a></td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="right">#</th>
                                    <th class="playlist-title pl">{{ __('manage/sp/report/analytics.top_10_playlists') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.video_views') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.video_views_midpoint') }}</th>
                                    <th>{{ __('manage/sp/report/analytics.video_views_complete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="right">1</td>
                                    <td class="playlist-title pl"><a href="#">My Public Channel</a></td>
                                    <td class="right">7,962</td>
                                    <td class="right">6,141</td>
                                    <td class="right">1,374</td>
                                </tr>
                                <tr>
                                    <td class="right">2</td>
                                    <td class="playlist-title pl"><a href="#">Test playlist A</a></td>
                                    <td class="right">306</td>
                                    <td class="right">129</td>
                                    <td class="right">16</td>
                                </tr>
                                <tr>
                                    <td class="right">3</td>
                                    <td class="playlist-title pl"><a href="#">Playlist Cobaan Prend</a></td>
                                    <td class="right">28</td>
                                    <td class="right">7</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">4</td>
                                    <td class="playlist-title pl"><a href="#">Test Live Streams</a></td>
                                    <td class="right">12</td>
                                    <td class="right">1</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">5</td>
                                    <td class="playlist-title pl"><a href="#">test playlist</a></td>
                                    <td class="right">1</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">6</td>
                                    <td class="playlist-title pl"><a href="#">playlist1</a></td>
                                    <td class="right">2</td>
                                    <td class="right">1</td>
                                    <td class="right">1</td>
                                </tr>
                                <tr>
                                    <td class="right">7</td>
                                    <td class="playlist-title pl"><a href="#">playlist2</a></td>
                                    <td class="right">2</td>
                                    <td class="right">1</td>
                                    <td class="right">1</td>
                                </tr>
                                <tr>
                                    <td class="right">8</td>
                                    <td class="playlist-title pl"><a href="#">playlist3</a></td>
                                    <td class="right">1</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">9</td>
                                    <td class="playlist-title pl"><a href="#">playlist4</a></td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                <tr>
                                    <td class="right">10</td>
                                    <td class="playlist-title pl"><a href="#">playlist5</a></td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                    <td class="right">0</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list video-listing">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="right">#</th>
                                    <th class="playlist-title pl">{{ __('manage/sp/report/analytics.tod_10_videos') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.video_views') }}</th>
                                    <th class="right">{{ __('manage/sp/report/analytics.video_views_midpoint') }}</th>
                                    <th>{{ __('manage/sp/report/analytics.video_views_complete') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="right">1</td>
                                    <td class="playlist-title pl"><a href="#">毒酒验出致命甲醇 7嫌犯落网2本地人</a></td>
                                    <td class="right">4,074</td>
                                    <td class="right">2,887</td>
                                    <td class="right">569</td>
                                </tr>
                                <tr>
                                    <td class="right">2</td>
                                    <td class="playlist-title pl"><a href="#">未来谁当副首相？安华心中有谱</a></td>
                                    <td class="right">994</td>
                                    <td class="right">839</td>
                                    <td class="right">268</td>
                                </tr>
                                <tr>
                                    <td class="right">3</td>
                                    <td class="playlist-title pl"><a href="#">巫统退党潮扎希没在怕·希山凯里等否认退党</a></td>
                                    <td class="right">810</td>
                                    <td class="right">715</td>
                                    <td class="right">190</td>
                                </tr>
                                <tr>
                                    <td class="right">4</td>
                                    <td class="playlist-title pl"><a href="#">【财经espresso】和气生财：税收局万能MK置地和解</a></td>
                                    <td class="right">503</td>
                                    <td class="right">440</td>
                                    <td class="right">102</td>
                                </tr>
                                <tr>
                                    <td class="right">5</td>
                                    <td class="playlist-title pl"><a href="#">教育部长现身也不给脸！学生高喊”马智礼下台”</a></td>
                                    <td class="right">475</td>
                                    <td class="right">383</td>
                                    <td class="right">59</td>
                                </tr>
                                <tr>
                                    <td class="right">6</td>
                                    <td class="playlist-title pl"><a href="#">三场补选后 巫统伊党合作大问题？</a></td>
                                    <td class="right">328</td>
                                    <td class="right">251</td>
                                    <td class="right">22</td>
                                </tr>
                                <tr>
                                    <td class="right">7</td>
                                    <td class="playlist-title pl"><a href="#">【晨报】母亲住家遭警突击·纳吉“妈妈我爱你”</a></td>
                                    <td class="right">291</td>
                                    <td class="right">270</td>
                                    <td class="right">54</td>
                                </tr>
                                <tr>
                                    <td class="right">8</td>
                                    <td class="playlist-title pl"><a href="#">没做好排毒 小心毒素累积体内</a></td>
                                    <td class="right">161</td>
                                    <td class="right">143</td>
                                    <td class="right">34</td>
                                </tr>
                                <tr>
                                    <td class="right">9</td>
                                    <td class="playlist-title pl"><a href="#">感觉会被误吞！菲律宾Oslob与鲸鲨（Whale Shark）零距离共游</a></td>
                                    <td class="right">76</td>
                                    <td class="right">45</td>
                                    <td class="right">16</td>
                                </tr>
                                <tr>
                                    <td class="right">10</td>
                                    <td class="playlist-title pl"><a href="#">吉南西岭桥断夺2命 赛夫丁到现场关切</a></td>
                                    <td class="right">68</td>
                                    <td class="right">18</td>
                                    <td class="right">0</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <!-- ChartJS-->
    <script src="/js/plugins/chartJs/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alasql/0.3.7/alasql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.9.2/xlsx.core.min.js"></script>
    <script>
        var lineData = {
            labels: ["20180911","20180912","20180913","20180914","20180915","20180916","20180917","20180918","20180919","20180920",],
            datasets: [
                {
                    label: "Example dataset",
                    fillColor: "rgba(101,195,223,0.5)",
                    strokeColor: "rgba(101,195,223,0.7)",
                    pointColor: "rgba(101,195,223,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(101,195,223,1)",
                    data: [200,2373,3453,4821,1037,3494,2695,4738,5638,0]
                }
            ]
        }
        var lineOptions = {
            scaleShowGridLines: true,
            scaleGridLineColor: "rgba(0,0,0,.05)",
            scaleGridLineWidth: 1,
            bezierCurve: true,
            bezierCurveTension: 0.4,
            pointDot: true,
            pointDotRadius: 4,
            pointDotStrokeWidth: 1,
            pointHitDetectionRadius: 20,
            datasetStroke: true,
            datasetStrokeWidth: 2,
            datasetFill: true,
            responsive: true,
        };
        var ctx = document.getElementById("analytics").getContext("2d");
        var dateChart = new Chart(ctx).Line(lineData, lineOptions);

        $colors = ['#a3e1d4','#dedede','#ff95b2'];
        var pieOptions = {
            segmentShowStroke: true,
            segmentStrokeColor: "#fff",
            segmentStrokeWidth: 2,
            percentageInnerCutout: 45, // This is 0 for Pie charts
            animationSteps: 100,
            animationEasing: "easeOutBounce",
            animateRotate: true,
            animateScale: false,
            responsive: true
        };

        var userTypeData = [
            {
                value: 236734,
                color: '#a3e1d4',
                highlight: "#1ab394",
                label: "Returning Visitor"
            },
            {
                value: 128312,
                color: '#dedede',
                highlight: "#1ab394",
                label: "New Visitor"
            }
        ];
        var newvsreturn = document.getElementById("newvsreturn").getContext("2d");
        var userTypeChart = new Chart(newvsreturn).Doughnut(userTypeData, pieOptions);

        var userAgeData = [
            {
                value: 236734,
                color: '#a3e1d4',
                highlight: "#1ab394",
                label: "35-44"
            },
            {
                value: 128312,
                color: '#dedede',
                highlight: "#1ab394",
                label: "45-54"
            },
            {
                value: 422312,
                color: '#ff95b2',
                highlight: "#1ab394",
                label: "25-34"
            }
        ];
        var user = document.getElementById("user").getContext("2d");
        var userAgeChart = new Chart(user).Doughnut(userAgeData, pieOptions);

        var genderData = [
            {
                value: 236734,
                color: '#a3e1d4',
                highlight: "#1ab394",
                label: "Male"
            },
            {
                value: 478312,
                color: '#dedede',
                highlight: "#1ab394",
                label: "Female"
            }
        ];
        var gender = document.getElementById("gender").getContext("2d");
        var gendarChart = new Chart(gender).Doughnut(genderData, pieOptions);

        var deviceData = [
            {
                value: 6927,
                color: '#a3e1d4',
                highlight: "#1ab394",
                label: "Desktop"
            },
            {
                value: 5927,
                color: '#dedede',
                highlight: "#1ab394",
                label: "Mobile"
            },
            {
                value: 4728,
                color: '#ff95b2',
                highlight: "#1ab394",
                label: "Tablet"
            }
        ];
        var device = document.getElementById("device").getContext("2d");
        var deviceChart = new Chart(device).Doughnut(deviceData, pieOptions);

        var socialData = [
            {
                value: 3829,
                color: '#a3e1d4',
                highlight: "#1ab394",
                label: "Facebook"
            },
            {
                value: 2937,
                color: '#dedede',
                highlight: "#1ab394",
                label: "Not Set"
            },
            {
                value: 4719,
                color: '#ff95b2',
                highlight: "#1ab394",
                label: "Google+"
            }
        ];
        var social = document.getElementById("social").getContext("2d");
        var socialChart = new Chart(social).Doughnut(socialData, pieOptions);
    </script>

@endpush