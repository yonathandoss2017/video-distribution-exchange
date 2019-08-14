@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/exchange/tod.page_title') }} - {{ __('manage/sp/exchange/tod.region_rights', ['region_name' => $regionRight['tod']['name'] ]) }} | {{ __('app.title') }}
@endpush

@section('content')
	<div class="container">
	    <div class="row">
	        <div class="col-md-8 offset-md-2">
	        	<div class="form">
	        		<div class="title-header">
	        			<a href="{{ route('manage.sp.tod.show', [$property_id, $regionRight->tod->id]) }}" class="btn btn-normal btn-m">{{ __('manage/sp/exchange/tod.back_to_tod') }}</a>
	        			<div class="title">{{ __('manage/sp/exchange/tod.regions_distribution') }}</div>
	        		</div>
	        		<form method="get" class="form-horizontal">
	        			<div class="ibox">
	        				<div class="ibox-title">
	        					<h5>{{ __('manage/sp/exchange/tod.regional_rights') }}</h5>
	        				</div>
	        				<div class="ibox-content">
	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.allowed_regions') }}</label>
	        						<div class="col-md-9 control-label t-a-l">
                                        @if($regionRight->region_allowed)
                                            @foreach ($regionRight->region_allowed as $region)
                                                {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
	        						</div>
	        					</div>
	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/common.except') }}</label>
	        						<div class="col-md-9 control-label t-a-l">
                                        @if($regionRight->region_excepted)
                                            @foreach ($regionRight->region_excepted as $region)
                                                {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </div>
	        					</div>
	        					<div class="form-group row">
	        						<label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.availability_period') }}</label>
	        						<div class="col-md-9 control-label t-a-l">
                                        @if($regionRight->started_at && $regionRight->ended_at)
                                            {{ optional($regionRight->started_at)->toFormattedDateString() }} - {{ optional($regionRight->ended_at)->toFormattedDateString() }}
                                        @else
                                            @if($regionRight->tod->sp_property_id == \App\Models\Property::ID_FOR_ADMIN)
                                                {{ __('manage/sp/exchange/tod.always_available') }}
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </div>
	        					</div>
        					</div>
    					</div> <!-- /.ibox -->

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/exchange/marketplace_terms.payment_model') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.payment_mode') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $regionRight->payment_mode ? __('term.payment_mode.'.$regionRight->payment_mode) : '-' }}</div>
                                </div>
                                @if($regionRight->payment_mode && $regionRight->payment_mode != 'free-download')
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.exclusive') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ __('term.exclusive_property.'.$regionRight->exclusivity) }}</div>
                                    </div>
                                @endif
                                @if($regionRight->payment_mode == 'revenue-share')
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.revenue-proportion') }}</label>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-5 business-terms-column">
                                                    <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.content_provider') }} {{ $regionRight->revenue_share_cp }}  %</label>
                                                </div>
                                                <div class="col-md-5 business-terms-column">
                                                    <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.service_provider') }} {{ $regionRight->revenue_share_sp }} %</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(in_array($regionRight->payment_mode, ['annual-download', 'monthly-download']))
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_update_quantity') }}</label>
                                        <div class="col-md-9 control-label t-a-l">
                                            @if($regionRight->payment_mode == 'annual-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_year') }}</label>
                                            @elseif($regionRight->payment_mode == 'monthly-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_month') }}</label>
                                            @endif
                                            {{ $regionRight->update_count }}&nbsp;&nbsp;{{ __('manage/cp/exchange/marketplace_terms.item') }}
                                        </div>
                                    </div>
                                @endif
                                @if(in_array($regionRight->payment_mode, ['charge-download', 'annual-download', 'monthly-download']))
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.content_price') }}</label>
                                        <div class="col-md-9 control-label t-a-l">
                                            @if($regionRight->payment_mode == 'annual-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.per_year') }}</label>
                                            @elseif($regionRight->payment_mode == 'monthly-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.per_month') }}</label>
                                            @endif
                                            {{ $regionRight->price }}&nbsp;&nbsp;￥
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.footnote') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $regionRight->payment_comments ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/exchange/marketplace_terms.distribution_model') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row" style="margin-bottom: 3px">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.api_distribution') }}</label>
                                    <div class="col-md-9 control-label t-a-l">
                                        @if($regionRight->api_share_to)
                                            @foreach ($regionRight->api_share_to as $item)
                                                {{ __('term.api_share_to.'.$item) }} {{ $loop->last ? '' : ',' }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                @if($regionRight->payment_mode != 'revenue-share')
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_download') }}</label>
                                        <div class="col-md-9 control-label t-a-l">
                                            @if($regionRight->download_resolution)
                                                @foreach ($regionRight->download_resolution as $item)
                                                    {{ __('term.video_download.'.$item) }} {{ $loop->last ? '' : ',' }}
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/sp/exchange/tod.custom_terms') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/sp/exchange/tod.extra_terms') }}</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static">
                                            {!! $regionRight->extra_terms ? nl2br($regionRight->extra_terms) : '-' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
					</form>
				</div>
	        </div>
        </div> <!-- /.row -->
    </div>
@stop
