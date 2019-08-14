<div class="video-list spwp">
    <table class="table">
        <thead>
            <tr>
                <th class="image"></th>
                <th class="video-name fx-video-name">{{ __('manage/cp/ivideoadd/ivideoadd.title_playlist') }}</th>
                <th>{{ __('manage/cp/ivideoadd/ivideoadd.scenes') }}</th>
                <th>{{ __('manage/cp/ivideoadd/ivideoadd.last_updated') }}</th>
                <th style="width: 100px;">{{ __('common.status') }}</th>
                <th class="text-right">{{ __('common.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $status = [
                \App\Models\Entry::DPP_STATUS_PROCESSING => 'label-grey',
                \App\Models\Entry::DPP_STATUS_REVIEW => 'label-orange',
                \App\Models\Entry::DPP_STATUS_PUBLISHED => 'label-active',
            ];
            @endphp
            @foreach( $entries as $entry)
                <tr>
                    <td class="image">
                        <div class="video-img"><a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $entry->id }}"><img src="{{ \App\Services\Serve\VideoImageService::getImageUrl($entry, $property_id, null, 240) }}" alt="" onclick="getPlayer('{{ $entry->id }}')"></a></div>
                    </td>
                    <td class="video-name fx-video-name"><a href="javascript:void(0)" data-toggle="modal" data-target="#modal-video{{ $entry->id }}" onclick="getPlayer('{{ $entry->id }}')">{{ $entry->name }}</a>
                        <div class="playlist-small">
                        <small><ul><li>{{ $playlist->name }}</li></ul></small>
                        </div>
                    </td>
                    <td class="playlist-amount">
                        <span class="amount">{{ $entry->dpp_status==\App\Models\Entry::DPP_STATUS_PROCESSING?0:$entry->scenes_count }}</span> <small>{{ __('manage/cp/ivideoadd/ivideoadd.available') }}</small>
                    </td>
                    <td>{{ $entry->updated_at->toFormattedDateString() }}<br>
                        <small class="timestamp" id="{{ $entry->id }}" dt="{{ $entry->updated_at->timestamp }}"></small>
                    </td>
                    <td><span class="label {{ $status[$entry->dpp_status] }}">{{ __('manage/cp/ivideoadd/ivideoadd.status_'.$entry->dpp_status) }}</span></td>
                    <td>
                        @if ($entry->dpp_status == \App\Models\Entry::DPP_STATUS_REVIEW)
                        <form method="POST" action="{{ route('manage.cp.dpp.entry.destroy', [$property_id,$playlist->id,$entry->id]) }}" accept-charset="UTF-8" onsubmit="return ConfirmDelete()">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-normal btn-m delete">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('manage.cp.dpp.scenes.index', [$property_id, $playlist->id, $entry->id]) }}" class="btn btn-normal btn-m">{{ $entry->dpp_status == \App\Models\Entry::DPP_STATUS_PUBLISHED?__('common.view'):__('common.manage') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info">
                @if ($entries->count()>0)
                    {{
                        __(
                            'manage/cp/ivideoadd/ivideoadd.showing_from_to_playlists',
                            [
                                'from'=>$entries->firstItem(),
                                'to'=>$entries->lastItem(),
                                'total'=>$entries->total()
                            ]
                        )
                    }}
                @else
                    {{ __('manage/cp/ivideoadd/ivideoadd.no_videos') }}
                @endif
            </div>
        </div>

        <div class="col-sm-7">
            <div class="dataTables_paginate paging_simple_numbers">
                {{ $entries->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
