@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/request_logs.request_details') }}
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="form-horizontal">
                <div class="title-header">
                    <div class="min-menu row">
                        <div class="col-md-3">
                            <div class="title">{{ __('manage/cp/exchange/request_logs.request_details') }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="right-panel">
                                <a href="{{ route('manage.cp.exchange.request_logs.index', $property_id) }}" class="btn btn-normal btn-m">
                                    {{ __('manage/cp/exchange/request_logs.back_to_request_logs') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.title-header -->
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/request_logs.requester_information') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/request_logs.requested_by') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                {{ $requestLog->user->name }} &lt;{{ $requestLog->user->email }}&gt;
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/request_logs.service_providers') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                @foreach($requestLog->serviceProviders as $sp)
                                <p>
                                    {{ $sp->organization->name }} &raquo; {{ $sp->name }} <br/>
                                    <small>{{ __('manage/cp/exchange/request_logs.sp_account_uuid') }}: {{ $sp->uuid }}</small>
                                </p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> <!-- /.ibox -->

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('manage/cp/exchange/request_logs.email_information') }}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('manage/cp/exchange/request_logs.to') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                @if ($requestLog->recipients)
                                    @foreach ($requestLog->recipients as $rec)
                                        <p>{{ $rec->name }} &lt;{{ $rec->pivot->email ?: $rec->email }}&gt;</p>
                                    @endforeach
                                @else
                                    -
                                    <small class="block">No email has been sent, please go to <a href="{{ route('manage.cp.exchange.notification-settings.index', $property_id) }}" target="_blank">Notification Settings</a> to choose at least 1 email recipients.</small>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('common.subject') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                {{ $requestLog->subject }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 control-label">{{ __('common.message') }}</label>
                            <div class="col-md-9 control-label t-a-l">
                                {!! nl2br($requestLog->message) !!}
                            </div>
                        </div>
                    </div>
                </div> <!-- /.ibox -->
            </div> <!-- /.form-horizontal -->

            <p>&nbsp;</p>

            <div class="form-horizontal">
                <div class="title-header">
                    <div class="min-menu row">
                        <div class="title col-md-5">{{ __('manage/cp/exchange/request_logs.playlists_requested') }}</div>
                        <div class="col-md-7">
                            <div class="right-panel">
                                <a href="#" class="btn btn-normal dropdown-toggle" data-toggle="dropdown">{{ __('manage/cp/exchange/request_logs.marketplace_terms') }} <i class="fa fa-caret-down" aria-hidden="true" style="font-size: inherit;"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0)" onclick="filterPlaylist(this)" data-id="-1">{{ __('common.all') }}</a></li>
                                    @foreach($terms as $term)<li><a href="javascript:void(0)" onclick="filterPlaylist(this)" data-id="{{ $term->id }}">{{ $term->name }}</a></li>@endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.title-header -->
                <div class="ibox">
                    <div class="ibox-content" id="request_playlist">
                        @include('manage.cp.exchange.request-logs.playlist', ['requestLog' => $requestLog])
                    </div>
                </div>
                <div class="form-save">
                    <form method="post" action="{{ route('manage.cp.exchange.distribution.store', $property_id) }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="tom_id" value="-1" id="tom_id">
                        <input type="hidden" name="log_id" value="{{ $requestLog->id }}">
                        <button type="submit" class="btn btn-primary">{{ __('manage/cp/contents/request_logs.generate_terms') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
    <script type="text/javascript">
        function filterPlaylist(obj)
        {
            $.ajax({
                url : '?tom_id=' + $(obj).attr('data-id'),
                dataType: 'html',
            }).done(function (data) {
                $('#request_playlist' ).html(data);
                $('#tom_id').val($(obj).attr('data-id'));
            }).fail(function () {
            });
        }
    </script>
@endpush