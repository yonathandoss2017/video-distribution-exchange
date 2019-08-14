@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/distribution.terms_of_distribution') }}
@endpush

@push('script-head')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="title-header ">
                <div class="min-menu row">
                    <div class="col-md-3">
                        <div class="title">{{ __('manage/cp/exchange/distribution.terms_of_distribution') }}</div>
                    </div>
                    <div class="col-md-9">
                        <div class="right-panel">
                                <a href="{{ route('manage.cp.exchange.distribution.edit', [$property_id, $ownSpTod->id]) }}" class="btn btn-normal m-r">{{ __('manage/cp/exchange/distribution.internal_whitelisting') }}</a>
                                <a href="{{ route('manage.cp.exchange.distribution.create', $property_id) }}" class="btn btn-normal m-r">{{ __('manage/cp/exchange/distribution.new_terms') }}</a>
                            <div class="status-float">
                                <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('manage/cp/exchange/distribution.status_filter') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ url()->current() .'?search='.$search.'&sort='.$sort }}">{{ __('common.all') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_ACTIVE.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.active') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_SP_PENDING.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.pending_sp') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_DRAFT.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.draft') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_CP_REVOKED.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.cp_revoked') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_SP_DECLINED.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.sp_declined') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_SP_DISCONTINUE.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.sp_discontinued') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.platform_review') }}</a></li>
                                    <li><a href="{{ url()->current() .'?status='.\App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED.'&search='.$search.'&sort='.$sort }}">{{ __('manage/cp/exchange/distribution.platform_rejected') }}</a></li>
                                </ul>
                            </div>
                            {!! Form::open(['route'=>['manage.cp.exchange.distribution.index', $property_id],'method'=>'GET','class' => 'playlist-search', 'id' => 'terms_form']) !!}
                            <div class="input-group sr">
                                <input type="text" placeholder="{{ __('common.search') }}" class="input-sm form-control" name="search" value="{{ request()->query('search') }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </span>
                            </div>
                            <input type="hidden" class="date_sort" name="sort" value="{{ $sort }}">
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="col-md-12 search-result">
                            @if($search)
                                {{ __('common.keywords') }} <div class="search-result-info"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current(). '?status='.$status.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>&nbsp;&nbsp;&nbsp;
                            @endif
                            @if(in_array(strtolower($status), [
                                \App\Models\TermsOfDistribution::STATUS_DRAFT,
                                \App\Models\TermsOfDistribution::STATUS_ACTIVE,
                                \App\Models\TermsOfDistribution::STATUS_SP_PENDING,
                                \App\Models\TermsOfDistribution::STATUS_SP_DECLINED,
                                \App\Models\TermsOfDistribution::STATUS_CP_REVOKED,
                                \App\Models\TermsOfDistribution::STATUS_SP_DISCONTINUE,
                                \App\Models\TermsOfDistribution::STATUS_PLATFORM_REVIEW,
                                \App\Models\TermsOfDistribution::STATUS_PLATFORM_REJECTED,
                                ]))

                                {{ __('common.status') }}
                                <div class="search-result-info">
                                    <span class="search-result-text">{{ ucfirst(__('manage/cp/exchange/distribution.'. strtolower($status))) }}</span>
                                    <span class="search-close"><a href="{{ url()->current(). '?search='.$search.'&sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span>
                                </div>&nbsp;&nbsp;&nbsp;
                            @endif
                        </div>
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="playlist-list">
                        <table class="table table-hover  tod">
                            <thead>
                            <tr>
                                <th class="playlist-title" style="width: 46%">{{ __('manage/cp/exchange/distribution.terms') }}</th>
                                <th style="width: 120px;">{{ __('common.playlists') }}</th>
                                <th class="date {{ $sort == '' ? 'sorting' : ($sort == 'asc' ? 'sorting_asc' : 'sorting_desc') }}" id="date_sort" style="width: 15%">{{ __('common.last_updated') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th class="text-right">{{ __('common.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($termsOfDistributions as $termsOfDistribution)
                                    <tr>
                                        <td class="playlist-title fourty-five">
                                            @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_DRAFT)
                                                <a href="{{ route('manage.cp.exchange.distribution.edit', [$property_id, $termsOfDistribution->id]) }}">
                                            @else
                                                <a href="{{ route('manage.cp.exchange.distribution.show', [$property_id, $termsOfDistribution->id]) }}">
                                            @endif
                                                    {{ $termsOfDistribution->name }}
                                                    @if($termsOfDistribution->show_new_mark)
                                                        <small class="text-blue m-l font-weight-bold">{{ __('common.new') }}</small>
                                                    @endif
                                                </a>
                                            <br>
                                            <small>{{ __('manage/cp/exchange/distribution.distribute_to') }}: {{ $termsOfDistribution->serviceProvider ? $termsOfDistribution->serviceProvider->organization->name : '-' }} » {{ $termsOfDistribution->serviceProvider ? $termsOfDistribution->serviceProvider->name : '-' }}</small>
                                        </td>
                                        <td class="playlist-amount">
                                            <span class="amount">{{ $termsOfDistribution->playlists_count }}</span> <small>{{ __('common.playlists') }}</small>
                                        </td>
                                        <td class="playlist-amount">
                                            <div id="{{ $termsOfDistribution->id }}-{{ $termsOfDistribution->updated_at->timestamp }}"></div>
                                            <small class="timestamp" id="{{ $termsOfDistribution->id }}" dt="{{ $termsOfDistribution->updated_at->timestamp }}"></small>
                                        </td>
                                        <td class="status"><span class="label label-{{ $termsOfDistribution->status_color_class }}">{{ __('manage/cp/exchange/distribution.'.$termsOfDistribution->status) }}</span></td>
                                        <td class="playlist-actions">
                                            <div class="action-1 dropdown">
                                                <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">{{ __('common.actions') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                <ul class="dropdown-menu">
                                                    @if($termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_ACTIVE || $termsOfDistribution->status == \App\Models\TermsOfDistribution::STATUS_SP_PENDING)
                                                        <li>
                                                            <a href="{{ route('manage.cp.exchange.distribution.show', [$property_id, $termsOfDistribution->id]) }}">{{ __('manage/cp/exchange/distribution.view') }}</a>
                                                        </li>
                                                        <li>
                                                            {{ Form::open(['url' => route('manage.cp.exchange.distribution.duplicate', ['property_id'=> $property_id, 'id'=> $termsOfDistribution->id ]), 'method' => 'POST', 'id' => 'duplicate_'.$termsOfDistribution->id]) }}
                                                            {{ Form::close() }}
                                                            <a href="javascript:$('#duplicate_{{ $termsOfDistribution->id }}').submit();">{{ __('manage/cp/exchange/distribution.duplicate') }}</a>
                                                        </li>
                                                        <li>
                                                            {{ Form::open(['url' => route('manage.cp.exchange.distribution.revoke', ['property_id'=> $property_id, 'id'=> $termsOfDistribution->id ]), 'method' => 'PUT', 'id' => "only_revoke_".$property_id . "_" . $termsOfDistribution->id]) }}
                                                            {{ Form::close() }}
                                                            <a href="javascript:$('#only_revoke_{{ $property_id }}_{{ $termsOfDistribution->id }}').submit();">{{ __('manage/cp/exchange/distribution.revoke') }}</a>
                                                        </li>
                                                    @else
                                                        <li>    
                                                            <a href="{{ route('manage.cp.exchange.distribution.edit', [$property_id, $termsOfDistribution->id]) }}">
                                                                {{ __('common.edit') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            {{ Form::open(['url' => route('manage.cp.exchange.distribution.duplicate', ['property_id'=> $property_id, 'id'=> $termsOfDistribution->id ]), 'method' => 'POST', 'id' => 'duplicate_'.$termsOfDistribution->id]) }}
                                                            {{ Form::close() }}
                                                            <a href="javascript:$('#duplicate_{{ $termsOfDistribution->id }}').submit();">{{ __('manage/cp/exchange/distribution.duplicate') }}</a>
                                                        </li>
                                                    @endif
                                                    @if($termsOfDistribution->sp_property_id || $termsOfDistribution->sp_property_id == null)
                                                        <li>
                                                            <a href="javascript:void(0)" data-form-id="{{ $property_id }}_{{ $termsOfDistribution->id }}" onclick="setOtherFormId(this);">
                                                                {{ Form::open(['url' => route('manage.cp.exchange.distribution.destroy', ['property_id'=> $property_id, 'id'=> $termsOfDistribution->id ]), 'method' => 'DELETE', 'id' => "destory_".$property_id . "_" . $termsOfDistribution->id]) }}
                                                                {{ Form::hidden('isRevoke', '') }}
                                                                {{ Form::close() }}
                                                                {{ Form::open(['url' => route('manage.cp.exchange.distribution.revoke', ['property_id'=> $property_id, 'id'=> $termsOfDistribution->id ]), 'method' => 'PUT', 'id' => "revoke_".$property_id . "_" . $termsOfDistribution->id]) }}
                                                                {{ Form::close() }}
                                                                {{ __('common.delete') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach()
                            </tbody>
                        </table>
                        <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($termsOfDistributions->count()>0)
                                        {{
                                            __(
                                                'manage/cp/exchange/distribution.showing_from_to_terms',
                                                [
                                                    'from'=>$termsOfDistributions->firstItem(),
                                                    'to'=>$termsOfDistributions->lastItem(),
                                                    'total'=>$termsOfDistributions->total()
                                                ]
                                            )
                                        }}
                                        @else
                                            {{ __('manage/cp/exchange/distribution.no_terms') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        {{ $termsOfDistributions->appends(request()->input())->links() }}
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="active_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog s-width" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/exchange/distribution.revoke_terms_of_distribution') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <div>{{ __('manage/cp/exchange/distribution.active_delete_do_you_want_to_proceed') }}</div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="delete-tod-button" data-form-id="" id="revokeAndDestory" onclick="deleteTOD(this);">{{ __('manage/cp/exchange/distribution.revoke_and_delete') }}</a>
                            <button type="button" class="btn btn-secondary delete-tod-button" data-form-id="" id="revoke" onclick="deleteTOD(this);">{{ __('manage/cp/exchange/distribution.revoke_only') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="other_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog s-width" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/exchange/distribution.delete_terms_of_distribution') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <div>{{ __('manage/cp/exchange/distribution.other_delete_do_you_want_to_proceed') }}</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary delete-tod-button" data-form-id="" id="destory" onclick="deleteTOD(this);">{{ __('common.delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
    $(document).ready(function(){
        $('#date_sort').click(function(){
            if($(this).hasClass('sorting') || $(this).hasClass('sorting_desc')) {
                $('.date_sort').val('asc');
            } else {
                $('.date_sort').val('desc');
            }
            $("#terms_form").submit();
        });
    });
    function setActiveFormId(element) {
        var formId = element.getAttribute('data-form-id');
        $('.delete-tod-button').attr('data-form-id', formId);

        $('#active_delete').modal('show');
    }
    function setOtherFormId(element) {
        var formId = element.getAttribute('data-form-id');
        $('.delete-tod-button').attr('data-form-id', formId);

        $('#other_delete').modal('show');
    }
    function deleteTOD(element) {
        var formId = element.getAttribute('data-form-id');
        var id = element.getAttribute('id');
        if ('revokeAndDestory' == id) {
            $("input[name='isRevoke']").val(1);
            $('#' + 'destory_' + formId).submit();
        } else if('destory' == id) {
            $("input[name='isRevoke']").val(0);
            $('#' + id + '_' + formId).submit();
        } else {
            $('#' + id + '_' + formId).submit();
        }
    }
</script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
