@extends('partials.layout_home')

@push('title')
    {{ __('manage/organization/settings.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
	<div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                {{ Form::open(['method'=>'POST', 'url'=>route('manage.settings.update'), 'class'=>'form-horizontal']) }}
                <div class="form">
                    <div class="title-header ">
                        <div class="title">{{ __('manage/organization/settings.page_title') }}</div>
                    </div>
                    <!-- Organization information -->
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/organization/settings.organization_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <form method="get" class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/common.name') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ ucwords($orgName) }}</div>
                                </div>

								<div class="form-group row{{ $errors->has('address') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/common.address') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" name="address" class="form-control{{ $errors->has('address') ? " form-control-danger" : "" }}" value="{{ old('address', ucwords($orgAddress)) }}" required="">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'address'])
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/common.country') }}</label>
                                    <div class="col-md-9 control-label t-a-l">{{ ucwords($orgCountry) }}</div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- end of organization information -->

                    <div class="form-save">
                            <button type="submit" class="btn btn-primary">{{ __('manage/organization/common.save') }}</button>
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
