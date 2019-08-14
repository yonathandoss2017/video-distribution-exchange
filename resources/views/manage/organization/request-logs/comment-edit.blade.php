@extends('partials.layout_home')

@push('title')
    {{ __('app.title') }}
@endpush

@section('content')
    <!-- Begin page content -->
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="title-header">
                    <a href="{{ route('manage.organization.request-logs.index', $item->property_id) }}" class="btn btn-normal btn-m">{{ __('manage/cp/contents/request_logs.back_to_request_logs') }}</a>
                    <div class="title">{{ __('manage/cp/exchange/request_logs.add_edit_comments') }}</div>
                </div> <!-- /.title-header -->
                {{ Form::open(['url' => route('manage.organization.request-logs.comment.store', $item->id), 'method'=>'PUT', 'class'=>'form-horizontal']) }}
                    {{ csrf_field() }}
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/cp/exchange/request_logs.comments') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <textarea class="form-control-area" id="exampleTextarea" name="comments" rows="10" style="max-width: 100%">{{ $item->publish_review_comment }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /.ibox -->
                    <div class="form-save"><button type="submit" class="btn btn-primary">{{ __('common.save') }}</button></div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
