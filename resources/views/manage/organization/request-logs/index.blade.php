@extends('partials.layout_home')

@push('title')
    {{ __('app.title') }} | {{ __('manage/cp/exchange/request_logs.request_logs') }}
@endpush

@push('script-head')
    <script type="text/javascript" src="https://momentjs.com/downloads/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
@endpush

@section('content')
<!-- Begin page content -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="title-header">
                <div class="min-menu row">
                    <div class="col-md-3">
                        <div class="title">{{ __('manage/cp/exchange/request_logs.request_logs') }}</div>
                    </div>
                    <div class="col-md-9">
                        <div class="right-panel">
                            <div class="status-float">
                                <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown">{{ __('common.bulk_action') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0)" onclick="bulk_action(0);">{{ __('manage/cp/contents/request_logs.approve') }}</a></li>
                                    <li><a href="javascript:void(0)" onclick="bulk_action(1);">{{ __('manage/cp/contents/request_logs.reject') }}</a></li>
                                </ul>
                            </div>

                            {!! Form::open(['url'=>route('manage.organization.request-logs.index'),'method'=>'GET', 'class' => 'playlist-search']) !!}
                            <div class="input-group sr">
                                <input type="text" placeholder="{{ __('common.search') }}" class="input-sm form-control" name="search" value="">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-sm btn-normal"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </span>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="col-md-12 search-result">
                        @if($search)
                            {{ __('common.keywords') }} <div class="search-result-info filter-margin-right"><span class="search-result-text">{{ $search }} </span><span class="search-close"><a href="{{ url()->current() }}"><i class="fa fa-times" aria-hidden="true"></i></a></span></div>
                        @endif
                    </div>
                </div>
            </div> <!-- /.title-header -->
            <div class="ibox">
                <div class="ibox-content">
                  <div class="video-list spwp">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="select-all">
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input">
                              <span class="custom-control-indicator"></span>
                            </label>
                          </th>
                          <th class="image">
                              <select class='select2' name="" id="select-all">
                                  <option value="0">{{ __('manage/cp/contents/request_logs.current_page') }}</option>
                                  <option value="1">{{ __('manage/cp/contents/request_logs.all_page') }}</option>
                              </select>
                          </th>
                          <th class="playlist-title pl" style="width: 25%;">{{ __('common.title') }}</th>
                          <th>{{ __('common.videos') }}</th>
                          <th>{{ __('common.owner_account') }}</th>
                          <th>{{ __('manage/cp/exchange/request_logs.requested_by') }}</th>
                          <th>{{ __('common.date') }}</th>
                          <th class="text-right">{{ __('common.actions') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($pending_list as $pending_item)
                        <tr>
                          <td>
                            <label class="custom-control custom-checkbox">
                              <input type="checkbox" class="custom-control-input item-checkbox" value="{{ $pending_item->id }}">
                              <span class="custom-control-indicator"></span>
                            </label>
                          </td>
                          <td class="image">
                            <div class="video-img">
                                <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($pending_item, $pending_item->property_id, null, 120) }}">
                            </div>
                          </td>
                          <td class="playlist-title">{{ $pending_item->name }}</td>
                          <td class="playlist-title">
                            <span class="amount">{{ $pending_item->entries_count }}</span> <small>{{ __('common.videos') }}</small>
                          </td>
                          <td>
                            <div>{{ $pending_item->contentProvider->name }}</div>
                            <small>{{ $pending_item->creator->name }}</small>
                          </td>
                          <td class="playlist-title">
                            <a>{{ $pending_item->creator->name }}</a> <br>
                                <small>{{ $pending_item->creator->email }}</small>
                          </td>
                          <td>
                              <div id="{{ $pending_item->type }}-{{ $pending_item->id }}-{{ $pending_item->created_at->timestamp }}"></div>
                              <small class="timestamp" id="{{ $pending_item->type }}-{{ $pending_item->id }}" dt="{{ $pending_item->created_at->timestamp }}"></small>
                          </td>
                          <td class="playlist-actions">
                            <div class="action-1 dropdown">
                              <a href="#" class="btn btn-normal dropdown-toggle m-r" data-toggle="dropdown" aria-expanded="false">{{ __('manage/cp/exchange/request_logs.actions') }} <i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                <ul class="dropdown-menu">
                                    <li>
                                      <form method="post" action="{{ route('manage.organization.request-logs.approve', $pending_item->id) }}" id="approve-form-{{ $pending_item->id }}">
                                          {{ csrf_field() }}
                                      </form>
                                      <a href="javascript:void(0);" class="approve-btn" data-id="{{ $pending_item->id }}">{{ __('manage/cp/exchange/request_logs.approve') }}</a>
                                    </li>
                                    <li>
                                        <form method="post" action="{{ route('manage.organization.request-logs.reject', $pending_item->id) }}" id="reject-form-{{ $pending_item->id }}">
                                          {{ csrf_field() }}
                                        </form>
                                        <a href="javascript:void(0);" class="reject-btn" data-id="{{ $pending_item->id }}">{{ __('manage/cp/exchange/request_logs.reject') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('manage.organization.request-logs.comment.edit', $pending_item->id) }}">{{ __('manage/cp/exchange/request_logs.comments') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('manage.organization.request-logs.playlist.show', $pending_item->id) }}">{{ __('manage/cp/exchange/request_logs.view') }}</a>
                                    </li>
                                </ul>
                            </div>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>

                    <div class="row">
                      <div class="col-md-12"><span>{{ __('manage/cp/contents/request_logs.have_chosen') }} <span id="request-log-selected-count">0</span> {{ __('manage/cp/contents/request_logs.request_logs') }}</span></div>
                    </div>

                    <div class="row">
                            <div class="col-sm-5">
                              <div class="dataTables_info">
                                  @if ($pending_list->count()>0)
                                      {{
                                          __(
                                              'manage/cp/contents/request_logs.showing_from_to_request_logs',
                                              [
                                                  'from'=>$pending_list->firstItem(),
                                                  'to'=>$pending_list->lastItem(),
                                                  'total'=>$pending_list->total()
                                              ]
                                          )
                                      }}
                                  @else
                                      {{ __('manage/cp/contents/request_logs.no_request_logs') }}
                                  @endif
                              </div>
                              <!-- the content is "No Logs" when there's no requests -->
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers">
                                  {{ $pending_list->appends(request()->input())->links() }}
                                </div>
                            </div>
                    </div>
                  </div>
                </div>
            </div> <!-- /.ibox -->
        </div>
        <div class="modal fade" id="bulk-approve" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            {{ Form::open(['url'=>route('manage.organization.request-logs.approve.bulk')]) }}
            {{ Form::hidden('approve_items','') }}
            {{ Form::hidden('select_all','') }}
            {{ Form::hidden('all_ids', $all_ids) }}
            {{ method_field('POST') }}
            <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">{{ __('manage/cp/contents/request_logs.bulk_approve_request_logs') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>{{ __('manage/cp/contents/request_logs.action_is_not_reversible') }}</div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-secondary">{{ __('manage/cp/contents/request_logs.bulk_approve') }}</button>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <div class="modal fade" id="bulk-reject" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            {{ Form::open(['url'=>route('manage.organization.request-logs.reject.bulk')]) }}
            {{ Form::hidden('reject_items','') }}
            {{ Form::hidden('select_all','') }}
            {{ Form::hidden('all_ids', $all_ids) }}
            {{ method_field('POST') }}
            <div class="modal-dialog s-width" role="document">
                <div class="modal-content">
                <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">{{ __('manage/cp/contents/request_logs.bulk_reject_request_logs') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>{{ __('manage/cp/contents/request_logs.action_is_not_reversible') }}</div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-secondary">{{ __('manage/cp/contents/request_logs.bulk_reject') }}</button>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop

