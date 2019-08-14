@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/exchange/tod.page_title') }} - {{ $tod['name'] }} | {{ __('app.title') }}
@endpush

@section('content')
	<div class="container">
	    <div class="row">
	        <div class="col-md-8 offset-md-2">
	        	<div class="form">
	        		<div class="title-header">
                        @if($tod->sp_property_id == \App\Models\Property::ID_FOR_ADMIN)
                            <a href="#" class="btn btn-normal btn-m delete disabled">
                        @else
                            @if (\App\Models\TermsOfDistribution::STATUS_ACTIVE == $tod->status)
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDiscontinue" data-delete-form-id="{{ $tod->id }}" onclick="setDeleteFormId(this)" class="btn btn-normal btn-m delete">
                            @elseif (\App\Models\TermsOfDistribution::STATUS_SP_PENDING == $tod->status)
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDecline" data-delete-form-id="{{ $tod->id }}" onclick="setDeleteFormId(this)" class="btn btn-normal btn-m delete">
                            @else
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalDelete" data-delete-form-id="{{ $tod->id }}" onclick="setDeleteFormId(this)" class="btn btn-normal btn-m delete">
                            @endif
                        @endif
        					{!! Form::open(['method' => 'DELETE', 'url' => route('manage.sp.tod.delete', $property_id), 'id' => 'form_delete_'. $tod->id]) !!}
	        					{!! Form::hidden('delete_mode', 0) !!}
	        					{!! Form::hidden('tod_id', $tod->id) !!}
	        					<i class="fa fa-trash font-12" aria-hidden="true"></i>
        					{!! Form::close() !!}
        				</a>
	        			<div class="title">{{ __('manage/sp/exchange/tod.page_title') }}</div>
	        		</div>

	        		<form method="get" class="form-horizontal">
	        			<div class="ibox">
	        				<div class="ibox-title">
	        					<h5>{{ __('manage/sp/exchange/tod.terms_summary') }}</h5>
	        				</div>
	        				<div class="ibox-content">
	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/common.name') }}</label>
	        						<div class="col-md-9 control-label t-a-l">
                                        @if($tod['name'] == 'Whitelist for own SP')
                                            {{ __('manage/sp/exchange/tod.whitelist_sp') }}
                                        @else
                                            {{ $tod['name'] }}
                                        @endif
                                    </div>
	        					</div>

	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/common.status') }}</label>
	        						<div class="col-md-9 tod-status">
	        							@if($tod->status == \App\Models\TermsOfDistribution::STATUS_ACTIVE)
	        							<span class="label label-active">{{ __('manage/sp/exchange/tod.'.$tod->status) }}</span>
	        							@elseif($tod->status == \App\Models\TermsOfDistribution::STATUS_SP_PENDING)
	        							<span class="label label-orange">{{ __('manage/sp/exchange/tod.'.$tod->status) }}</span>
	        							@elseif($tod->status == \App\Models\TermsOfDistribution::STATUS_CP_REVOKED)
	        							<span class="label label-revoked">{{ __('manage/sp/exchange/tod.'.$tod->status) }}</span>
	        							@elseif($tod->status == \App\Models\TermsOfDistribution::STATUS_SP_DECLINED)
	        							<span class="label label-rejected">{{ __('manage/sp/exchange/tod.'.$tod->status) }}</span>
	        							@elseif($tod->status == \App\Models\TermsOfDistribution::STATUS_SP_DISCONTINUE)
	        							<span class="label label-redoutline">{{ __('manage/sp/exchange/tod.'.$tod->status) }}</span>
                                        @elseif($tod->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW)
                                        <span class="label label-orange">{{ __('manage/sp/exchange/tod.'.$tod->status) }}</span>
                                        @elseif($tod->status == \App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED)
                                        <span class="label label-rejected">{{ __('manage/sp/exchange/tod.'.$tod->status) }}</span>
	        							@endif
        							</div>
	        					</div>

	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.internal_remarks') }}</label>
	        						<div class="col-md-9 control-label t-a-l">{{ $tod['internal_remarks'] ?: '-' }}</div>
	        					</div>

	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.contract') }}</label>
	        						<div class="col-md-9 control-label t-a-l">
	        							@if($tod->contract)
	        								<a href="{{ $tod->contract_url }}" target="_blank">{{ $tod->contract_name }}</a>
        								@else
        								-
        								@endif
        							</div>
	        					</div>

	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.created_by') }}</label>
	        						<div class="col-md-9 control-label t-a-l">
										@if(object_get($tod->userCreator, 'name'))
                                            {{ object_get($tod->userCreator, 'name') }}
                                        @else
										    {{ __('manage/sp/exchange/tod.system_generated') }}
                                        @endif
                                            {{ __('manage/sp/exchange/tod.on') }} {{ $tod->created_at->toFormattedDateString() }}
        							</div>
	        					</div>

	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.updated_by') }}</label>
	        						<div class="col-md-9 control-label t-a-l">
	        							@if ($tod->userUpdater)
		        							{{ object_get($tod->userUpdater, 'name') }}
                                        @else
                                            {{ __('manage/sp/exchange/tod.system_generated') }}
	        							@endif
                                            {{ __('manage/sp/exchange/tod.on') }} {{ $tod->updated_at->toFormattedDateString() }}
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
										{{ $tod->contentProvider->organization->name }}
										&raquo;
										{{ $tod->contentProvider->name }}
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
        									@forelse ($tod->regionRights as $regionRight)
	        									<tr>
	        										<td class="playlist-title pt">
	        											<a href="cp-tod-region">
                                                            @if($regionRight->allowed_regions)
                                                                @foreach ($regionRight->allowed_regions as $region)
                                                                    {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                                                @endforeach
                                                            @else
                                                                -
                                                            @endif
	        											</a>
                                                        @if($regionRight->excepted_regions)
                                                        <br/>
                                                        <small>{{ __('manage/sp/common.except') }}:
                                                            @foreach ($regionRight->excepted_regions as $region)
                                                                {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                                            @endforeach
                                                        </small>
														@endif
	        										</td>
	        										<td class="playlist-amount">
	        											<small>
															@if($regionRight->started_at && $regionRight->ended_at)
															    {{ optional($regionRight->started_at)->toFormattedDateString() }} - {{ optional($regionRight->ended_at)->toFormattedDateString() }}
                                                            @else
                                                                @if($tod->sp_property_id == \App\Models\Property::ID_FOR_ADMIN)
                                                                    {{ __('manage/sp/exchange/tod.always_available') }}
                                                                @else
                                                                    -
                                                                @endif
                                                            @endif
														</small>
	        										</td>
	        										<td class="playlist-actions">
                                                        @if($tod->sp_property_id != \App\Models\Property::ID_FOR_ADMIN)
	        											    <a href="{{ route('manage.sp.tod.regionRight', [$property_id, $tod->id, $regionRight->id]) }}" class="btn btn-normal btn-m">{{ __('manage/sp/common.view') }}</a>
                                                        @endif
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
							@if($tod->serviceProvider)
								<div class="ibox-title">
									<h5>{{ __('manage/sp/exchange/tod.content_provider_playlists') }}</h5>
								</div>
								<div class="ibox-content pt-0">
									<div class="video-list spwp">
										@if ($tod->playlistsWithTrashed->count() > 0)
										<table class="table">
											<tbody>
												@foreach ($tod->playlistsWithTrashed as $playlist)
													<tr>
														<td class="image-small">
															<div class="video-img video-img-small">
																<a href="{{ route('manage.sp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">
																	<img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id, \Carbon\Carbon::parse($playlist->published_at)->timestamp) }}">
																</a>
															</div>
														</td>
                                                        <td {{ is_null($playlist->pivot->deleted_at) ? '' : 'colspan="2"' }} class="playlist-title {{ is_null($playlist->pivot->deleted_at) ? '' : 'no-highlight' }}">
                                                            <a href="{{ route('manage.sp.videos', [$property_id, 'playlist'=>$playlist->id]) }}">{{ $playlist->name }}</a> <br/>
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
	    		</div> <!-- /.form -->
	        </div>
	    </div>
	</div>

	@include('manage.sp.tod._modal_discontinue')
@stop
