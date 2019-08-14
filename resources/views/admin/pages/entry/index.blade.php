@extends('admin.layout')
@push('title') {{ __('admin/entry.entries') }} | {{ __('app.title') }} @endpush
@push('head-scripts')
    <!-- timezones -->
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush
@section('content')
<div class="row">

    <header class="title-header col-md-12">
        <h3 class="title">{{ __('admin/entry.entries') }}</h3>
    </header>

    <div class="col-4">
        <p class="brdcrmb"><a class="brdcrmb-item">{{ __('admin/entry.entries') }}</a> <span class="brdcrmb-item">/</span> <strong class="brdcrmb-item">{{ __('admin/common.home') }}</strong></p>
    </div><!-- .col-* -->

    <div class="col-8 min-menu">
        <div class="right-panel">
            {!! Form::open(['route'=>['admin.entry.index'],'method'=>'GET','id' => 'playlists_form']) !!}
            <div class="input-group sr">
                <input type="text" placeholder="{{ __('admin/common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}" >
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                </span>
            </div>
            {!! Form::close() !!}
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

                <table class="table table-hover last-left">

                    <thead>
                    <tr>
                        <th class="playlist-amount" style="width: 67px">{{ __('admin/common.id') }}</th>
                        <th class="image"></th>
                        <th class="playlist-title ptl" style="width: 50.5%">{{ __('admin/common.name') }} / {{ __('admin/sidebar.properties') }}</th>
                        <th>{{ __('admin/common.status') }}</th>
                        <th class="date-column">{{ __('admin/common.created_at') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                        @foreach($entries as $entry)
                            <tr>
                                <td>{{ $entry->id }}</td>
                                <td class="image">
                                    <div class="video-img">
                                        <img src="{{ \App\Services\Serve\VideoImageService::getImageUrl($entry, $entry->property_id, null, 120) }}" alt="">
                                    </div>
                                </td>
                                <td class="playlist-title ptl">
                                    <a href="{{ route('manage.cp.video.edit', [$entry->property_id, $entry->id]) }}" target="_blank">{{ $entry->name }}</a><br/>
                                    <a href="{{ route('manage.organization.select', $entry->content_provider->organization_id) }}" target="_blank"><small>{{ $entry->content_provider->organization->name }}</small></a> » <a href="{{ route('manage.cp.playlists.index', $entry->property_id) }}" target="_blank"><small>{{ $entry->content_provider->name }}</small></a>
                                </td>
                                <td>
                                    @if ($entry->status == \App\Models\Entry::STATUS_READY)
                                        @if ($entry->isPending)
                                            <span class="label label-blue">{{ __('admin/entry.scheduled') }}</span>
                                        @else
                                            <span class="label label-active">{{ __('admin/entry.ready') }}</span>
                                        @endif
                                    @elseif ($entry->status == \App\Models\Entry::STATUS_PENDING)
                                        <span class="label label-orange">{{ __('admin/entry.pending') }}</span>
                                    @elseif ($entry->status == \App\Models\Entry::STATUS_REJECTED)
                                        <span class="label label-rejected">{{ __('admin/entry.reject') }}</span>
                                    @elseif ($entry->status == \App\Models\Entry::STATUS_ERROR)
                                        <span class="label label-error">{{ __('admin/entry.error') }}</span>
                                    @elseif ($entry->status == \App\Models\Entry::STATUS_DRAFT)
                                        <span class="label label-grey">{{ __('admin/entry.draft') }}</span>
                                    @else
                                        <span class="label label-orange">{{ __('admin/entry.processing') }}</span>
                                    @endif
                                </td>
                                <td class="date">
                                    <div id="{{ $entry->id }}-{{ $entry->created_at->timestamp }}"></div>
                                    <small class="timestamp" id="{{ $entry->id }}" dt="{{ $entry->created_at->timestamp }}"></small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                <div class="row">
                    <div class="col-5">
                        <div class="dataTables_info">
                            @if ($entries->count()>0)
                            {{
                                    __(
                                        'admin/entry.entries_pagination',
                                        [
                                            'from'=>$entries->firstItem(),
                                            'to'=>$entries->lastItem(),
                                            'total'=>$entries->total()
                                        ]
                                    )
                                }}
                            @else
                                {{ __('admin/entry.no_entries') }}
                            @endif
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="dataTables_paginate paging_simple_numbers">
                            {{ $entries->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>

            </div><!-- .ibox-content -->

        </div><!-- .ibox -->

    </div><!-- .col* -->

</div><!-- .row -->
@stop

@push('js')
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
