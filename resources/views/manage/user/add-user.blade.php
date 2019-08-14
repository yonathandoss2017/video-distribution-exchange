@extends('partials.layout_home')


@push('title')
    {{ __('manage/organization/user.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header ">
                        <div class="title">{{ __('manage/organization/user.new_user') }}</div>
                    </div>
                    {{ Form::open(['method'=>'POST', 'url'=>route('manage.user.store'), 'class'=>'form-horizontal']) }}
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/organization/user.user_information') }}</h5>
                        </div>
                        <div class="ibox-content">

                            <div class="form-group row{{ $errors->has('email') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/organization/common.email') }}</label>
                                <div class="col-md-9">
                                    <input type="email" class="form-control form-control-danger" name="email" value="{{ old('email') }}" required="">
                                    @if($errors->has('email'))
                                        <div class="form-control-feedback">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <div class="loader-width hidden">
                                    <div class="loader"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/organization/common.name') }}</label>
                                <div class="col-md-9">
                                    <input type="hidden" class="hidden-name" name="name" disabled/>
                                    <input type="text" class="form-control" name="name" required="">
                                </div>
                            </div>

                                <div class="form-group row">
                                    <label class="col-md-3 control-label">{{ __('manage/organization/user.organization_admin') }}</label>
                                    <div class="col-md-9">
                                        <label class="custom-control custom-radio">
                                            {{ Form::radio('is_admin','yes',false, ['class' => 'custom-control-input']) }}
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">{{ __('manage/organization/common.yes') }}</span>
                                        </label>
                                        <label class="custom-control custom-radio">
                                            {{ Form::radio('is_admin','no',true, ['class' => 'custom-control-input']) }}
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
                                            <a href="#">{{ $property -> name }}
                                                @if($property->type == 'sp_plus')
                                                <small class="label sp-plus">{{ $types[$property->type] }}</small>
                                                @else
                                                <small class="label {{ $property->type }}">{{ $types[$property->type] }}</small>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="playlist-actions">
                                            @if($property->type == \App\Models\Property::TYPE_CP)
                                                {{ Form::select($property->id.'-role', $cpRoles, null, ['class' => 'custom-select']) }}
                                            @else
                                                {{ Form::select($property->id.'-role', $spRoles, null, ['class' => 'custom-select']) }}
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
                        <button type="submit" class="btn btn-primary btn-submit">{{ __('manage/organization/common.save') }}</button>
                    </div>
                    {{ Form::close() }}
                </div>
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

        var inputEmail = $('input[name=email]');
        var inputName = $('input[name=name]');
        var hiddenName = $('.hidden-name');
        var loader = $('.loader-width');
        var checkEmailUrl = '{{ route('check-email') }}';
        inputEmail.keyup(function () {
            inputName.prop('disabled', false);
            hiddenName.prop('disabled', true);
            loader.removeClass('hidden');
            var inputEmailVal = inputEmail.val();
            if (inputEmailVal.length > 3) {
                delay(function () {
                    $.ajax({
                        url: checkEmailUrl,
                        type: 'GET',
                        data: { email : inputEmailVal },
                        success: function (data, status) {
                            console.log(data.status);
                            if (data.status) {
                                inputName.val(data.data.name);
                                hiddenName.val(data.data.name);
                                inputName.prop('disabled', true);
                                hiddenName.prop('disabled', false);
                            } else {
                                inputName.prop('disabled', false);
                                hiddenName.prop('disabled', true);
                            }
                            loader.addClass('hidden');
                        },
                        error: function (xhr, desc, err) {
                            console.log(xhr);
                            console.log("Details: " + desc + "\nError:" + err);
                            loader.addClass('hidden');
                        }
                    });
                }, 1000);
            }
        });

        var delay = (function(){
            var timer = 0;
            return function(callback, ms){
                clearTimeout (timer);
                timer = setTimeout(callback, ms);
            };
        })();

    })();
</script>
@endpush
