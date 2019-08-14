@extends('partials.layout_cp')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/marketplace_terms.marketplace_terms') }}
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
                        <div class="title">{{ __('manage/cp/exchange/marketplace_terms.marketplace_terms') }}</div>
                    </div>
                    <div class="col-md-9">
                        <div class="right-panel">
                            <a href="{{ route('manage.cp.exchange.marketplace-terms.create', $property_id) }}" class="btn btn-normal m-r">{{ __('manage/cp/exchange/marketplace_terms.new_marketplace_terms') }}</a>
                            {!! Form::open(['route'=>['manage.cp.exchange.marketplace-terms.index', $property_id],'method'=>'GET','class' => 'playlist-search', 'id' => 'terms_form']) !!}
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
                            {{ __('common.keywords') }} <div class="search-result-info"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current(). '?sort='.$sort }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>&nbsp;&nbsp;&nbsp;
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
                                <th class="playlist-title pl">{{ __('manage/cp/exchange/distribution.terms') }}</th>
                                <th style="width: 120px;">{{ __('common.playlists') }}</th>
                                <th style="width: 18%;">{{ __('manage/cp/exchange/marketplace_terms.allowed_regions') }}</th>
                                <th>{{ __('manage/cp/exchange/marketplace_terms.payment_mode') }}</th>
                                <th class="date {{ $sort == '' ? 'sorting' : ($sort == 'asc' ? 'sorting_asc' : 'sorting_desc') }}" id="date_sort" style="width: 15%">{{ __('common.last_updated') }}</th>
                                <th class="text-right">{{ __('common.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($terms as $term)
                                <tr>
                                    <td class="playlist-title pl">
                                        <a href="{{ route('manage.cp.exchange.marketplace-terms.show', [$property_id, $term->id]) }}">{{ $term->name }}</a>
                                    </td>
                                    <td class="playlist-amount">
                                        <span class="amount">{{ $term->playlists_count }}</span> <small>{{ __('common.playlists') }}</small>
                                    </td>
                                    <td style="width: 18%;">
                                        @foreach ($term->region_allowed as $region)
                                            {{ __('region.'.$region) }} {{ $loop->last ? '' : ',' }}
                                        @endforeach
                                    </td>
                                    <td>{{ __('term.payment_mode.'.$term->payment_mode) }}</td>
                                    <td class="date">
                                        <div id="{{ $term->id }}-{{ $term->updated_at->timestamp }}"></div>
                                        <small class="timestamp" id="{{ $term->id }}" dt="{{ $term->updated_at->timestamp }}"></small>
                                    </td>
                                    <td class="playlist-actions">
                                        <a href="{{ route('manage.cp.exchange.marketplace-terms.edit', [$property_id, $term->id]) }}" class="btn btn-normal btn-m">{{ __('common.edit') }}</a>
                                        <a href="javascript:void(0)" class="btn btn-normal btn-m delete" data-form-id="{{ $property_id }}_{{ $term->id }}" onclick="setDeleteFormId(this);">
                                            {{ Form::open(['url' => route('manage.cp.exchange.marketplace-terms.destroy', ['property_id'=> $property_id, 'id'=> $term->id ]), 'method' => 'DELETE', 'id' => "destory_".$property_id . "_" . $term->id]) }}
                                            {{ Form::close() }}
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info">
                                        @if ($terms->count()>0)
                                            {{
                                                __(
                                                    'manage/cp/exchange/distribution.showing_from_to_terms',
                                                    [
                                                        'from'=>$terms->firstItem(),
                                                        'to'=>$terms->lastItem(),
                                                        'total'=>$terms->total()
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
                                        {{ $terms->appends(request()->input())->links() }}
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="delete_term" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog s-width" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('manage/cp/exchange/marketplace_terms.delete_terms_of_distribution') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div>{{ __('manage/cp/exchange/distribution.other_delete_do_you_want_to_proceed') }}</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary delete-term-button" data-form-id="" onclick="deleteTerm(this);">{{ __('common.delete') }}</button>
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
    function setDeleteFormId(element) {
        $('.delete-term-button').attr('data-form-id', element.getAttribute('data-form-id'));
        $('#delete_term').modal('show');
    }
    function deleteTerm(element) {
        var formId = element.getAttribute('data-form-id');
        $('#destory_' + formId).submit();
    }
</script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
@endpush
