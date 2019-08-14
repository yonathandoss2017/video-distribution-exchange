@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/settings/property_settings.property_settings') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="form">
                    <div class="title-header ">
                        <div class="title">{{ __('manage/cp/settings/property_settings.property_settings') }}</div>
                    </div>

                    {{ Form::model($property, ['method'=>'POST', 'url'=>route('manage.cp.update', $property->id), 'class'=>'form-horizontal', 'files' => true]) }}
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/settings/property_settings.property_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row{{ $errors->has('name') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label" for="name">{{ __('common.name') }}</label>
                                <div class="col-md-9">
                                    {{ Form::text(
                                        'name',
                                        null,
                                        [
                                            'class' => 'form-control'.($errors->has('name') ? ' form-control-danger' : ''),
                                            'required' => "false",
                                            'placeholder' => __('manage/cp/settings/property_settings.property_name')
                                        ]
                                    ) }}
                                    @include('partials.errors', ['err_type' => 'field','field' => 'name'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('description') ? " has-danger" : "" }}"">
                                <label class="col-md-3 control-label" for="description">{{ __('manage/cp/settings/property_settings.descriptions') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control-area" id="exampleTextarea" name="description" rows="3">{{ $property->description }}</textarea>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'description'])
                                </div>
                            </div>
                            <div class="form-group row {{ $errors->has('country') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/settings/property_settings.country') }}</label>
                                <div class="col-md-9">
                                    {{ Form::select('country', $countries, null, ['class' => 'form-control', 'placeholder' => __('manage/cp/contents/playlists.select_a_country'), 'required']) }}
                                    @include('partials.errors', ['err_type' => 'field','field' => 'country'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('logoImage') ? ' has-danger' : '' }} img-direct-upload">
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
                            <div class="form-group row{{ $errors->has('featureImage') ? ' has-danger' : '' }} img-direct-upload">
                                <label class="col-md-3 control-label">{{ __('manage/cp/settings/property_settings.featured_image') }}</label>
                                <div class="col-md-9">
                                    <label class="custom-file">
                                        <input type="file" name="featureImage" class="imagefile" class="custom-file-input">
                                        <span class="custom-file-control"></span>
                                        @include('partials.errors', ['err_type' => 'field','field' => 'featureImage'])
                                    </label>
                                    <div class="description">
                                        <small>
                                            * {{ __('manage/cp/settings/property_settings.supported_format') }}: .JPG, .JPEG, .PNG<br>
                                            * {{ __('manage/cp/settings/property_settings.recommended_size') }}: 720 x 400 px, {{ __('manage/cp/settings/property_settings.file_size_less_than') }}2M
                                        </small>
                                    </div>
                                    <div class="creative-image">
                                        <img class="preview_image" src="{{ \App\Services\Serve\PropertyImageService::getImageUrl($property, 'feature') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @can('super-admin')
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>{{ __('manage/cp/settings/property_settings.backend_settings') }}</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/settings/property_settings.api_key') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $property->api_key ?? 'N/A' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/cp/settings/property_settings.api_token') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ $property->api_token ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>

                <div class="form-save"><button type="submit" class="btn btn-primary">{{ __('common.save') }}</button></div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
@stop
@push('js')
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/js/ie10-viewport-bug-workaround.js"></script>
    <script src="/js/scripts.js?v=3"></script>
    <script type="text/javascript" src="/js/upload-image.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
        bindImageFileChange();
    </script>
@endpush
