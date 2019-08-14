@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.regions_distribution') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.cp.exchange.distribution.show', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.back_to_terms_of_distribution') }}</a>
                        <div class="title">{{ __('manage/cp/exchange/distribution.regions_distribution') }}</div>
                    </div>
                    <form method="get" class="form-horizontal">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/exchange/distribution.regional_rights') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.allowed_regions') }}</label>
                                    <div class="col-md-9 control-label t-a-l">
                                        @foreach ($distribution_region->allowed_regions as $region)
                                            {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('common.except') }}</label>
                                    <div class="col-md-9 control-label t-a-l">
                                        @if($distribution_region->excepted_regions)
                                            @foreach ($distribution_region->excepted_regions as $region)
                                                {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.availabilty_period') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $distribution_region->started_at ? $distribution_region->started_at->format('M j, Y') : '' }} - {{ $distribution_region->ended_at ? $distribution_region->ended_at->format('M j, Y') : '' }}</div>
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
                                    <div class="col-md-9 control-label t-a-l">{{ $distribution_region->payment_mode ? __('term.payment_mode.'.$distribution_region->payment_mode) : '-' }}</div>
                                </div>
                                @if($distribution_region->payment_mode && $distribution_region->payment_mode != 'free-download')
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.exclusive') }}</label>
                                        <div class="col-md-9 control-label t-a-l">{{ __('term.exclusive_property.'.$distribution_region->exclusivity) }}</div>
                                    </div>
                                @endif
                                @if($distribution_region->payment_mode == 'revenue-share')
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.revenue-proportion') }}</label>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-5 business-terms-column">
                                                    <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.content_provider') }} {{ $distribution_region->revenue_share_cp }}  %</label>
                                                </div>
                                                <div class="col-md-5 business-terms-column">
                                                    <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.service_provider') }} {{ $distribution_region->revenue_share_sp }} %</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(in_array($distribution_region->payment_mode, ['annual-download', 'monthly-download']))
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_update_quantity') }}</label>
                                        <div class="col-md-9 control-label t-a-l">
                                            @if($distribution_region->payment_mode == 'annual-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_year') }}</label>
                                            @elseif($distribution_region->payment_mode == 'monthly-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_month') }}</label>
                                            @endif
                                            {{ $distribution_region->update_count }}&nbsp;&nbsp;{{ __('manage/cp/exchange/marketplace_terms.item') }}
                                        </div>
                                    </div>
                                @endif
                                @if(in_array($distribution_region->payment_mode, ['charge-download', 'annual-download', 'monthly-download']))
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.content_price') }}</label>
                                        <div class="col-md-9 control-label t-a-l">
                                            @if($distribution_region->payment_mode == 'annual-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.per_year') }}</label>
                                            @elseif($distribution_region->payment_mode == 'monthly-download')
                                                <label class="control-label t-a-l m-r" style="padding-top: 0px;">{{ __('manage/cp/exchange/marketplace_terms.per_month') }}</label>
                                            @endif
                                            {{ $distribution_region->price }}&nbsp;&nbsp;￥
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.footnote') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $distribution_region->payment_comments ?? '-' }}</div>
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
                                        @if($distribution_region->api_share_to)
                                            @foreach ($distribution_region->api_share_to as $item)
                                                {{ __('term.api_share_to.'.$item) }} {{ $loop->last ? '' : ',' }}
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                @if($distribution_region->payment_mode != 'revenue-share')
                                    <div class="form-group row">
                                        <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_download') }}</label>
                                        <div class="col-md-9 control-label t-a-l">
                                            @if($distribution_region->download_resolution)
                                                @foreach ($distribution_region->download_resolution as $item)
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
                                <h5>{{ __('manage/cp/exchange/distribution.custom_terms') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.extra_terms') }}</label>
                                    <div class="col-md-9">
                                        <p class="form-control-static">
                                            {!! $distribution_region->extra_terms ? nl2br($distribution_region->extra_terms) : '-' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop