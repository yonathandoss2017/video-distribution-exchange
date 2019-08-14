@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/ivideoadd/ivideoadd_scene.dpp_video', ['name'=>$entry->name]) }}
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
        <div class="title-header row">
            <div class="title col-md-6">{{ __('manage/cp/ivideoadd/ivideoadd_scene.dpp_video', ['name'=>$entry->name]) }}</div>
            <div class="col-md-6">
                <a href="{{ route('manage.cp.dpp.edit', [$property_id, $playlist_id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/ivideoadd/ivideoadd_scene.back_to_dpp_playlist') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/ivideoadd/ivideoadd_scene.ivideoadd_summary') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-5 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_total_scene') }}</label>
                                <div class="col-md-7 control-label t-a-l">
                                    {{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_total_scenes', ['scenes' => $scenes['total']]) }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_total_duration') }}</label>
                                <div class="col-md-7 control-label t-a-l">
                                    {{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_total_durations', ['seconds' => $scenes['total_dpp']]) }}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/ivideoadd/ivideoadd_scene.valuation_analysis') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="get" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-md-5 control-label">{{ __('manage/cp/ivideoadd/ivideoadd_scene.summary_total_duration') }}</label>
                                <div class="col-md-7 control-label t-a-l">
                                    {{ __('manage/cp/ivideoadd/ivideoadd_scene.not_available') }}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="ibox dpp-video">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="playlist-title ptl">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_type') }}</th>
                                    <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.total_scenes') }}</th>
                                    <th>{{ __('manage/cp/ivideoadd/ivideoadd_scene.total_dpp_time') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach( $scenes['types'] as $type=>$value)
                                    <tr>
                                        <td class="playlist-title ptl">
                                            <a href="#">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_type_'.$type) }}</a>
                                        </td>
                                        <td class="playlist-amount">
                                            <span class="amount">{{ $value[0] }}</span> <small>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scenes_small') }}</small>
                                        </td>
                                        <td class="right"><span class="amount">{{ $value[1] }}</span> <small>{{ __('manage/cp/ivideoadd/ivideoadd_scene.seconds') }}</small></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="ibox scene-locale">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/ivideoadd/ivideoadd_scene.scene_locales_analysis') }}</h5>
                        <i class="fa fa-angle-down fa-2x pull-right fa-angle" aria-hidden="true"></i>
                    </div>
                    <div class="ibox-content hidden">
                        @include('manage.cp.dpp_scenes.locales')
                    </div>
                </div>
            </div>
        </div>

        <div class="min-menu row m-t-2">
            <div class="col-md-9">
                <div class="title">{{ __('manage/cp/ivideoadd/ivideoadd_scene.scenes_management') }}</div>
            </div>
            <div class="col-md-3">
                <div class="right-panel">
                    <div class="status-float">
                        <a href="#" class="btn btn-normal dropdown-toggle" data-toggle="dropdown">{{ __('manage/cp/ivideoadd/ivideoadd_scene.type_filter') }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('manage.cp.dpp.scenes.index', [$property_id, $playlist_id, $entry->id]) }}">{{ __('manage/cp/ivideoadd/ivideoadd_scene.type_all') }}</a></li>
                            <li><a href="{{ route('manage.cp.dpp.scenes.index', [$property_id, $playlist_id, $entry->id, 'type'=>\App\Models\EntryScene::TYPE_SIGNAGE]) }}">{{ __('manage/cp/ivideoadd/ivideoadd_scene.type_signage') }}</a></li>
                            <li><a href="{{ route('manage.cp.dpp.scenes.index', [$property_id, $playlist_id, $entry->id, 'type'=>\App\Models\EntryScene::TYPE_PRODUCT]) }}">{{ __('manage/cp/ivideoadd/ivideoadd_scene.type_product') }}</a></li>
                            <li><a href="{{ route('manage.cp.dpp.scenes.index', [$property_id, $playlist_id, $entry->id, 'type'=>\App\Models\EntryScene::TYPE_VIDEO]) }}">{{ __('manage/cp/ivideoadd/ivideoadd_scene.type_video') }}</a></li>
                            <li><a href="{{ route('manage.cp.dpp.scenes.index', [$property_id, $playlist_id, $entry->id, 'type'=>\App\Models\EntryScene::TYPE_LOGO]) }}">{{ __('manage/cp/ivideoadd/ivideoadd_scene.type_logo') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row platforms row-eq-height">
        @foreach( $scenes['scenes'] as $scene)
            <div class="col-md-4 scenes-m">
                <div class="video-img main">
                    <a href="javascript:void(0)"><img src="{{ \App\Services\Serve\ScenesImageService::getImageUrl($scene) }}" alt=""></a>
                    <div class="time"><p>{{ gmdate("H:i:s", $scene->dpp_duration) }}</p></div>
                </div>
                <div class="info">
                    <div class="small-detail">
                        <div class="video-title">{{ $scene->name }}</div>
                        <div class="small-detail-row">
                            <object data="/images/scene-type.svg" type="image/svg+xml" class="m-r-11"></object><span>{{ isset($scene->type) ? ucfirst($scene->type) : 'N/A' }}</span>
                        </div>
                        <div class="small-detail-row">
                            <object data="/images/locale.svg" type="image/svg+xml" class="m-r-11"></object><span>{{ isset($scene->locale) ? $scene->locale : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="small-detail m-t-sm">
                        @if ($entry->dpp_status == \App\Models\Entry::DPP_STATUS_PUBLISHED)
                        <a href="{{ route('manage.cp.dpp.scenes.show', [$property_id, $playlist_id, $entry->id, $scene->id]) }}" class="btn btn-normal btn-m no-float">{{ __('common.view') }}</a>
                        @else
                        <a href="{{ route('manage.cp.dpp.scenes.edit', [$property_id, $playlist_id, $entry->id, $scene->id]) }}" class="btn btn-normal btn-m no-float">{{ __('common.edit') }}</a>
                        <div class="genre-info">
                            {{ Form::open(['url'=>route('manage.cp.dpp.scenes.destroy',[$property_id, $playlist_id, $entry->id, $scene->id]),'onsubmit' => 'return ConfirmDelete()']) }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-normal btn-m delete">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            {{ Form::close() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        </div>
        <div class="m-t">{{ __('manage/cp/ivideoadd/ivideoadd_scene.total_scenes_available', ['scenes'=>$scenes['scenes']->count()]) }}</div>
        </div>
    </div>
</div>
@stop

@push('js')
    <script type="text/javascript">
        $('.fa-angle').click(function() {
            $(this).toggleClass('fa-angle-down fa-angle-up');
            $(this).closest('.ibox').find('.ibox-content').slideToggle();
        });
    </script>
    <script>
        $(window).on('hashchange', function() {
            if (window.location.hash) {
                var page = window.location.hash.replace('#', '');
                if (page == Number.NaN || page <= 0) {
                    return false;
                } else {
                    getLocales(page);
                }
            }
        });
        $(document).ready(function() {
            $(document).on('click', '.pagination a', function (e) {
                getLocales($(this).attr('href').split('page=')[1]);
                e.preventDefault();
            });
        });
        function getLocales(page) {
            $.ajax({
                url : '?page=' + page,
                dataType: 'json',
            }).done(function (data) {
                $('.scene-locale .ibox-content' ).html(data);
                location.hash = page;
            }).fail(function () {
                console.log('Locales could not be loaded.');
            });
        }
</script>
@endpush
