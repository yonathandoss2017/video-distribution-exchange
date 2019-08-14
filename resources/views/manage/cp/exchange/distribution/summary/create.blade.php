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
                        <a href="{{ route('manage.cp.exchange.distribution.create', $property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/exchange/distribution.back_to_terms_of_distribution') }}</a>
                        <div class="title">{{ __('manage/cp/exchange/distribution.edit_summary') }}</div>
                    </div>
                    {{ Form::open(['method' => 'POST', 'route' => ['manage.cp.exchange.distribution.summary.store', $property_id], 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data' ]) }}
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/exchange/distribution.terms_summary') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row {{ $errors->has('name') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('common.name') }}*</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="{{ __('manage/cp/exchange/distribution.terms_of_distribution') }} #1245" tabindex="1">
                                    @include('partials.errors', ['err_type' => 'field', 'field' => 'name'])
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.internal_remarks') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control-area" id="exampleTextarea" rows="3" name="internal_remarks" tabindex="2">{{ old('internal_remarks') }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('contract') ? " has-danger" : "" }}">
                                <label class="col-md-3 control-label">{{ __('manage/cp/exchange/distribution.contract') }}</label>
                                <div class="col-md-9">
                                    <label class="custom-file">
                                        <input type="file" name="contract" id="contract_file" class="filestyle" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
                                    </label>
                                    <div class="description">
                                        <small>* {{ __('manage/cp/exchange/distribution.supported_format') }}: .JPG, .JPEG, .PNG, .PDF, .DOC, .DOCX</small>
                                    </div>
                                    @include('partials.errors', ['err_type' => 'field', 'field' => 'contract'])
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
@push('js')
    <script type="text/javascript" src="/js/bootstrap-filestyle.min.js"> </script>
    <script>
        $(":file").filestyle({btnClass: "btn-upload",placeholder: "{{ __('manage/cp/exchange/distribution.no_file') }}",text:"{{ __('manage/cp/exchange/distribution.browse') }}"});
    </script>
    <script>
        $('#contract_file').change(function () {
            if (this.files && this.files[0]) {
                var name = this.files[0].name;
                var arr = name.split('.'), suffix = arr[arr.length - 1];
                var can_accept_type = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
                if (can_accept_type.indexOf(suffix) < 0) {
                    $('.custom-file div').find('input').val('');
                    return alert("{{ __('manage/cp/exchange/distribution.invalid_contract_file') }}");
                }
            }
        });
    </script>
@endpush
