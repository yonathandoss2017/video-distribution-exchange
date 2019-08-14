@extends('admin.layout')
@push('title') {{ __('admin/sidebar.exchange') }} | {{ __('app.title') }} @endpush
@section('content')
    <div class="row justify-content-center">

        <header class="title-header col-md-9">
            <h3 class="title">
                @if($termsOfDistribution->name == 'Whitelist for own SP')
                    {{ __('manage/sp/exchange/tod.whitelist_sp') }}
                @else
                    {{ $termsOfDistribution->name }}
                @endif
            </h3>
        </header>

        <div class="col-md-9">
            <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.exchange') }}</a> <span class="brdcrmb-item">/</span><strong class="brdcrmb-item">{{ __('manage/sp/exchange/tod.page_title') }}</strong></p>
        </div><!-- .col-* -->

        <div class="col-md-9">
            <form method="get" class="form-horizontal">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.terms_summary') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/common.name') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($termsOfDistribution->name == 'Whitelist for own SP')
                                {{ __('manage/sp/exchange/tod.whitelist_sp') }}
                            @else
                                {{ $termsOfDistribution->name }}
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/common.status') }}</label>
                        <div class="col-md-9 tod-status">
                            @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW)
                                <span class="label label-orange">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                            @elseif($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED)
                                <span class="label label-rejected">{{ __('manage/sp/exchange/tod.'.$termsOfDistribution->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.internal_remarks') }}</label>
                        <div class="col-md-9 control-label t-a-l">{{ $termsOfDistribution->internal_remarks ?: '-' }}</div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.contract') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($termsOfDistribution->contract)
                                <a href="{{ $termsOfDistribution->contract_url }}" target="_blank">{{ $termsOfDistribution->contract_name }}</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.created_by') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if(object_get($termsOfDistribution->userCreator, 'name'))
                                {{ object_get($termsOfDistribution->userCreator, 'name') }}
                            @else
                                {{ __('manage/sp/exchange/tod.system_generated') }}
                            @endif
                            {{ __('manage/sp/exchange/tod.on') }} {{ $termsOfDistribution->created_at->toFormattedDateString() }}
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.updated_by') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if ($termsOfDistribution->userUpdater)
                                {{ object_get($termsOfDistribution->userUpdater, 'name') }}
                            @else
                                {{ __('manage/sp/exchange/tod.system_generated') }}
                            @endif
                            {{ __('manage/sp/exchange/tod.on') }} {{ $termsOfDistribution->updated_at->toFormattedDateString() }}
                        </div>
                    </div>
                </div>
            </div> <!-- /.ibox -->

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.content_provider') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.distribute_from') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            {{ $termsOfDistribution->contentProvider->organization->name }}
                            &raquo;
                            {{ $termsOfDistribution->contentProvider->name }}
                        </div>
                    </div>
                </div>
            </div> <!-- /.ibox -->

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.service_provider') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.distribute_to') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($termsOfDistribution->sp_property_id != \App\Models\Property::ID_FOR_ADMIN)
                                {{ $termsOfDistribution->serviceProvider->organization->name }}
                                &raquo;
                                {{ $termsOfDistribution->serviceProvider->name }}
                            @else
                                {{ __('manage/sp/exchange/tod.whitelist_internal') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- /.ibox -->

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/sp/exchange/tod.distribution_rights') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="playlist-list">
                        <table class="table table-hover">
                            <tbody>
                            @forelse ($termsOfDistribution->regionRights as $regionRight)
                                <tr>
                                    <td class="playlist-title pt">
                                        <a href="cp-tod-region">
                                            @if ($regionRight->allowed_regions_in_lang)
                                                {{ str_replace(',', ', ', $regionRight->allowed_regions_in_lang) }}
                                            @else
                                                -
                                            @endif
                                        </a><br/>
                                        @if($regionRight->excepted_regions_in_lang)
                                            <small>
                                                {{ __('manage/sp/common.except') }}:
                                                {{ str_replace(',', ', ', $regionRight->excepted_regions_in_lang) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="playlist-amount">
                                        <small>
                                            @if($regionRight->started_at && $regionRight->ended_at)
                                                {{ optional($regionRight->started_at)->toFormattedDateString() }} - {{ optional($regionRight->ended_at)->toFormattedDateString() }}
                                            @else
                                                @if($termsOfDistribution->sp_property_id == \App\Models\Property::ID_FOR_ADMIN)
                                                    {{ __('manage/sp/exchange/tod.always_available') }}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </small>
                                    </td>
                                    <td class="playlist-actions">
                                        <a href="{{ route('admin.exchange.regionRight', [$termsOfDistribution->id, $regionRight->id]) }}" class="btn btn-normal btn-m">{{ __('manage/sp/common.view') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td>{{ __('manage/sp/exchange/tod.no_regions') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- /.ibox -->

            <div class="ibox">
                @if($termsOfDistribution->serviceProvider)
                    <div class="ibox-title">
                        <h5>{{ __('manage/sp/exchange/tod.content_provider_playlists') }}</h5>
                    </div>
                    <div class="ibox-content pt-0">
                        <div class="video-list spwp">
                            @if ($termsOfDistribution->playlistsWithTrashed->count() > 0)
                                <table class="table">
                                    <tbody>
                                    @foreach ($termsOfDistribution->playlistsWithTrashed as $playlist)
                                        <tr>
                                            <td class="image-small">
                                                <div class="video-img video-img-small">
                                                    <a href="{{ route('manage.cp.videos', [$playlist->property_id, 'playlist'=>$playlist->id]) }}">
                                                        <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $playlist->property_id, \Carbon\Carbon::parse($playlist->published_at)->timestamp) }}">
                                                    </a>
                                                </div>
                                            </td>
                                            <td {{ is_null($playlist->pivot->deleted_at) ? '' : 'colspan="2"' }} class="playlist-title {{ is_null($playlist->pivot->deleted_at) ? '' : 'no-highlight' }}">
                                                <a href="{{ route('manage.cp.videos', [$playlist->property_id, 'playlist'=>$playlist->id]) }}">{{ $playlist->name }}</a> <br/>
                                                <small><b>{{ $playlist->entries_count }}</b> {{ __('manage/sp/common.videos') }}</small>
                                            </td>
                                            @if(!is_null($playlist->pivot->deleted_at))
                                                <td class="text-right">
                                                    <span class="label label-grey">{{ __('manage/sp/exchange/tod.sp_deleted') }}</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <span class="no-content">{{ __('manage/sp/exchange/tod.no_playlist_selected') }}</span>
                                <br />
                                <br />
                            @endif
                            <small>{{ __('manage/sp/exchange/tod.tod_playlists_note') }}</small>
                        </div>
                    </div>
                @else
                    <div class="ibox-title">
                        <h5>{{ __('manage/sp/exchange/tod.content_provider_playlists') }}</h5>
                    </div>
                    <div class="ibox-content">
                        {{ __('manage/sp/exchange/tod.include_all_playlists_from_this_cp') }}
                    </div>
                @endif
            </div> <!-- /.ibox -->
        </form>
        @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW)
        <div class="form-save">
            {{ Form::open(['method' => 'PUT', 'route' => ['admin.exchange.approve', $termsOfDistribution->id] ]) }}
            <button type="submit" class="btn btn-normal btn-m m-r">{{ __('manage/sp/common.approve') }}</button>
            {{ Form::close() }}
            {{ Form::open(['method' => 'PUT', 'route' => ['admin.exchange.reject', $termsOfDistribution->id] ]) }}
            <button type="submit" class="btn btn-normal btn-m m-r">{{ __('manage/sp/common.reject') }}</button>
            {{ Form::close() }}
        </div>
        @endif
        </div>
    </div><!-- .row -->
@stop
