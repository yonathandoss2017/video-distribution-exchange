@extends('admin.layout')
@push('title') {{ __( 'admin/organization.organizations') }} | {{ __('app.title') }} @endpush
@section('content')
<div class="row justify-content-center">

    <header class="title-header col-md-9">
        <h3 class="title">{{ $organization->name }}</h3>
    </header>

    <div class="col-md-9">
        <p class="brdcrmb"><a class="brdcrmb-item">{{ __( 'admin/organization.organizations') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __( 'admin/common.edit') }}</strong></p>
    </div><!-- .col-* -->

    <div class="col-md-9">

        {{ Form::model($organization, ['route' => ['admin.organization.update', $organization->id], 'method'=>'PUT', 'name'=>'form_organization_edit', 'class'=>'form-horizontal']) }}
        <div class="ibox">

            <div class="ibox-title">
                <h5>{{ __('admin/organization.organization_information') }}</h5>
            </div>

            <div class="ibox-content">

                <div class="form-group row{{ $errors->has('name') ? " has-danger" : "" }}">
                    {{ Form::label('name', __('admin/common.name').'*', ['class' => 'col-md-3 control-label']) }}
                    <div class="col-md-9">
                        {{ Form::text('name',null,['class' => 'form-control', 'required' => 'required']) }}
                        @include('partials.errors', ['err_type' => 'field','field' => 'name'])
                    </div>
                </div>
                <div class="form-group row">
                    {{ Form::label('address', __('admin/common.address'), ['class' => 'col-md-3 control-label']) }}
                    <div class="col-md-9">
                        {{ Form::textarea('address',null,['class' => 'form-control-area', 'rows' => 3]) }}
                    </div>
                </div>
                <div class="form-group row{{ $errors->has('country') ? " has-danger" : "" }}">
                    {{ Form::label('country', __('admin/common.country').'*', ['class' => 'col-md-3 control-label']) }}
                    <div class="col-md-9">
                        {!! Form::select('country',__('country'),null,['placeholder' => __('admin/common.country'),'class' => 'form-control', 'required' => 'required']) !!}
                        @include('partials.errors', ['err_type' => 'field','field' => 'country'])
                    </div>
                </div>

            </div><!-- .ibox-content -->

        </div><!-- .ibox -->

        <div class="ibox">
            <div class="ibox-title">
                <h5>{{ __('admin/organization.features') }}</h5>
            </div>
            <div class="ibox-content">
                <div class="row feature-body">
                    <div class="col-md-4 feature-box-size">
                        <div class="feature-box text-center">
                            <div class="feature-icon">
                                <img src="/images/ai-feature.png">
                            </div>
                            <div class="feature-title">{{ __('admin/organization.ai_censorship') }}</div>
                            <small>{{ __('admin/organization.cp_feature') }}</small>
                            <div class="mt-2">
                                <input class="hidden" name="ai_content_review" value="{{ old('ai_content_review') ? old('ai_content_review') : (is_null(optional($organization->feature)->ai_content_review) ? 'off' : (optional($organization->feature)->ai_content_review == 1 ? 'on' : 'off')) }}">
                                <button type="button" class="btn btn-sm btn-toggle feature_btn {{ old('ai_content_review') ? (old('ai_content_review') == 'on' ? 'active' : '') : (is_null(optional($organization->feature)->ai_content_review) ? '' : (optional($organization->feature)->ai_content_review == 1 ? 'active' : '')) }}" data-toggle="button" aria-pressed="{{ old('ai_content_review') ? (old('ai_content_review') == 'on' ? 'true' : 'false') : (is_null(optional($organization->feature)->ai_content_review) ? 'false' : (optional($organization->feature)->ai_content_review == 1 ? 'true' : 'false')) }}" autocomplete="off">
                                    <div class="handle"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-save">
            <button type="submit" class="btn btn-primary">{{ __('admin/common.update') }}</button>
        </div>
        {{ Form::close() }}

    </div><!-- .col* -->

</div><!-- .row -->
@stop
@push('foot-scripts')
    <script type="text/javascript">
        $(".feature_btn").click(function () {
            if($(this).hasClass('active')){
                $(this).parent().find('input').val('off');
            }else{
                $(this).parent().find('input').val('on');
            }
        });
    </script>
@endpush
