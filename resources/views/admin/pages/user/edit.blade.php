@extends('admin.layout')
@push('title') {{ __('admin/sidebar.users') }} | {{ __('app.title') }} @endpush

@section('content')

    <div class="row justify-content-center">

            <header class="title-header col-md-9">
                <h3 class="title">{{ $user->name }}</h3>
            </header>

            <div class="col-md-9">
                <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.users') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __('admin/common.edit') }}</strong></p>
            </div><!-- .col-* -->

            <div class="col-md-9">

                {{ Form::open(['url' => route('admin.user.update', $user->id), 'method' => 'POST', 'name'=>'form_user_creation', 'class' => 'form-horizontal']) }}
                {{ method_field('PUT') }}
                <div class="ibox">

                    <div class="ibox-title">
                        <h5>{{ __('admin/user.user_information') }}</h5>
                    </div>

                    <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('admin/common.name') }}*</label>
                                <div class="col-md-9">
                                    <input type="text" name="name" value="{{ $user->name }}" class="form-control" required="">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'name'])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('admin/common.email') }}*</label>
                                <div class="col-md-9">
                                    <input type="text" value="{{ $user->email}}" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('password') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/common.password') }}</label>
                                <div class="col-md-9">
                                    <input type="password" name="password" class="form-control">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'password'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('password_confirmation') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/user.re_type_password') }}</label>
                                <div class="col-md-9">
                                    <input type="password" name="password_confirmation" class="form-control">
                                    @include('partials.errors', ['err_type' => 'field','field' => 'password_confirmation'])
                                </div>
                            </div>
                            <div class="form-group row{{ $errors->has('role') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('admin/user.system_role') }}</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="role">
                                        <option value="" {{ $user_system_role_id ? 'disabled' : '' }}>{{ __('admin/user.normal_user') }}</option>
                                        @foreach($system_roles as $role)
                                            <option value="{{ $role->id }}" {{ ( (old("role") == $role->id || $user_system_role_id == $role->id) ? "selected":"") }}>{{ __('admin/user.'.$role->name) }}</option>
                                        @endforeach
                                    </select>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'role'])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('admin/common.status') }}</label>
                                <div class="col-md-9">
                                    <label class="custom-control custom-radio">
                                        <input name="is_active" value="1" type="radio" class="custom-control-input" {{ !is_null($user->activated_at) && !$user->is_disabled ? 'checked' : '' }}>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('admin/common.active') }}</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input name="is_active" value="0" type="radio" class="custom-control-input" {{ is_null($user->activated_at) && !$user->is_disabled ? 'checked' : '' }}>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('admin/common.inactive') }}</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input name="is_active" value="2" type="radio" class="custom-control-input" {{ $user->is_disabled ? 'checked' : '' }}>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('admin/common.disable') }}</span>
                                    </label>
                                </div>
                            </div>

                    </div><!-- .ibox-content -->

                </div><!-- .ibox -->

                <div class="ibox" id="dataTables-user">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>{{ __('admin/user.property') }}</th>
                                    <th>{{ __('admin/common.organization') }}</th>
                                    <th>{{ __('admin/common.role') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($user->roles)
                                    @foreach($user->roles as $role)
                                        <tr>
                                            <td class="playlist-title">
                                                <a href="#">—</a>
                                            </td>
                                            <td class="playlist-title">
                                                <a href="#">—</a>
                                            </td>
                                            <td class="playlist-title">
                                                {{ __('admin/user.'.$role->name) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if($user->organizations)
                                    @foreach($user->organizations as $organization)
                                        <tr>
                                            <td class="playlist-title">
                                                <a href="#">—</a>
                                            </td>
                                            <td class="playlist-title">
                                                <a href="#">{{ $organization->name }}</a>
                                            </td>
                                            <td class="playlist-title">
                                                {{ __('admin/user.'.\App\Models\Role::ORGANIZATION_ADMIN) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if($user->properties)
                                    @foreach($user->properties as $property)
                                        <tr>
                                            <td class="playlist-title">
                                                <a href="#">{{ $property->name }}
                                                    @if($property->type == 'sp_plus')
                                                        <small class="label sp-plus">{{ $types[$property->type] }}</small>
                                                    @else
                                                        <small class="label {{ $property->type }}">{{ $types[$property->type] }}</small>
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="playlist-title">
                                                <a href="#">{{ optional($property->organization)->name }}</a>
                                            </td>
                                            <td class="playlist-title">
                                                @if(optional($property->roles))
                                                    {{ __('admin/user.'.optional($property->roles)[0]->name) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-save">
                    {{ Form::submit(__('admin/common.update'), ['class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}
            </div><!-- .col* -->

        </div><!-- .row -->

@stop
