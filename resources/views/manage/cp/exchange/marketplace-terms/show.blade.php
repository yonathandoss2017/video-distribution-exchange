@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/marketplace_terms.marketplace_terms') }}
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
        <div class="form">
            <div class="title-header ">
                <a href="{{ route('manage.cp.exchange.marketplace-terms.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/marketplace_terms.back_to_marketplace_terms') }}</a>
                <div class="title">{{ __('manage/cp/exchange/marketplace_terms.marketplace_terms') }}</div>
            </div>
            <form method="get" class="form-horizontal">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/cp/exchange/marketplace_terms.regional_rights') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.allowed_regions') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @foreach ($marketplaceTerm->region_allowed as $region)
                                {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('common.except') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($marketplaceTerm->region_excepted)
                                @foreach ($marketplaceTerm->region_excepted as $region)
                                    {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                @endforeach
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ __('manage/cp/exchange/marketplace_terms.payment_model') }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.payment_mode') }}</label>
                        <div class="col-md-9 control-label t-a-l">{{ __('term.payment_mode.'.$marketplaceTerm->payment_mode) }}</div>
                    </div>
                    @if($marketplaceTerm->payment_mode != 'free-download')
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.exclusive') }}</label>
                         <div class="col-md-9 control-label t-a-l">{{ __('term.exclusive_property.'.$marketplaceTerm->exclusivity) }}</div>
                    </div>
                    @endif
                    @if($marketplaceTerm->payment_mode == 'revenue-share')
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.revenue-proportion') }}</label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-5 business-terms-column">
                                    <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.content_provider') }} {{ $marketplaceTerm->revenue_share_cp }}  %</label>
                                </div>
                                <div class="col-md-5 business-terms-column">
                                    <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.service_provider') }} {{ $marketplaceTerm->revenue_share_sp }} %</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(in_array($marketplaceTerm->payment_mode, ['annual-download', 'monthly-download']))
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_update_quantity') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($marketplaceTerm->payment_mode == 'annual-download')
                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_year') }}</label>
                            @elseif($marketplaceTerm->payment_mode == 'monthly-download')
                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_month') }}</label>
                            @endif
                            {{ $marketplaceTerm->update_count }}&nbsp;&nbsp;{{ __('manage/cp/exchange/marketplace_terms.item') }}
                        </div>
                    </div>
                    @endif
                    @if(in_array($marketplaceTerm->payment_mode, ['charge-download', 'annual-download', 'monthly-download']))
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.content_price') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($marketplaceTerm->payment_mode == 'annual-download')
                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.per_year') }}</label>
                            @elseif($marketplaceTerm->payment_mode == 'monthly-download')
                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.per_month') }}</label>
                            @endif
                            {{ $marketplaceTerm->price }}&nbsp;&nbsp;￥
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.footnote') }}</label>
                        <div class="col-md-9 control-label t-a-l">{{ $marketplaceTerm->payment_comments ?? '-' }}</div>
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
                            @foreach ($marketplaceTerm->api_share_to as $item)
                                {{ __('term.api_share_to.'.$item) }} {{ $loop->last ? '' : ',' }}
                            @endforeach
                        </div>
                    </div>
                    @if($marketplaceTerm->payment_mode != 'revenue-share')
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_download') }}</label>
                        <div class="col-md-9 control-label t-a-l">
                            @if($marketplaceTerm->download_resolution)
                                @foreach ($marketplaceTerm->download_resolution as $item)
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

            </form>
        </div>
        </div>
    </div>
</div>
@stop
