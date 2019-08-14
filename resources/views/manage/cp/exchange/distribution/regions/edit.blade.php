@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.add_regions') }}
@endpush

@php
    $jquery_in_head = true;
@endphp
@push('script-head')
    <script src="/vendor/jquery/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header ">
                        <a href="{{ route('manage.cp.exchange.distribution.edit', [$property_id, $id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.back_to_terms_of_distribution') }}</a>
                        <div class="title">{{ __('manage/cp/exchange/distribution.add_regions') }}</div>
                    </div>
                    {!! Form::open(['route' => ['manage.cp.exchange.distribution.regions.update', $property_id, $id, $distribution_region->id], 'method' => 'PUT', 'class'=>'form-horizontal']) !!}
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/exchange/marketplace_terms.marketplace_terms') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.terms_name') }}</label>
                                    <div class="col-md-9">
                                        <select class="form-control" name="tom_id" onchange="getMarketplaceTerm(this)">
                                            <option value="">{{ __('manage/cp/contents/playlists.select_a_marketplace_terms') }}</option>
                                            @foreach($terms as $index => $term) <option value="{{ $index }}">{{ $term }}</option> @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="term_info">
                            @include('manage.cp.exchange.distribution.regions.term', ['marketplaceTerm' => $distribution_region, 'isEdit' => true, 'show_date' => true])
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/exchange/distribution.custom_terms') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.extra_terms') }}</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control-area" name="extra_terms" id="extra_terms" rows="8">{{ old('extra_terms') ? old('extra_terms') : $distribution_region->extra_terms }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/exchange/distribution.save_to_marketplace_terms') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.save_or_not') }}</label>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" name="save_to_marketplace_terms" value="1" {{ old('save_to_marketplace_terms') == 1 ? 'checked' : '' }} class="custom-control-input save_terms">
                                                    <span class="custom-control-indicator"></span>
                                                    <span class="custom-control-description">{{ __('common.yes') }}</span>
                                                </label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="custom-control custom-radio">
                                                    <input type="radio" name="save_to_marketplace_terms" value="0" {{ old('save_to_marketplace_terms') == 0 ? 'checked' : '' }} class="custom-control-input save_terms">
                                                    <span class="custom-control-indicator"></span>
                                                    <span class="custom-control-description">{{ __('common.no') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row {{ old('save_to_marketplace_terms') == 1 ? '' : 'hidden' }} {{ $errors->has('tom_name') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.new_terms_name') }}*</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="tom_name" value="{{ old('tom_name') }}" tabindex="1">
                                        @include('partials.errors', ['err_type' => 'field', 'field' => 'tom_name'])
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
        var request_url = "{{ route('manage.cp.exchange.distribution.regions.marketplace_term', [$property_id, $id]) }}";
        function getMarketplaceTerm(obj)
        {
            var selVal = $(obj).val();
            $.ajax({
                url : request_url + "?term=" + selVal,
                dataType: 'html',
            }).done(function (data) {
                $('#term_info').html(data);
            }).fail(function () {
            });
        }
        $(function () {
            $('.save_terms').click(function () {
                if (this.checked && this.value == 1) {
                    $(this).parents('.form-group').siblings().removeClass('hidden')
                    $(this).parents('.form-group').siblings().find('input').prop('required', true)
                } else {
                    $(this).parents('.form-group').siblings().addClass('hidden')
                    $(this).parents('.form-group').siblings().find('input').prop('required', false)
                }
            })
        })
    </script>
@endpush
