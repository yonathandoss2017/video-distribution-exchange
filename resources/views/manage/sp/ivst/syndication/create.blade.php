@extends('partials.layout_sp')

@push('title')
    {{ __('manage/sp/ivst/syndication.page_title') }} | {{ __('app.title') }}
@endpush
@push('script-head')
<script src="/vendor/jquery/jquery.js"></script>
<script src="/vendor/jquery/jquery-ui.js"></script>
@endpush

@section('content')
    <!-- Begin page content -->
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                {!! Form::open(['route' => ['manage.sp.syndication.store', $property_id], 'method' => 'post', 'files' => true, 'class'=>'form-horizontal', 'id' => 'notification-url-form']) !!}
                    <input type="hidden" name="is_remove" id="is-remove" value="0">
                    <div class="title-header ">
                        <div class="title">{{ __('manage/sp/ivst/syndication.page_title') }}</div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('manage/sp/ivst/syndication.wordpress_information') }}</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="form-group row{{ $errors->has('sp_ivst_notification_url') ? " has-danger" : "" }}">
                                <label class="col-md-4 control-label">{{ __('manage/sp/ivst/syndication.notification_url') }}</label>
                                <div class="col-md-8">
                                    <input type="text" name="sp_ivst_notification_url" class="form-control" value="{{ $sp->sp_ivst_notification_url }}" placeholder="http://www.mywebsite.com/notification" required>
                                    @include('partials.errors', ['err_type' => 'field','field' => 'sp_ivst_notification_url'])
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-save">
                        @if ($sp->sp_ivst_notification_url)
                            <button type="button" class="btn btn-secondary m-r" id="remove">{{ __('manage/sp/common.remove') }}</button>
                        @endif
                        <button type="submit" class="btn btn-primary">{{ __('manage/sp/common.save') }}</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    $(function(){
        $('#remove').click(function(){
            $('#is-remove').val(1);
            $('#notification-url-form').submit();
        });
    })
</script>
@endpush
