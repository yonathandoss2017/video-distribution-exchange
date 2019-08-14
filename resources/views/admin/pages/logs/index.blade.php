@extends('admin.layout')
@push('title') {{ __('admin/header.logs') }} | {{ __('app.title') }} @endpush
@section('content')
    <style>
        #table-log {
            font-size: 0.85rem;
        }

        .sidebar {
            font-size: 0.85rem;
            line-height: 1;
        }

        .btn {
            font-size: 0.7rem;
        }

        .stack {
            font-size: 0.85em;
        }

        .date {
            min-width: 75px;
        }

        .text {
            word-break: break-all;
        }

        a.llv-active {
            z-index: 2;
            background-color: #f5f5f5;
            border-color: #777;
        }

        .list-group-item {
            word-wrap: break-word;
        }

        .folder {
            padding-top: 15px;
        }

        .div-scroll {
            height: 80vh;
            overflow: hidden auto;
        }
        .nowrap {
            white-space: nowrap;
        }
    </style>
    <div class="col-md" style="text-align: center; margin-bottom: 20px;">
        <div class="btn-group" role="group" aria-label="Basic example">
            <a href="{{ route('admin.logs.node1') }}" class="btn btn-secondary {{ (Request::is('admin/logs/node1')) ? 'active' : '' }}">{{ __('admin/log.node1') }}</a>
            <a href="{{ route('admin.logs.node2') }}" class="btn btn-secondary {{ (Request::is('admin/logs/node2')) ? 'active' : '' }}">{{ __('admin/log.node2') }}</a>
        </div>
    </div>
    <div class="row">
        <div class="col sidebar mb-3">
            <div class="list-group div-scroll">
                @foreach($data['folders'] as $folder)
                    <div class="list-group-item">
                        <a href="?f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}">
                            <span class="fa fa-folder"></span> {{$folder}}
                        </a>
                        @if ($data['current_folder'] == $folder)
                            <div class="list-group folder">
                                @foreach($data['folder_files'] as $file)
                                    <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}&f={{ \Illuminate\Support\Facades\Crypt::encrypt($folder) }}"
                                       class="list-group-item @if ($data['current_file'] == $file) llv-active @endif">
                                        {{$file}}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
                @foreach($data['files'] as $file)
                    <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt($file) }}"
                       class="list-group-item @if ($data['current_file'] == $file) llv-active @endif">
                        {{$file}}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="col-10 table-container">
            @if ($data['logs'] === null)
                <div>
                    {{ __('admin/log.empty_log_warning') }}
                </div>
            @else
                <table id="table-log" class="table table-striped" data-ordering-index="{{ $data['standardFormat'] ? 2 : 0 }}">
                    <thead>
                    <tr>
                        @if ($data['standardFormat'])
                            <th>{{ __('admin/common.level') }}</th>
                            <th>{{ __('admin/common.date') }}</th>
                        @else
                            <th>{{ __('admin/log.line_number') }}</th>
                        @endif
                        <th>{{ __('admin/common.content') }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($data['logs'] as $key => $log)
                        <tr data-display="stack{{{$key}}}">
                            @if ($data['standardFormat'])
                                <td class="nowrap text-{{{$log['level_class']}}}">
                                    <span class="fa fa-{{{$log['level_img']}}}" aria-hidden="true"></span>&nbsp;&nbsp;{{$log['level']}}
                                </td>
                            @endif
                            <td class="date">{{{$log['date']}}}</td>
                            <td class="text">
                                @if ($log['stack'])
                                    <button type="button"
                                            class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
                                            data-display="stack{{{$key}}}">
                                        <span class="fa fa-search"></span>
                                    </button>
                                @endif
                                {{{$log['text']}}}
                                @if (isset($log['in_file']))
                                    <br/>{{{$log['in_file']}}}
                                @endif
                                @if ($log['stack'])
                                    <div class="stack" id="stack{{{$key}}}"
                                         style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
            <div class="p-3">
                @if($data['current_file'])
                    <a href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt($data['current_file']) }}{{ ($data['current_folder']) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($data['current_folder']) : '' }}">
                        <span class="fa fa-download"></span> {{ __('admin/log.file_download') }}
                    </a>
                    -
                    <a id="delete-log" href="?del={{ \Illuminate\Support\Facades\Crypt::encrypt($data['current_file']) }}{{ ($data['current_folder']) ? '&f=' . \Illuminate\Support\Facades\Crypt::encrypt($data['current_folder']) : '' }}">
                        <span class="fa fa-trash"></span> {{ __('admin/log.file_delete') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
@stop
@push('foot-scripts')
    <!-- Datatables -->
    <script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.table-container tr').on('click', function () {
                $('#' + $(this).data('display')).toggle();
            });
            $('#table-log').DataTable({
                "order": [$('#table-log').data('orderingIndex'), 'desc'],
                "stateSave": true,
                "stateSaveCallback": function (settings, data) {
                    window.localStorage.setItem("datatable", JSON.stringify(data));
                },
                "stateLoadCallback": function (settings) {
                    var data = JSON.parse(window.localStorage.getItem("datatable"));
                    if (data) data.start = 0;
                    return data;
                }
            });
            $('#delete-log, #clean-log, #delete-all-log').click(function () {
                return confirm('Are you sure?');
            });
        });
    </script>
@endpush