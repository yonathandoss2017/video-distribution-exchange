@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.terms_of_distribution') }}
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="form">
                <div class="title-header">
                    <a href="javascript:void(0)" class="btn btn-normal btn-m delete m-l" data-form-id="{{ $property_id }}_{{ $id }}" onclick="setOtherFormId(this);">
                        {{ Form::open(['url' => route('manage.cp.exchange.distribution.destroy', ['property_id'=> $property_id, 'id'=> $id ]), 'method' => 'DELETE', 'id' => $property_id . "_" . $id]) }}
                        <i class="fa fa-trash font-12" aria-hidden="true"></i>
                        {{ Form::close() }}
                    </a>
                    @if($distribution->serviceProvider && $distribution->regionRights->count() > 0 && $distribution->playlists->count() > 0)
                        {{ Form::open(['method' => 'PUT', 'route' => ['manage.cp.exchange.distribution.publish', $property_id, $id] ]) }}
                        <button type="submit" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.publish_to_sp') }}</button>
                        {{ Form::close() }}
                    @endif
                    <div class="title">{{ __('manage/cp/exchange/distribution.terms_of_distribution') }}</div>
                </div>
                <span class="form-horizontal">
                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.terms_summary') }}</h5></div>
                            <a href="{{ route('manage.cp.exchange.distribution.summary.edit', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.edit_summary') }}</a>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.name') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $distribution->name }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.status') }}</label>
                                <div class="col-md-9 tod-status"><span class="label label-grey">{{ __('manage/cp/exchange/distribution.'.$distribution->status) }}</span></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.internal_remarks') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $distribution->internal_remarks ?: '-' }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.contract') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    @if($distribution->contract)
                                        <a href="{{ $distribution->contract_url }}" target="_blank">{{ $distribution->contract_name }}</a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.created_by') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    @if($distribution->creator)
                                        {{ $distribution->userCreator->name }} on {{ $distribution->created_at->format('M j, Y') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.updated_by') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    @if($distribution->updater)
                                        {{ $distribution->userUpdater->name }} on {{ $distribution->updated_at->format('M j, Y') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.service_provider') }}</h5></div>
                            <a href="{{ route('manage.cp.exchange.distribution.sp.index', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.select_sp') }}</a>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.service_provider') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    @if($distribution->serviceProvider)
                                        {{ $distribution->serviceProvider->organization->name }} » {{ $distribution->serviceProvider->name }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.sp_account_uuid') }}</label>
                                <div class="col-md-9 control-label t-a-l">
                                    @if($distribution->serviceProvider)
                                        {{ $distribution->serviceProvider->uuid }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.distribution_rights') }}</h5></div>
                            @if($distribution->regionRights->count() < 1)
                            <a href="{{ route('manage.cp.exchange.distribution.regions.create', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('common.edit') }}</a>
                            @endif
                        </div>
                        <div class="ibox-content">
                            @if($distribution->regionRights->count() > 0)
                                <div class="playlist-list">
                                    <table class="table table-hover">
                                        <tbody>
                                        @foreach($distribution->regionRights as $regionRight)
                                        <tr>
                                            <td class="playlist-title pt">
                                                <a href="#">
                                                    @foreach ($regionRight->allowed_regions as $region)
                                                        {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                                    @endforeach
                                                </a>
                                                @if($regionRight->excepted_regions)
                                                <br>
                                                <small>{{ __('common.except') }}:
                                                    @foreach ($regionRight->excepted_regions as $region)
                                                        {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                                    @endforeach
                                                </small>
                                                @endif
                                            </td>
                                            <td class="playlist-amount">
                                                <small>{{ $regionRight->started_at ? $regionRight->started_at->format('M j, Y') : '' }} - {{ $regionRight->ended_at ? $regionRight->ended_at->format('M j, Y') : '' }}</small>
                                            </td>
                                            <td class="playlist-actions">
                                                <a href="{{ route('manage.cp.exchange.distribution.regions.edit', [$property_id, $id, $regionRight->id]) }}" class="btn btn-normal btn-m">{{ __('common.edit') }}</a>
                                                <a href="javascript:deleteRegion({{ $regionRight->id }});" class="btn btn-normal btn-m trash m-l"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                <form id="delete_form_{{ $regionRight->id }}" action="{{ route('manage.cp.exchange.distribution.regions.destroy', [$property_id, $id, $regionRight->id]) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                {{ __('manage/cp/exchange/distribution.no_region_added') }}
                            @endif
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title title-with-button">
                            <div class="title-button-header"><h5>{{ __('manage/cp/exchange/distribution.content_provider_playlists') }}</h5></div>
                            <a href="{{ route('manage.cp.distribution.playlist', ['property' => $property_id, 'termsOfDistribution' => $distribution]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.manage_playlists') }}</a>
                        </div>
                        <div class="ibox-content">
                            @if($distribution->playlists->count() > 0)
                                <div class="video-list spwp">
                                <table class="table">
                                    <tbody>
                                    @foreach($distribution->playlists as $playlist)
                                    <tr>
                                        <td class="image-small">
                                            <div class="video-img video-img-small">
                                                <a href="{{ route('manage.cp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">
                                                    <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id) }}">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="playlist-title">
                                            <a href="{{ route('manage.cp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">{{ $playlist->name }}</a> <br/>
                                            <small><b>{{ $playlist->ready_entries_count }}</b> {{ __('common.videos') }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
								<small>{{ __('manage/cp/exchange/distribution.most_recent_terms_of_distribution') }}</small>
                            </div>
                            @else
                                <span>{{ __('manage/cp/exchange/distribution.no_playlist_selected') }}</span><br><br>
                                <small>{{ __('manage/cp/exchange/distribution.most_recent_terms_of_distribution') }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="modal fade" id="other_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog s-width" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/exchange/distribution.delete_terms_of_distribution') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                <div>{{ __('manage/cp/exchange/distribution.other_delete_do_you_want_to_proceed') }}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary delete-tod-button" data-form-id="" onclick="deleteTOD(this);">{{ __('common.delete') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
    <script>
        $(function () {
            //防止页面后退
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
        });
        function deleteRegion(id) {
            var x = confirm('Are you sure you want to delete?');
            if (x) {
                $('#delete_form_'+id).submit();
            }
        }
        function setOtherFormId(element) {
            var formId = element.getAttribute('data-form-id');
            $('.delete-tod-button').attr('data-form-id', formId);

            $('#other_delete').modal('show');
        }
        function deleteTOD(element) {
            var formId = element.getAttribute('data-form-id');
            $('#' + formId).submit();
        }
    </script>
@endpush
