@extends('admin.layout')
@push('title') {{ __('admin/sidebar.content_providers') }} | {{ __('app.title') }} @endpush
@push('head-scripts')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush
@section('content')
<div class="row">

    <header class="title-header col-md-12">
        <h3 class="title">{{ __('admin/sidebar.content_providers') }}</h3>
    </header>

    <div class="col-4">
        <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/sidebar.content_providers') }}</a> <span class="brdcrmb-item">/</span>  <strong class="brdcrmb-item">{{ __('admin/common.home') }}</strong></p>
    </div><!-- .col-* -->

    <div class="col-8 min-menu">
        <div class="right-panel">
            {!! Form::open(['route'=>['admin.cp.index'], 'method'=>'GET', 'accept-charset' => 'UTF-8', 'id' => 'playlists_form']) !!}
                <div class="input-group sr">
                    <input type="text" placeholder="{{ __('admin/common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </span>
                </div>
            {!! Form::close() !!}
        </div>
    </div><!-- .col-* -->

    <div class="col-md-12">

        <div class="ibox">

            <div class="ibox-content playlist-list">

                <table class="table table-hover">

                    <thead>
                    <tr>
                        <th style="width: 8%;">{{ __('admin/common.id') }}</th>
                        <th class="playlist-title ptl" style="width: 29%;">{{ __('admin/common.name') }}</th>
                        <th style="width: 29%;">{{ __('admin/common.organization') }}</th>
                        <th>{{ __('admin/common.created_at') }}</th>
                        <th>{{ __('admin/common.actions') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($properties as $property)
                    <tr>
                        <td>{{ $property->id }}</td>
                        <td class="playlist-title">
                            <a href="#">{{ $property->name }}</a>
                        </td>
                        <td>{{ $property->organization->name }}</td>
                        <td class="date">
                            <div id="{{ $property->id }}-{{ $property->created_at->timestamp }}"></div>
                            <small class="timestamp" id="{{ $property->id }}" dt="{{ $property->created_at->timestamp }}"></small>
                        </td>
                        <td class="playlist-actions hp">
                            <a href="{{ route('admin.cp.edit', $property->id) }}" class="btn btn-normal btn-m">{{ __('admin/common.edit') }}</a>
                            <a href="{{ route('admin.cp.api', $property->id) }}" class="btn btn-normal btn-m">{{ __('admin/common.api') }}</a>
                            @if($property->status == \App\Models\Playlist::STATUS_DELETE_PROCESSING)
                                <a href="#" class="btn btn-normal btn-m delete disabled"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            @else
                                <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="{{ 'property_'.$property->id }}" class="btn btn-normal btn-m delete" onclick="setFormId(this);">{{ Form::open(['url' => route('admin.cp.destroy', $property->id), 'method' => 'DELETE', 'id' => 'property_'.$property->id]) }}<i class="fa fa-trash" aria-hidden="true"></i>{{ Form::close() }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                    <input type="hidden" name="comfirm_del" value="{{ __('admin/common.confirm_delete') }}" />

                </table>

                <div class="row">
                    <div class="col-5">
                        <div class="dataTables_info">
                            @if ($properties->count()>0)
                                {{
                                    __(
                                        'admin/content_provider.cp_pagination',
                                        [
                                            'from'=>$properties->firstItem(),
                                            'to'=>$properties->lastItem(),
                                            'total'=>$properties->total()
                                        ]
                                    )
                                }}
                            @else
                                {{ __('admin/content_provider.no_cp') }}
                            @endif
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="dataTables_paginate paging_simple_numbers">
                            {{ $properties->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>

            </div><!-- .ibox-content -->

        </div><!-- .ibox -->

    </div><!-- .col* -->

</div><!-- .row -->
<div class="modal" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog s-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('admin/content_provider.delete_provider') }}</h5>
            </div>
            <div class="modal-body">
                <div>{{ __('admin/content_provider.this_action_is_not_reversible') }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin/common.back') }}</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#delete" onclick="nextStep();">{{ __('admin/common.continue') }}</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog s-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('admin/content_provider.delete_provider') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>{{ __('admin/content_provider.delete_warn') }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="delete-property-button" data-form-id="" onclick="deleteProperty(this);">{{ __('admin/common.delete') }}</button>
            </div>
        </div>
    </div>
</div>
@stop
@push('foot-scripts')
    <script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
    <script>
        function setFormId(element) {
            var formId = element.getAttribute('data-form-id');
            $('#delete-property-button').attr('data-form-id', formId);
        }
        function nextStep() {
            $('#delete_confirm').modal('hide');
        }
        function deleteProperty(element) {
            var formId = element.getAttribute('data-form-id');
            $('#' + formId).submit();
        }
    </script>
@endpush