@push('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
<script type="text/javascript">
    $(function() {
        $('.select2').select2({
            minimumResultsForSearch: Infinity,
        });

        $('.approve-btn').click(function(){
            $('#approve-form-' + $(this).data('id')).submit();
        });
        $('.reject-btn').click(function(){
            $('#reject-form-' + $(this).data('id')).submit();
        });
        $('.ai-review-btn').click(function(){
            $('#ai-review-form-' + $(this).data('id')).submit();
        });

        $("td input[type=checkbox]").click(function () {
            if ($(this).is(":checked")) {
                $('#request-log-selected-count').html(parseInt($('#request-log-selected-count').html()) + 1);
            } else {
                if ($(".select-all input[type=checkbox]").is(":checked")) {
                    var this_page_select = 0;
                    $('.item-checkbox').each(function(){
                        if ($(this).is(':checked')) {
                            this_page_select++;
                        }
                    });
                    $(".select-all input[type=checkbox]").prop('checked', false);
                    $('#request-log-selected-count').html(this_page_select);
                } else {
                    $('#request-log-selected-count').html(parseInt($('#request-log-selected-count').html()) - 1);
                }
                $('#select-all').val(0);
                $('#select-all').trigger('change');
            }
        });

        $('#select-all').change(function () {
            var this_page_select = 0;
            $('.item-checkbox').each(function(){
                if ($(this).is(':checked')) {
                    this_page_select++;
                }
            });
            if ($(".select-all input[type=checkbox]").is(":checked")) {
                if (1 == $(this).val()) {
                    $('#request-log-selected-count').html(parseInt({{ $pending_list->total() }}) - parseInt({{ $pending_list->count() }}) + this_page_select );
                } else {
                    $('#request-log-selected-count').html(this_page_select);
                }
            } else {
                $('#request-log-selected-count').html(this_page_select);
            }
        });

        $(".select-all input[type=checkbox]").click(function() {
            if($(this).is(":checked")) {
                $("td input[type=checkbox]").prop("checked", true)
                $("td input[type=checkbox]").prop("checked", true);
                var select_all = $('#select-all').val();
                if (0 == select_all) {
                    $('#request-log-selected-count').html({{ $pending_list->count() }});
                } else if (1 == select_all) {
                    $('#request-log-selected-count').html({{ $pending_list->total() }});
                }
            } else {
                $("td input[type=checkbox]").prop("checked", false)
                $("td input[type=checkbox]").prop("checked", false);
                $('#request-log-selected-count').html(0);
            }
        });
    });
    function bulk_action(bulk_type) {
        var item_ids = '';
        var select_all = $('#select-all').val();
        if (!$(".select-all input[type=checkbox]").is(":checked")) {
            select_all = 0;
        }

        $('.item-checkbox').each(function(){
            if ($(this).is(':checked')) {
                if (item_ids) {
                    item_ids += ',' + $(this).val();
                } else {
                    item_ids += $(this).val();
                }
            }
        });
        if (!item_ids) {
            alert("{{ __('manage/cp/contents/request_logs.no_record_selected') }}");
            return false;
        }

        $("input[name = 'select_all']").val(select_all);

        if (!bulk_type) {
            $("input[name = 'approve_items']").val(item_ids);
            $('#bulk-approve').modal('show');
        } else if (bulk_type == 2) {
            $("input[name = 'ai_review_items']").val(item_ids);
            $('#bulk-ai-review').modal('show');
        } else {
            $("input[name = 'reject_items']").val(item_ids);
            $('#bulk-reject').modal('show');
        }
    }
</script>
@endpush
