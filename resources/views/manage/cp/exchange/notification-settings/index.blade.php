@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/settings/notification_settings.notification_settings') }}
@endpush

@section('content')
    <div class="container" id="app">
        <div class="row">
            <div class="col-md-12">
                <div class="title-header ">
                    <div class="title">{{ __('manage/cp/settings/notification_settings.notification_settings') }}</div>
                </div>

                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th class="playlist-title ptl">{{ __('manage/cp/settings/notification_settings.name') }}</th>
                                    <th class="email-width">{{ __('manage/cp/settings/notification_settings.email') }}</th>
                                    <th>{{ __('manage/cp/settings/notification_settings.role') }}</th>
                                    <th>{{ __('manage/cp/settings/notification_settings.email_notifications') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="playlist-title">
                                            <a href="#">{{ $user->name }}</a>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ __('admin/user.'.$user->roles->first()->name) }}</td>
                                            <td class="playlist-actions">
                                                <radio-button
                                                        value="{{ $user->isAcceptNotificationForProperty($property_id) }}"
                                                        url-update="{{ route('manage.cp.exchange.notification-settings.update', [$property_id, $user->id]) }}"></radio-button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">{{ __('manage/cp/settings/notification_settings.show_users', ['first' => $users->firstItem(), 'last' => $users->lastItem(), 'total' => $users->total()]) }}</div>
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
@stop

@push('js')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush