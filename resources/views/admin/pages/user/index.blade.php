@extends('admin.layout')
@push('title') {{ __('admin/sidebar.users') }} | {{ __('app.title') }} @endpush
@push('head-scripts')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush

@section('content')
    <div class="row">

            <header class="title-header col-md-12">
                <h3 class="title">{{ __('admin/sidebar.users') }}</h3>
            </header>

            <div class="col-4">
                <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.users') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __('admin/common.home') }}</strong></p>
            </div><!-- .col-* -->

            <div class="col-8 min-menu">
                <div class="right-panel">
                    {!! Form::open(['route'=>['admin.user.index'], 'method'=>'GET', 'accept-charset' => 'UTF-8', 'id' => 'playlists_form']) !!}
                        <div class="input-group sr">
                            <input type="text" placeholder="{{ __('admin/common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </span>
                        </div>
                    {!! Form::close() !!}
                </div>
                <div class="right-panel">
                    <a href="{{ route('admin.user.create') }}" class="btn btn-normal btn-m m-r">{{ __('admin/user.new_user') }}</a>
                </div>
            </div><!-- .col-* -->

            <div class="col-md-12">

                <div class="ibox">

                    <div class="ibox-content playlist-list">

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th style="width: 8%;">{{ __('admin/common.id') }}</th>
                                <th class="playlist-title pl">{{ __('admin/common.name') }}</th>
                                <th class="properties">{{ __('admin/user.property') }}</th>
                                <th>{{ __('admin/common.status') }}</th>
                                <th>{{ __('admin/common.created_at') }}</th>
                                <th>{{ __('admin/common.actions') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="playlist-title">
                                    <a href="">{{ $user->name }}</a><br/>
                                    <small>{{ $user->email }}</small>
                                </td>
                                <td class="properties">
                                    <span class="label cp">CP {{ $user->cp_count }}</span>
                                    <span class="label sp">SP {{ $user->sp_count }}</span>
                                </td>
                                <td><span class="label {{ $user->is_disabled ? 'label-rejected' : ($user->isActive() ? 'label-approved' : 'label-pending') }}">{{ $user->is_disabled ? __( 'admin/common.disable') : ($user->isActive()? __( 'admin/common.active') : __( 'admin/common.inactive')) }}</span></td>
                                <td class="date">
                                    <div id="{{ $user->id }}-{{ $user->created_at->timestamp }}"></div>
                                    <small class="timestamp" id="{{ $user->id }}" dt="{{ $user->created_at->timestamp }}"></small>
                                </td>
                                <td class="playlist-actions hp">
                                    <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-normal btn-m">{{ __('admin/common.edit') }}</a>
                                    {{-- <form method="POST" action="{{ route('admin.user.destroy', $user->id) }}" accept-charset="UTF-8" onsubmit="return ConfirmDelete()">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <a href="#" onclick="$(this).closest('form').submit()" class="btn btn-normal btn-m delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </form> --}}
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                            <input type="hidden" name="comfirm_del" value="{{ __('admin/common.confirm_delete') }}" />

                        </table>

                        <div class="row">
                            <div class="col-5">
                                <div class="dataTables_info">
                                    @if ($users->count()>0)
                                        {{
                                            __(
                                                'admin/user.users_pagination',
                                                [
                                                    'from'=>$users->firstItem(),
                                                    'to'=>$users->lastItem(),
                                                    'total'=>$users->total()
                                                ]
                                            )
                                        }}
                                    @else
                                        {{ __('admin/user.no_users') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $users->appends(request()->input())->links() }}
                                </div>
                            </div>
                        </div>

                    </div><!-- .ibox-content -->

                </div><!-- .ibox -->

            </div><!-- .col* -->

        </div><!-- .row -->
@stop
@push('foot-scripts')
    <script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
    <script>
        var co = $("input[name='comfirm_del']").val();
        function ConfirmDelete() {
            var x = confirm(co);
            return (x) ? true : false;
        }
    </script>
@endpush
