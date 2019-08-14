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
                    <a href="javascript:void(0);" class="btn btn-normal btn-m delete m-l" data-toggle="modal" @if($distribution->revocable) data-target="#revoke-delete" @else data-target="#other-delete" @endif><i class="fa fa-trash font-12" aria-hidden="true"></i></a>
                    @if($distribution->status == \App\Models\TermsOfDistribution::STATUS_ACTIVE)
                        {{ Form::open(['url' => route('manage.cp.exchange.distribution.duplicate', ['property_id'=> $property_id, 'id'=> $distribution->id ]), 'method' => 'POST']) }}
                            <button type="submit" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.duplicate') }}</button>
                        {{ Form::close() }}
                    @else
                        <a href="javascript:void(0);" id="edit_tod_pop" class="btn btn-normal btn-m" data-toggle="modal" data-target="#edit-tod">{{ __('common.edit') }}</a>
                    @endif
                    <div class="title">{{ __('manage/cp/exchange/distribution.terms_of_distribution') }}</div>
                    {{ Form::open(['url'=>route('manage.cp.exchange.distribution.revoke', [$property_id, $distribution->id]), 'method' => 'PUT', 'id' => 'revoke-form']) }}
                    {{ Form::close() }}
                    {{ Form::open(['url'=>route('manage.cp.exchange.distribution.destroy', [$property_id, $distribution->id]), 'method' => 'DELETE', 'id' => 'delete-form']) }}
                    {{ Form::hidden('isRevoke','') }}
                    {{ Form::close() }}
                </div>
                <form method="get" class="form-horizontal">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/exchange/distribution.terms_summary') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.name') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $distribution->name }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.status') }}</label>
                                <div class="col-md-9 tod-status"><span class="label label-{{ $distribution->status_color_class }}">{{ __('manage/cp/exchange/distribution.'.$distribution->status) }}</span></div>
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
                                <div class="col-md-9 control-label t-a-l">{{ $distribution->userCreator ? $distribution->userCreator->name : __('manage/cp/exchange/distribution.system_generated') }} on {{ $distribution->created_at->format('M j, Y') }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('common.updated_by') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $distribution->userUpdater ? $distribution->userUpdater->name : __('manage/cp/exchange/distribution.system_generated') }} on {{ $distribution->updated_at->format('M j, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/exchange/distribution.service_provider') }}</h5>
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
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/exchange/distribution.distribution_rights') }}</h5>
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
                                            <a href="{{ route('manage.cp.exchange.distribution.regions.show', [$property_id, $distribution->id, $regionRight->id]) }}" class="btn btn-normal btn-m">{{ __('common.view') }}</a>
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
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/exchange/distribution.content_provider_playlists') }}</h5>
                        </div>
                        <div class="ibox-content pt-0">
                            @if($distribution->playlistsWithTrashed->count() > 0)
                                <div class="video-list spwp">
                                <table class="table">
                                    <tbody>
                                    @foreach($distribution->playlistsWithTrashed as $playlist)
                                    <tr>
                                        <td class="image-small">
                                            <div class="video-img video-img-small">
                                                <a href="{{ route('manage.cp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">
                                                    <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id) }}">
                                                </a>
                                            </div>
                                        </td>
                                        <td {{ is_null($playlist->pivot->deleted_at) ? '' : 'colspan="2"' }} class="playlist-title {{ is_null($playlist->pivot->deleted_at) ? '' : 'no-highlight' }}">
                                            <a href="{{ route('manage.cp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">{{ $playlist->name }}</a> <br/>
                                            <small><b>{{ $playlist->ready_entries_count }}</b> {{ __('common.videos') }}</small>
                                        </td>
                                        @if(!is_null($playlist->pivot->deleted_at))
                                        <td class="text-right">
                                            <span class="label label-grey">{{ __('manage/cp/exchange/distribution.sp_deleted') }}</span>
                                        </td>
                                        @endif
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
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="revoke-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog s-width" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/exchange/distribution.revoke_terms_of_distribution') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>{{ __('manage/cp/exchange/distribution.revoke_message') }}</div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:deleteTOD(1);">{{ __('manage/cp/exchange/distribution.revoke_and_delete') }}</a>
                    <button type="button" onclick="deleteTOD(2);" class="btn btn-secondary">{{ __('manage/cp/exchange/distribution.revoke_only') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="other-delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                    <button type="button" onclick="deleteTOD(0);" class="btn btn-secondary">{{ __('common.delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-tod" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog s-width" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/exchange/distribution.edit_terms_of_distribution') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>{{ __('manage/cp/exchange/distribution.edit_terms_of_distribution_warn_message') }}</div>
                </div>
                <div class="modal-footer">
                    {{ Form::open(['url'=>route('manage.cp.exchange.distribution.revert_to_draft', [$property_id, $distribution->id]), 'method' => 'PUT']) }}
                        <button type="submit" class="btn btn-secondary">{{ __('common.edit') }}</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
    <script>
        function deleteTOD(type) {
            if (type == 2) {
                $('#revoke-form').submit();
            } else {
                $("input[name = 'isRevoke']").val(type);
                $('#delete-form').submit();
            }
        }
        @if ($show_pop)
            $(function () {
                $('#edit_tod_pop').trigger('click');
            });
        @endif
    </script>
@endpush
