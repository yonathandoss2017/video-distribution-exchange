@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.edit_summary') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form">
                    <div class="title-header">
                        <a href="{{ route('manage.cp.exchange.distribution.edit', [$property_id, $summary->id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.back_to_internal_whitelisting') }}</a>
                        <div class="title">{{ __('manage/cp/exchange/distribution.edit_summary') }}</div>
                    </div>
                    {{ Form::open(['method' => 'PUT', 'route' => ['manage.cp.exchange.distribution.summary.update', $property_id, $summary->id], 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data' ]) }}
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/exchange/distribution.terms_summary') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row {{ $errors->has('name') ? " has-danger" : "" }}" hidden>
                                <label class="col-md-3 control-label">{{ __('common.name') }}*</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="Whitelist for own SP" placeholder="{{ __('manage/cp/exchange/distribution.terms_of_distribution') }} #1245" tabindex="1">
                                    @include('partials.errors', ['err_type' => 'field', 'field' => 'name'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.internal_remarks') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control-area" id="exampleTextarea" rows="3" name="internal_remarks" tabindex="2">{{ !empty(old('internal_remarks')) ? old('internal_remarks') : $summary->internal_remarks }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-save"><button type="submit" class="btn btn-primary">{{ __('common.save') }}</button></div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop