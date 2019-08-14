@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/settings/notification_settings.licensing_terms') }} &#8212; {{ __('manage/cp/settings/notification_settings.email_notifications') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-header ">
                    <div class="min-menu row">
                        <div class="col-md-6"><div class="title">{{ __('manage/cp/settings/notification_settings.licensing_terms') }} &#8212; {{ __('manage/cp/settings/notification_settings.email_notifications') }}</div></div>
                        <div class="col-md-6">
                            <div class="right-panel">
                                <a href="#" class="btn btn-normal dropdown-toggle" data-toggle="dropdown">{{ __('common.actions') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu notifications">
                                    {{ Form::open(['url' => route('manage.cp.notifications.enable', $property_id), 'method'=>'POST']) }}
                                    {{ Form::hidden('id', '') }}
                                    <li><button id="enable" style="display: none">{{ __('manage/cp/settings/notification_settings.enable_notifications') }}</button></li>
                                    {{ Form::close() }}
                                    {{ Form::open(['url' => route('manage.cp.notifications.disable', $property_id), 'method'=>'POST']) }}
                                    {{ Form::hidden('id', '') }}
                                    <li><button id="disable" style="display: none">{{ __('manage/cp/settings/notification_settings.disable_notifications') }}</button></li>
                                    {{ Form::close() }}
                                    <li><a href="#" onclick="operateNotifications('enable')">{{ __('manage/cp/settings/notification_settings.enable_notifications') }}</a></li>
                                    <li><a href="#" onclick="operateNotifications('disable')">{{ __('manage/cp/settings/notification_settings.disable_notifications') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>



                </div>
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list m-top">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="cb"><label class="custom-control custom-checkbox"><input type="checkbox" id="select_all" class="custom-control-input"><span class="custom-control-indicator"></span></label></th>
                                    <th>{{ __('manage/cp/settings/notification_settings.user') }}</th>
                                    <th>{{ __('manage/cp/settings/notification_settings.email') }}</th>
                                    <th>{{ __('manage/cp/settings/notification_settings.notifications') }}</th>
                                    <th>{{ __('common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td class="cb"><label class="custom-control custom-checkbox"><input type="checkbox" id="select[]" name="select[]" value="{{ $user->id }}" class="custom-control-input"><span class="custom-control-indicator"></span></label></td>
                                    <td class="playlist-title pl"><div>{{ $user -> name }}</div></td>
                                    <td>{{ $user -> email }}</td>
                                    <td>{{ empty($user->licenseNotification) || $user->licenseNotification->status == \App\Models\LicenseNotification::DISABLE ? __('manage/cp/settings/notification_settings.disabled') : __('manage/cp/settings/notification_settings.enable') }}</td>
                                    <td class="playlist-actions">
                                        @if(empty($user->licenseNotification) || $user->licenseNotification->status == \App\Models\LicenseNotification::DISABLE)
                                        {{ Form::open(['url' => route('manage.cp.notifications.enable', $property_id), 'method'=>'POST']) }}
                                        {{ Form::hidden('id', $user->id) }}
                                        <button class="btn btn-normal btn-m">{{ __('manage/cp/settings/notification_settings.enable_notifications') }}</button>
                                        {{ Form::close() }}
                                        @else
                                        {{ Form::open(['url' => route('manage.cp.notifications.disable', $property_id), 'method'=>'POST']) }}
                                        {{ Form::hidden('id', $user->id) }}
                                        <button class="btn btn-normal btn-m">{{ __('manage/cp/settings/notification_settings.disable_notifications') }}</button>
                                        {{ Form::close() }}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($users->count()>0)
                                        {{
                                            __(
                                                'manage/cp/settings/notification_settings.user_pagination',
                                                [
                                                    'from'=>$users->firstItem(),
                                                    'to'=>$users->lastItem(),
                                                    'total'=>$users->total()
                                                ]
                                            )
                                        }}
                                        @else
                                            {{ __('manage/cp/settings/notification_settings.no_users') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                            {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>

    <script>
        var videofeed = {
            confirm:'@lang('videofeed.confirm_delete')'
        };
    </script>
@stop

@push('js')
<script>
    var arr = [];
    $('#select_all').change(function() {
        var checkboxes = $(this).closest('table').find(':checkbox');
        if($(this).is(':checked')) {
            var arr = [];
            checkboxes.prop('checked', true);
            $('input[name="select[]"]').each(function(){
                arr.push($(this).val());
            });
            $('input[name="id"]').val(arr);
        } else {
            checkboxes.prop('checked', false);
            $('input[name="id"]').val('');
        }
    });

    $('input[name="select[]"]').change(function() {
        if($(this).is(':checked')) {
            arr.push($(this).val());
            $('input[name="id"]').val(arr);
        } else {
            removeA(arr,$(this).val());
            $('input[name="id"]').val(arr);
        }
    });

    function removeA(arr) {
        var what, a = arguments, L = a.length, ax;
        while (L > 1 && arr.length) {
            what = a[--L];
            while ((ax= arr.indexOf(what)) !== -1) {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }

    function operateNotifications($v) {
        if ($v == 'enable') {
            $('#enable').click();
        } else {
            $('#disable').click();
        }
    }
</script>
@endpush
