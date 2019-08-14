@extends('admin.layout')
@push('title') {{ __('admin/sidebar.users') }} | {{ __('app.title') }} @endpush

@section('content')

    <div class="row justify-content-center">

            <header class="title-header col-md-9">
                <h3 class="title">{{ __('admin/user.new_user') }}</h3>
            </header>

            <div class="col-md-9">
                <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.users') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __('admin/user.new_user') }}</strong></p>
            </div><!-- .col-* -->

            <div class="col-md-9">

                {{ Form::open(['url' => route('admin.user.store'), 'method' => 'POST', 'name'=>'form_user_creation', 'class' => 'form-horizontal']) }}

                <div class="ibox">

                    <div class="ibox-title">
                        <h5>{{ __('admin/user.user_information') }}</h5>
                    </div>

                    <div class="ibox-content">


                            <div class="form-group row{{ $errors->has('email') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/common.email') }}*</label>
                                <div class="col-md-9">
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required="">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'email'])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('admin/common.name') }}*</label>
                                <div class="col-md-9">
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required="">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'name'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('password') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/common.password') }}*</label>
                                <div class="col-md-9">
                                    <input type="password" name="password" class="form-control" required="">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'password'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('password_confirmation') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/user.re_type_password') }}*</label>
                                <div class="col-md-9">
                                    <input type="password" name="password_confirmation" class="form-control" required="">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'password_confirmation'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('role') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/user.system_role') }}*</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="role">
                                        <option value="" disabled="">{{ __('admin/user.normal_user') }}</option>
                                        @foreach($system_roles as $role)
                                            <option value="{{ $role->id }}" {{ (old("role") == $role->id ? "selected":"") }}>{{ __('admin/user.'.$role->name) }}</option>
                                        @endforeach
                                    </select>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'role'])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('admin/common.status') }}</label>
                                <div class="col-md-9">
                                    <label class="custom-control custom-radio">
                                        <input name="is_active" value="1" type="radio" {{ old('is_active') == 1 ? 'checked' : '' }} class="custom-control-input">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('admin/common.active') }}</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input name="is_active" value="0" type="radio" class="custom-control-input" {{ (old('is_active') == 0 || empty(old('is_active'))) ? 'checked' : '' }}>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('admin/common.inactive') }}</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input name="is_active" value="2" type="radio" class="custom-control-input" {{ (old('is_active') == 2 || empty(old('is_active'))) ? 'checked' : '' }}>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('admin/common.disable') }}</span>
                                    </label>
                                </div>
                            </div>

                    </div><!-- .ibox-content -->

                </div><!-- .ibox -->

                <div class="form-save">
                    {{ Form::submit(__('admin/common.new'), ['class' => 'btn btn-primary']) }}
                </div>

                {{ Form::close() }}

            </div><!-- .col* -->

        </div><!-- .row -->

@stop
