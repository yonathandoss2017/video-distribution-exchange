@extends('admin.layout')
@push('title') {{ __( 'admin/organization.organizations') }} | {{ __('app.title') }} @endpush
@push('head-scripts')
    <!-- timezones -->
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush
@section('content')
    <div class="row">

        <header class="title-header col-md-12">
            <h3 class="title">{{ __('admin/sidebar.organizations') }}</h3>
        </header>

        <div class="col-4">
            <p class="brdcrmb"><a class="brdcrmb-item">{{ __( 'admin/organization.organizations') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __( 'admin/common.home') }}</strong></p>
        </div><!-- .col-* -->

        <div class="col-8 min-menu">
            <div class="right-panel">
                {!! Form::open(['route'=>['admin.organization.index'],'method'=>'GET','id' => 'playlists_form']) !!}
                <div class="input-group sr">
                    <input type="text" placeholder="{{ __('admin/common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}" >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="right-panel">
                <a href="{{ route('admin.organization.create') }}" class="btn btn-normal btn-m m-r">{{ __('admin/organization.new_organization') }}</a>
            </div>
        </div><!-- .col-* -->
        <div class="col-md-12 search-result">
            @if($search)
                {{ __('admin/common.keywords') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current() }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
            @endif
        </div>

        <div class="col-md-12">

            <div class="ibox">

                <div class="ibox-content playlist-list">

                    <table class="table table-hover">

                        <thead>
                        <tr>
                            <th>{{ __('admin/common.id') }}</th>
                            <th class="playlist-title ptl" style="width: 42%;">{{ __('admin/common.name') }}</th>
                            <th>{{ __('admin/organization.disk_space') }}</th>
                            <th>{{ __('admin/common.created_at') }}</th>
                            <th>{{ __('admin/common.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($organizations as $organization)
                                <tr>
                                    <td>{{ $organization->id }}</td>
                                    <td class="playlist-title">
                                        <a href="">{{ $organization->name }}</a><br/>
                                        <small>{{ $organization->properties_count }} {{ __('admin/organization.properties') }}</small>
                                    </td>
                                    <td><strong>{{ $organization->disk_space }}</strong></td>
                                    <td class="date">
                                        <div id="{{ $organization->id }}-{{ $organization->created_at->timestamp }}"></div>
                                        <small class="timestamp" id="{{ $organization->id }}" dt="{{ $organization->created_at->timestamp }}"></small>
                                    </td>
                                    <td class="playlist-actions hp">
                                        <a href="{{ route('admin.organization.edit', $organization->id) }}" class="btn btn-normal btn-m">{{ __('admin/common.edit') }}</a>
                                        @if($organization->type !== 'partner')
                                            <a href="/manage/organization/select/{{ $organization->id }}" class="btn btn-normal btn-m">{{ __('admin/common.login') }}</a>
                                            <a href="#" data-toggle="modal" data-target="#delete_confirm" data-form-id="{{ 'organization_'.$organization->id }}" class="btn btn-normal btn-m delete" onclick="setFormId(this);">{{ Form::open(['url' => route('admin.organization.destroy',$organization->id), 'method' => 'DELETE', 'id' => 'organization_'.$organization->id]) }}<i class="fa fa-trash" aria-hidden="true"></i>{{ Form::close() }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    <div class="row">
                        <div class="col-5">
                            <div class="dataTables_info">
                                @if ($organizations->count()>0)
                                {{
                                        __(
                                            'admin/organization.organizations_pagination',
                                            [
                                                'from'=>$organizations->firstItem(),
                                                'to'=>$organizations->lastItem(),
                                                'total'=>$organizations->total()
                                            ]
                                        )
                                    }}
                                @else
                                    {{ __('admin/organization.no_organizations') }}
                                @endif
                            </div>
                        </div>

                        <div class="col-7">
                            <div class="dataTables_paginate paging_simple_numbers">
                                {{ $organizations->appends(request()->input())->links() }}
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
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('admin/organization.delete_organization') }}</h5>
                </div>
                <div class="modal-body">
                    <div>{{ __('admin/organization.this_action_is_not_reversible') }}</div>
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
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('admin/organization.delete_organization') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>{{ __('admin/organization.delete_warn') }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="delete-org-button" data-form-id="" onclick="deleteOrg(this);">{{ __('admin/common.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
<script>
    function setFormId(element) {
        var formId = element.getAttribute('data-form-id');
        $('#delete-org-button').attr('data-form-id', formId);
    }
    function nextStep() {
        $('#delete_confirm').modal('hide');
    }
    function deleteOrg(element) {
        var formId = element.getAttribute('data-form-id');
        $('#' + formId).submit();
    }
</script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush