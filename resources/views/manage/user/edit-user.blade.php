@extends('partials.layout_home')


@push('title')
    {{ __('manage/organization/user.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="title-header ">
                    <div class="min-menu">
                        <div class="title">{{ __('manage/organization/user.update_access') }}</div>
                    </div>
                </div>
                {{ Form::model($user, ['method'=>'PATCH', 'url'=>route('manage.user.update', $user->id), 'class'=>'form-horizontal']) }}
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/organization/user.user_information') }}</h5>
                    </div>
                    <div class="ibox-content">
                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/organization/common.name') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $user -> name }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/organization/common.email') }}</label>
                                <div class="col-md-9 control-label t-a-l">{{ $user -> email }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/organization/user.organization_admin') }}</label>
                                <div class="col-md-9">
                                    <label class="custom-control custom-radio">
                                        {{ Form::radio('is_admin','yes',$isAdmin == true ? true: false, ['class' => 'custom-control-input']) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('manage/organization/common.yes') }}</span>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        {{ Form::radio('is_admin','no',$isAdmin == false ? true: false, ['class' => 'custom-control-input']) }}
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">{{ __('manage/organization/common.no') }}</span>
                                    </label>
                                </div>
                            </div>
                    </div>
                </div>

                <div class="ibox" id="dataTables-user">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>{{ __('manage/organization/user.property') }}</th>
                                    <th>{{ __('manage/organization/user.access_level') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($properties as $property)
                                    <tr>
                                        <td class="playlist-title pl">
                                            <a href="#">{{ $property->name }}
                                                @if($property->type == 'sp_plus')
                                                    <small class="label sp-plus">{{ $types[$property->type] }}</small>
                                                @else
                                                    <small class="label {{ $property->type }}">{{ $types[$property->type] }}</small>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="playlist-actions">
                                            @php
                                                $select = '0';
                                                foreach($property->roles as $role) {
                                                    $select = $role->id;
                                                    break;
                                                }
                                            @endphp
                                            @if($property->type == \App\Models\Property::TYPE_CP)
                                                {{ Form::select($property->id.'-role', $cpRoles, $select, ['class' => 'custom-select']) }}
                                            @else
                                                {{ Form::select($property->id.'-role', $spRoles, $select, ['class' => 'custom-select']) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-save">
                    <button type="submit" class="btn btn-primary">{{ __('manage/organization/common.save') }}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    (function (){
        var showHideTable = function() {
            if ($("input[name=is_admin]:checked").val() == "yes") {
                $("#dataTables-user").addClass("hidden");
            }else{
                $("#dataTables-user").removeClass("hidden");
            }
        };
        $("input[name=is_admin]:radio").change(function(){
            showHideTable();
        });
        showHideTable();
    })();
</script>
@endpush
