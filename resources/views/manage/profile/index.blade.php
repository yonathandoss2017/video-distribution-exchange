@extends('partials.layout_home')

@push('title')
    {{ __('manage/user_profile.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
	<div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                {{ Form::open(['method'=>'POST', 'url'=>route('manage.profile.update'), 'class'=>'form-horizontal']) }}
                <div class="form">
                    <div class="title-header ">
                        <div class="title">{{ __('manage/user_profile.page_title') }}</div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/user_profile.personal_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <form method="get" class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/common.email') }}</label>
                                   <div class="col-md-9 control-label t-a-l">{{ Auth::user()->email }}</div>
                                </div>

								<div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/common.name') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required="">
                                    </div>
                                </div>

								<div class="row form-group{{ $errors->has('password') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/user_profile.password') }}</label>
                                      <div class="col-md-9">
                                        <input type="password" class="form-control{{ $errors->has('password') ? " form-control-danger" : "" }}" name="password" placeholder="{{ __('manage/user_profile.no_enter_if_no_change') }}">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'password'])
                                    </div>
                                </div>

								<div class="form-group row{{ $errors->has('confirm_password') ? " has-danger" : "" }}">
                                    <label class="col-md-3 control-label">{{ __('manage/user_profile.confirm_password') }}</label>
                                      <div class="col-md-9">
                                        <input type="password" class="form-control{{ $errors->has('confirm_password') ? " form-control-danger" : "" }}" name="confirm_password" placeholder="{{ __('manage/user_profile.no_enter_if_no_change') }}">
                                        @include('partials.errors', ['err_type' => 'field','field' => 'confirm_password'])
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="form-save">
                        <button type="submit" class="btn btn-primary">{{ __('manage/organization/common.update') }}</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop