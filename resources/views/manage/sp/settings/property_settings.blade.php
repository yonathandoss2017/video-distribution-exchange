@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/setting/property_settings.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                {{ Form::model($property, ['method'=>'POST', 'url'=>route('manage.sp.update.property_settings', $property_id), 'class'=>'form-horizontal', 'files' => true]) }}
                <div class="form">
                    <div class="title-header ">
                        <div class="title">{{ __('manage/sp/setting/property_settings.page_title') }}</div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/sp/setting/property_settings.property_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row{{ $errors->has('name') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label" for="website">{{ __('manage/sp/common.name') }}</label>
                                <div class="col-md-9">
                                    {{ Form::text(
                                        'name',
                                        $property->name,
                                        [
                                            'class' => 'form-control'.($errors->has('name') ? ' form-control-danger' : ''),
                                            'required' => "true",
                                            'placeholder' => __('manage/sp/setting/property_settings.property_name')
                                        ]
                                    ) }}
                                    @include('partials.errors', ['err_type' => 'field','field' => 'name'])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/setting/property_settings.sp_account_uuid') }}</label>
                                <div class="col-md-9 d-flex justify-content-between">
                                    {!! Form::text('uuid', null, ['id' => 'uuidField', 'class' => 'form-control', 'readonly' => true]) !!}
                                    <a href="#" class="btn btn-normal btn-m copy" style="margin-left: 15px" onclick="btnCopy('uuidField', event)">{{ __('manage/sp/common.copy') }}</a>
                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('country') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/settings/property_settings.country') }}</label>
                                <div class="col-md-9">
                                    {{ Form::select('country', $countries, null, ['class' => 'form-control', 'placeholder' => __('manage/cp/contents/playlists.select_a_country'), 'required']) }}
                                    @include('partials.errors', ['err_type' => 'field','field' => 'country'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('logoImage') ? ' has-danger' : '' }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/settings/property_settings.logo') }}</label>
                                <div class="col-md-9">
                                    <label class="custom-file">
                                        <input type="file" name="logoImage" class="imagefile" class="custom-file-input"/>
                                        <span class="custom-file-control"></span>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'logoImage'])
                                    </label>
                                    <div class="description">
                                        <small>
                                            * {{ __('manage/cp/settings/property_settings.supported_format') }}: .JPG, .JPEG, .PNG<br/>
                                            * {{ __('manage/cp/settings/property_settings.recommended_size') }}: 360 x 200 {{ __('common.px') }}, {{ __('manage/cp/settings/property_settings.file_size_less_than') }}1M
                                        </small>
                                    </div>
                                    <div class="creative-image">
                                        @if($property->logo_path)
                                            <img class="preview_image" src="{{ \App\Services\Serve\PropertyImageService::getImageUrl($property, 'logo') }}" alt="">
                                        @else
                                            <img class="preview_image" src="{{ url('images/property-logo-default.png') }}" alt="">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @can('super-admin')
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/sp/setting/property_settings.backend_settings') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/setting/property_settings.api_key') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $property->api_key ?? 'N/A' }}</div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/sp/setting/property_settings.api_token') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $property->api_token ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>

                <div class="form-save">
                    <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div id="tooltip-content" class="hidden">
        <style>
            .tooltip-inner {
                max-width: 700px;
                text-align: left;
            }
        </style>
        <span>{{ __('manage/sp/setting/property_settings.ad_display_frequency') }}</span><br>
        <span>{{ __('manage/sp/setting/property_settings.scenario') }}:</span><br>
        <span>{{ __('manage/sp/setting/property_settings.information_first') }}</span><br>
        <span>{{ __('manage/sp/setting/property_settings.information_second') }}</span><br>
        <span>{{ __('manage/sp/setting/property_settings.information_third') }}</span><br>
        <span>{{ __('manage/sp/setting/property_settings.information_fourth') }}</span>
    </div>
@stop
@push('js')
    <script type="text/javascript" src="/js/upload-image.js"></script>
    <script>
        $(document).ready(function() {
            $('#ad-display-frequency-info').tooltip({
                html: true,
                title: $("#tooltip-content").html()
            });
        });

        function btnCopy(elId, e) {
            e.preventDefault();

            /* Get the text field */
            var copyText = document.getElementById(elId);

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("Copy");
        }

        bindImageFileChange();
    </script>
@endpush
