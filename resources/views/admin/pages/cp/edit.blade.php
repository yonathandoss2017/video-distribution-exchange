@extends('admin.layout')
@push('title') {{ __('admin/sidebar.content_providers') }} | {{ __('app.title') }} @endpush
@section('content')
<div class="row justify-content-center">

    <header class="title-header col-md-9">
        <h3 class="title">{{ $property->name }}</h3>
    </header>

    <div class="col-md-9">
        <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.content_providers') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __('admin/common.edit') }}</strong></p>
    </div><!-- .col-* -->

    <div class="col-md-9">
        {{ Form::model($property, ['route' => ['admin.cp.update', $property->id], 'method'=>'PUT', 'class' => 'form-horizontal']) }}

        <div class="ibox">

            <div class="ibox-title">
                <h5>{{ __('admin/content_provider.cp_information') }}</h5>
            </div>

            <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('admin/common.name') }}</label>
                        <div class="col-md-9">
                            {{ Form::text('name',$property->name,['class' => 'form-control', 'disabled'=>'']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('admin/common.type') }}</label>
                        <div class="col-md-9">
                            {{ Form::text('type',strtoupper($property->type),['class' => 'form-control', 'disabled'=>'']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 control-label">{{ __('admin/common.organization') }}</label>
                        <div class="col-md-9">
                            {{ Form::select('organization_id',$organizations,null,['class'=>'form-control','required'=>'']) }}
                        </div>
                    </div>
            </div><!-- .ibox-content -->

        </div><!-- .ibox -->

        <div class="form-save">
            <button type="submit" class="btn btn-primary">{{ __('admin/common.update') }}</button>
        </div>

        {{ Form::close() }}
    </div><!-- .col* -->

</div><!-- .row -->
@stop
