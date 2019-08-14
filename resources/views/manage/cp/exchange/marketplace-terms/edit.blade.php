@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/marketplace_terms.edit_marketplace_terms') }}
@endpush

@php
    $jquery_in_head = true;
@endphp
@push('script-head')
<script src="/vendor/jquery/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="form">
                <div class="title-header ">
                    <a href="{{ route('manage.cp.exchange.marketplace-terms.index', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/marketplace_terms.back_to_marketplace_terms') }}</a>
                    <div class="title">{{ __('manage/cp/exchange/marketplace_terms.edit_marketplace_terms') }}</div>
                </div>
                {!! Form::open(['method' => 'PUT', 'route' => ['manage.cp.exchange.marketplace-terms.update', $property_id, $marketplaceTerm->id], 'class'=>'form-horizontal']) !!}
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/marketplace_terms.terms_summary') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row {{ $errors->has('name') ? " has-danger" : "" }}">
                            <label class="col-md-3 control-label">{{ __('common.name') }}*</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="name" value="{{ old('name', $marketplaceTerm->name) }}" tabindex="1" required>
                                @include('partials.errors', ['err_type' => 'field', 'field' => 'name'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/marketplace_terms.regional_rights') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row{{ $errors->has('allowed_regions') ? " has-danger" : "" }}">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.allowed_regions') }}*</label>
                            <div class="col-md-9">
                                <select class="form-control select2" id="allowed_regions" multiple name="allowed_regions[]" required></select>
                                @include('partials.errors', ['err_type' => 'field','field' => 'allowed_regions'])
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('same_regions') || $errors->has('contain_regions') ? " has-danger" : "" }}">
                            <label class="col-md-3 control-label">{{ __('common.except') }}</label>
                            <div class="col-md-9">
                                <select class="form-control select2" id="excepted_regions" multiple name="excepted_regions[]"></select>
                                @include('partials.errors', ['err_type' => 'field','field' => 'same_regions'])
                                @include('partials.errors', ['err_type' => 'field','field' => 'contain_regions'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/marketplace_terms.payment_model') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row{{ $errors->has('payment_mode') ? " has-danger" : "" }}">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.payment_mode') }}*</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="payment_mode" value="revenue-share" class="custom-control-input" required onclick="showPaymentDetail()" {{ 'revenue-share' == old('payment_mode', $marketplaceTerm->payment_mode) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.payment_mode.revenue-share') }}</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="payment_mode" value="charge-download" class="custom-control-input" required onclick="showPaymentDetail()" {{ 'charge-download' == old('payment_mode', $marketplaceTerm->payment_mode) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.payment_mode.charge-download') }}</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="payment_mode" value="annual-download" class="custom-control-input" required onclick="showPaymentDetail()" {{ 'annual-download' == old('payment_mode', $marketplaceTerm->payment_mode) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.payment_mode.annual-download') }}</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="payment_mode" value="monthly-download" class="custom-control-input" required onclick="showPaymentDetail()" {{ 'monthly-download' == old('payment_mode', $marketplaceTerm->payment_mode) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.payment_mode.monthly-download') }}</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="payment_mode" value="free-download" class="custom-control-input" required onclick="showPaymentDetail()" {{ 'free-download' == old('payment_mode', $marketplaceTerm->payment_mode) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.payment_mode.free-download') }}</span>
                                        </label>
                                    </div>
                                </div>
                                @include('partials.errors', ['err_type' => 'field','field' => 'payment_mode'])
                            </div>
                        </div>
                        <div class="payment_detail" style="margin-bottom: 20px">
                            <div class="form-group hidden revenue-share charge-download annual-download monthly-download row{{ $errors->has('exclusivity') ? " has-danger" : "" }}">
                                <div class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.exclusive') }}*</div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="exclusivity" value="exclusive" {{ 'exclusive' == old('exclusivity', $marketplaceTerm->exclusivity) ? 'checked' : '' }} class="custom-control-input" required>
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">{{ __('common.yes') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="custom-control custom-radio">
                                                <input type="radio" name="exclusivity" value="non-exclusive" {{ 'non-exclusive' == old('exclusivity', $marketplaceTerm->exclusivity) ? 'checked' : '' }} class="custom-control-input" required>
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">{{ __('common.no') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'exclusivity'])
                                </div>
                            </div>
                            <div class="form-group hidden revenue-share row{{ ($errors->has('revenue_share_cp') || $errors->has('revenue_share_sp')) ? " has-danger" : "" }}" id="revenue_share">
                                <div class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.revenue-proportion') }}*</div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-5 business-terms-column">
                                            <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.content_provider') }}</label>
                                            <input class="form-control short-3 revenue_share" name="revenue_share_cp" value="{{ old('revenue_share_cp', $marketplaceTerm->revenue_share_cp) }}" type="number" min="0" max="100" required>
                                            <label class="control-label t-a-l business-terms-label">%</label>
                                        </div>
                                        <div class="col-md-5 business-terms-column">
                                            <label class="control-label t-a-l m-r">{{ __('manage/cp/exchange/marketplace_terms.service_provider') }}</label>
                                            <input class="form-control short-3 revenue_share" name="revenue_share_sp" value="{{ old('revenue_share_sp', $marketplaceTerm->revenue_share_sp) }}" type="number" min="0" max="100" required>
                                            <label class="control-label t-a-l business-terms-label">%</label>
                                        </div>
                                    </div>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'revenue_share_cp'])
                                    @include('partials.errors', ['err_type' => 'field','field' => 'revenue_share_sp'])
                                </div>
                            </div>
                            <div class="form-group hidden annual-download monthly-download row{{ $errors->has('update_count') ? " has-danger" : "" }}" id="update_count">
                                <div class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_update_quantity') }}*</div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-5 business-terms-column">
                                            <label class="control-label t-a-l m-r tip annual-download hidden">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_year') }}</label>
                                            <label class="control-label t-a-l m-r tip monthly-download hidden">{{ __('manage/cp/exchange/marketplace_terms.not_less_than_per_month') }}</label>
                                            <input class="form-control short-3" name="update_count" value="{{ old('update_count', $marketplaceTerm->update_count) }}" type="number" min="0" required>
                                            <label class="control-label t-a-l business-terms-label">{{ __('manage/cp/exchange/marketplace_terms.item') }}</label>
                                        </div>
                                    </div>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'update_count'])
                                </div>
                            </div>
                            <div class="form-group hidden charge-download annual-download monthly-download row{{ $errors->has('price') ? " has-danger" : "" }}" id="price">
                                <div class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.content_price') }}*</div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-5 business-terms-column">
                                            <label class="control-label t-a-l m-r tip annual-download hidden">{{ __('manage/cp/exchange/marketplace_terms.per_year') }}</label>
                                            <label class="control-label t-a-l m-r tip monthly-download hidden">{{ __('manage/cp/exchange/marketplace_terms.per_month') }}</label>
                                            <input class="form-control short-3" name="price" value="{{ old('price', $marketplaceTerm->price) }}" type="number" min="0" required>
                                            <label class="control-label t-a-l business-terms-label">￥</label>
                                        </div>
                                    </div>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'price'])
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.footnote') }}</label>
                            <div class="col-md-9">
                                <textarea class="form-control-area" name="payment_comments" id="footnote" rows="3">{{ old('payment_comments', $marketplaceTerm->payment_comments) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/marketplace_terms.distribution_model') }}*</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row{{ $errors->has('api_share_to') ? " has-danger" : "" }}" style="margin-bottom: 3px">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.api_distribution') }}</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="custom-control custom-checkbox lt">
                                            <input type="checkbox" name="api_share_to[]" value="qq" class="custom-control-input api_share_to" {{ in_array('qq', old('api_share_to', $marketplaceTerm->api_share_to)) ? 'checked' : '' }} required>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.api_share_to.qq') }}</span>
                                        </label>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="custom-control custom-checkbox lt">
                                            <input type="checkbox" name="api_share_to[]" value="headlines" class="custom-control-input api_share_to" {{ in_array('headlines', old('api_share_to', $marketplaceTerm->api_share_to)) ? 'checked' : '' }} required>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.api_share_to.headlines') }}</span>
                                        </label>
                                    </div>
                                </div>
                                @include('partials.errors', ['err_type' => 'field','field' => 'api_share_to'])
                            </div>
                        </div>
                        <div class="form-group row{{ $errors->has('download_resolution') ? " has-danger" : "" }}" id="video_download" {{ (!old('payment_mode') || old('payment_mode') == 'revenue-share') ? 'hidden' : ((!old('payment_mode') && $marketplaceTerm->payment_mode == 'revenue-share') ? 'hidden' : '') }}>
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/marketplace_terms.video_download') }}</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="custom-control custom-checkbox lt">
                                            <input type="checkbox" name="download_resolution[]" value="hd" class="custom-control-input download_resolution" {{ in_array('hd', old('download_resolution', $marketplaceTerm->download_resolution)) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.video_download.hd') }}</span>
                                        </label>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="custom-control custom-checkbox lt">
                                            <input type="checkbox" name="download_resolution[]" value="low" class="custom-control-input download_resolution" {{ in_array('low', old('download_resolution', $marketplaceTerm->download_resolution)) ? 'checked' : '' }}>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('term.video_download.low') }}</span>
                                        </label>
                                    </div>
                                </div>
                                @include('partials.errors', ['err_type' => 'field','field' => 'download_resolution'])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-save"><button type="submit" class="btn btn-primary">{{ __('common.save') }}</button></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    var selectRegion = '{{ __('manage/cp/exchange/marketplace_terms.select_region') }}';
    var allowedRegion = {!! old('allowed_regions') ? json_encode(old('allowed_regions')) : json_encode($marketplaceTerm->region_allowed) !!};
	var exceptedRegion = {!! old('excepted_regions') ? json_encode(old('excepted_regions')) : json_encode($marketplaceTerm->region_excepted) !!};
    var regions = {!! json_encode($regions) !!};
    showPaymentDetail('{{ old('payment_mode', $marketplaceTerm->payment_mode) }}');
    function showPaymentDetail(id) {
        if (!id) {
            id = $("input[name='payment_mode']:checked").val();
        }
        if (id == "revenue-share") {
            $("#video_download").prop("hidden", true).siblings(".form-group").css("margin-bottom","3px")
            $('.download_resolution').prop("required", false)
            if (is_checked_for_api_share_to()) {
                $('.api_share_to').prop("required", false)
            }
        } else {
            $("#video_download").prop("hidden", false).siblings(".form-group").css("margin-bottom","20px")
            if (typeof $("#video_download").find('input[type="checkbox"]:checked').val() === 'undefined') {
                if (is_checked_for_api_share_to() || is_checked_for_download_resolution()) {
                    $('.api_share_to').prop("required", false)
                    $('.download_resolution').prop("required", false)
                } else {
                    $('.api_share_to').prop("required", true)
                    $('.download_resolution').prop("required", true)
                }
            }
        }
        $('.payment_detail').find('.form-group').addClass('hidden');
        $('.payment_detail').find('label.tip').addClass('hidden');
        $('.payment_detail').find('input[type="radio"]').prop("required", false)
        $('.payment_detail').find('input[type="number"]').prop("required", false)
        $('.payment_detail .'+id).removeClass('hidden');
        $('.payment_detail .'+id).find('input[type="radio"]').prop("required", true)
        $('.payment_detail .'+id).find('input[type="number"]').prop("required", true)
    }
    function is_checked_for_api_share_to() {
        var isChecked = false
        $('.api_share_to').each(function () {
            if (this.checked) {
                isChecked = true
            }
        })
        return isChecked
    }
    function is_checked_for_download_resolution() {
        var isChecked = false
        $('.download_resolution').each(function () {
            if (this.checked) {
                isChecked = true
            }
        })
        return isChecked
    }
    $(function () {
        $('.api_share_to').click(function () {
            if (!is_checked_for_api_share_to() && !is_checked_for_download_resolution()) {
                $('.api_share_to').prop("required", true)
            } else{
                $('.api_share_to').prop("required", false)
                $('.download_resolution').prop("required", false)
            }
        });
        $('.download_resolution').click(function () {
            if (!is_checked_for_api_share_to() && !is_checked_for_download_resolution()) {
                $('.download_resolution').prop("required", true)
                $('.api_share_to').prop("required", true)
            } else {
                $('.download_resolution').prop("required", false)
                $('.api_share_to').prop("required", false)
            }
        });
        $('.revenue_share').keyup(function () {
            var inpVal = $(this).val();
            if (inpVal < 0) inpVal = 0;
            if (inpVal > 100) inpVal = 100;
            $(this).val(inpVal);
            $(this).parent().siblings().find('input').val(100 - inpVal);
        });
    });
</script>
<script src="{{ asset('js/exchange/filters-allowed-selection.js') }}"></script>
@endpush
