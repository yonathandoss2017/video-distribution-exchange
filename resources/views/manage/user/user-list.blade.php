@extends('partials.layout_home')

@push('title')
    {{ __('manage/organization/user.page_title') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-header ">
                    <div class="min-menu row">
                        <div class="col-md-3">
                            <div class="title">{{ __('manage/organization/user.page_title') }}</div>
                        </div>
                        <div class="col-md-9">
                            <div class="right-panel">
                                <a href="{{ route('manage.user.add') }}" class="btn btn-normal btn-m">{{ __('manage/organization/user.new_user') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="playlist-list">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>{{ __('manage/organization/common.name') }}</th>
                                    <th>{{ __('manage/organization/common.email') }}</th>
                                    <th>{{ __('manage/organization/common.status') }}</th>
                                    <th>{{ __('manage/organization/common.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td class="playlist-title pt">
                                        <a href="#">{{ $user -> name }}</a>
                                    </td>
                                    <td>{{ $user ->email }}</td>
                                    <td>
                                        @if ($user->is_disabled)
                                            <span class="label label-rejected">{{ __('manage/organization/user.disable') }}</span>
                                        @else
                                            @if (! $user->activated_at)
                                                <span class="label label-grey">{{ __('manage/organization/user.pending') }}</span>
                                            @else
                                                <span class="label label-active">{{ __('manage/organization/user.active') }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="playlist-actions">
                                        <a href="{{ route('manage.user.edit',$user->id) }}" class="btn btn-normal btn-m{{ (Auth::user()->id == $user->id)?' hidden':'' }}">{{ __('manage/organization/user.update_access') }}</a>
                                        {{ Form::open(['method'=>'DELETE', 'url'=>route('manage.user.destroy',$user->id), 'class'=>'form-horizontal']) }}
                                        <button class="btn btn-normal btn-m delete"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        {{ Form::close() }}
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
                                                    'manage/organization/user.user_pagination',
                                                    [
                                                        'from'=>$users->firstItem(),
                                                        'to'=>$users->lastItem(),
                                                        'total'=>$users->total()
                                                    ]
                                                )
                                            }}
                                        @else
                                            {{ __('manage/organization/user.no_users') }}
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
@stop
