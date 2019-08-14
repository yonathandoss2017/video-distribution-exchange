@extends('partials.layout_home')

@push('title')
    {{ __('manage/organization/property.new_property') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header ">
                        <div class="title">{{ __('manage/organization/property.new_property') }}</div>
                    </div>
                    {{ Form::open(['method'=>'POST', 'url'=>route('manage.property.store'), 'class'=>'form-horizontal']) }}
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/organization/property.property_information') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row{{ $errors->has('property_name') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/common.name') }}</label>
                                    <div class="col-md-9">
                                        <input class="form-control{{ $errors->has('property_name') ? " form-control-danger" : "" }}" required="true" placeholder="{{ __('manage/organization/property.property_name') }}" name="property_name" type="text" id="property_name" value="{{ old('property_name') }}">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'property_name'])
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/property.select_type') }}</label>
                                    <div class="col-md-9">
                                        <div class="custom-controls-stacked">
                                            <label class="custom-control custom-radio">
                                                <input id="radio1" name="property_type" type="radio" value="cp" class="custom-control-input" checked>
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">
											  <div class="content-description">
                                                  <div class="cp-title">{{ __('manage/organization/property.content_provider') }}</div>
                                                  {{ __('manage/organization/property.cp_Introduction') }}.
											  </div>
										  </span>
                                            </label>
                                            <label class="custom-control custom-radio">
                                                <input id="radio2" name="property_type" type="radio" value="sp" class="custom-control-input">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">
												<div class="content-description">
													<div class="cp-title text-blue">{{ __('manage/organization/property.service_provider') }}</div>
                                                    {{ __('manage/organization/property.sp_Introduction') }}.
												</div>
										  </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9" style="margin-top: 9px;">
                            </div>
                            <div class="col-md-3">
                                <div class="form-save">
                                    <button type="submit" class="btn btn-primary">{{ __('manage/organization/common.save') }}</button>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
@push('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
