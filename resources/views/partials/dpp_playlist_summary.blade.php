<div class="row">
    <div class="col-md-6">
        <div class="ibox">
            <div class="ibox-title">
                <h5>{{ __('manage/cp/ivideoadd/ivideoadd.summary') }}</h5>
            </div>
            <div class="ibox-content">
                <form class="form-horizontal">
                    <div class="form-group row">
                        <label class="col-md-5 control-label">{{ __('manage/cp/ivideoadd/ivideoadd.summary_total_video') }}</label>
                        <div class="col-md-7 control-label t-a-l">
                            {{ __('manage/cp/ivideoadd/ivideoadd.summary_total_videos', ['videos' => $playlist_summary['total_videos']]) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-5 control-label">{{ __('manage/cp/ivideoadd/ivideoadd.summary_total_scene') }}</label>
                        <div class="col-md-7 control-label t-a-l">
                            {{ __('manage/cp/ivideoadd/ivideoadd.summary_total_scenes', ['scenes' => $playlist_summary['total_scenes']]) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-5 control-label">{{ __('manage/cp/ivideoadd/ivideoadd.summary_total_duration') }}</label>
                        <div class="col-md-7 control-label t-a-l">
                            {{ __('manage/cp/ivideoadd/ivideoadd.summary_total_durations', ['seconds' => $playlist_summary['total_dpp']]) }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-title">
                <h5>{{ __('manage/cp/ivideoadd/ivideoadd.valuation_analysis') }}</h5>
            </div>
            <div class="ibox-content">
                <form method="get" class="form-horizontal">
                    <div class="form-group row">
                        <label class="col-md-5 control-label">{{ __('manage/cp/ivideoadd/ivideoadd.summary_total_valuation') }}</label>
                        <div class="col-md-7 control-label t-a-l">
                            {{ __('manage/cp/ivideoadd/ivideoadd.not_available') }}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="ibox dpp">
            <div class="ibox-content">
                <div class="playlist-list">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="playlist-title ptl">{{ __('manage/cp/ivideoadd/ivideoadd.scene_type') }}</th>
                            <th>{{ __('manage/cp/ivideoadd/ivideoadd.total_scenes') }}</th>
                            <th>{{ __('manage/cp/ivideoadd/ivideoadd.total_dpp_time') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $playlist_summary['scenes'] as $scene)
                            <tr>
                                <td class="playlist-title ptl">
                                    <a href="#">{{ __('manage/cp/ivideoadd/ivideoadd.scene_type_'.$scene['type']) }}</a>
                                </td>
                                <td class="playlist-amount">
                                    <span class="amount">{{ $scene['scenes_num'] }}</span> <small>{{ __('manage/cp/ivideoadd/ivideoadd.scenes') }}</small>
                                </td>
                                <td class="right"><span class="amount">{{ $scene['dpp_sum'] }}</span> <small>{{ __('manage/cp/ivideoadd/ivideoadd.seconds') }}</small></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($locales)
        <div class="col-md-12">
            <div class="ibox scene-locale">
                <div class="ibox-title">
                    <h5>{{ __('manage/cp/ivideoadd/ivideoadd.scene_locales_analysis') }}</h5>
                    <i class="fa fa-angle-down fa-2x pull-right fa-angle" aria-hidden="true"></i>
                </div>
                <div class="ibox-content hidden">
                    @include('manage.cp.dpp.locales')
                </div>
            </div>
        </div>
    @endif
</div>
