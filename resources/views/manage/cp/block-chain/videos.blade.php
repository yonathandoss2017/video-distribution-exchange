<div class="video-list spwp">
    <table class="table">
        <thead>
            <tr>
                <th class="image"></th>
                <th class="video-name fx-video-name">{{ __('manage/cp/block-chain/block-chain.title_playlist') }}</th>
                <th>{{ __('manage/cp/block-chain/block-chain.last_updated') }}</th>
                <th style="width: 100px;">{{ __('common.status') }}</th>
                <th class="text-right">{{ __('common.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
            $status = [
                \App\Models\EntryAnzhengEvidence::STATUS_REQUEST_EVIDENCE => 'label-orange',
                \App\Models\EntryAnzhengEvidence::STATUS_EVIDENCE_DONE => 'label-blue',
                \App\Models\EntryAnzhengEvidence::STATUS_EVIDENCE_ERROR => 'label-error',
                \App\Models\EntryAnzhengEvidence::STATUS_REQUEST_DOWNLOAD => 'label-orange',
                \App\Models\EntryAnzhengEvidence::STATUS_EVIDENCE_READY => 'label-active',
                \App\Models\EntryAnzhengEvidence::STATUS_REQUEST_DOWNLOAD_ERROR => 'label-error',
            ];
            @endphp
            @foreach($entries as $entry)
                <tr>
                    <td class="image">
                        <div class="video-img"><a href="{{ route('manage.cp.video.edit', ['property_id'=> $property_id, 'id'=> $entry->id ]) }}"><img src="{{ App\Services\Serve\VideoImageService::getImageUrl($entry, $property_id, null, 240) }}" alt=""></a></div>
                    </td>
                    <td class="video-name fx-video-name"><a href="{{ route('manage.cp.video.edit', ['property_id'=> $property_id, 'id'=> $entry->id ]) }}">{{ $entry->name }}</a>
                        <div class="playlist-small">
                        <small><ul><li>{{ $playlist_evidence_request->playlist->name }}</li></ul></small>
                        </div>
                    </td>
                    <td class="date">
                        <div id="{{ $entry->id }}-{{ $entry->updated_at->timestamp }}"></div>
                        <small class="timestamp" id="{{ $entry->id }}" dt="{{ $entry->updated_at->timestamp }}"></small>
                    </td>
                    <td><span class="label {{ $status[$entry->anzhengEvidence->status] }}">{{ __('manage/cp/block-chain/block-chain.'.$entry->anzhengEvidence->statusDisplay()) }}</span></td>
                    <td>
                        @if (\App\Models\EntryAnzhengEvidence::STATUS_EVIDENCE_READY == $entry->anzhengEvidence->status)
                            <a href="{{ $entry->anzhengEvidence->receipt }}" class="btn btn-normal btn-m" target="_blank">{{ __('manage/cp/block-chain/block-chain.download_receipt') }}</a>
                        @elseif (\App\Models\EntryAnzhengEvidence::STATUS_EVIDENCE_DONE == $entry->anzhengEvidence->status) 
                            <a href="{{ route('manage.cp.block-chain.get-receipt', [$property_id, $entry->id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/block-chain/block-chain.get_receipt') }}</a>
                        @elseif (\App\Models\EntryAnzhengEvidence::STATUS_EVIDENCE_ERROR == $entry->anzhengEvidence->status)
                                {!! Form::open([
                                        'method'=>'POST',
                                        'url' => route('manage.cp.block-chain.store', [$property_id]),
                                        'class'=>'form-horizontal',
                                        'id' => 'add-form',
                                    ]) !!}
                                    <input type="hidden" name="selectedList" value="{{ $entry->id }}">
                                    <input type="hidden" name="playlistId" value="{{ $playlist_evidence_request->playlist->id }}">
                                    <input type="hidden" name="entity" value="{{ $entry->anzhengEvidence->entity }}">
                                    <button type="submit" class="btn btn-normal btn-m">{{ __('manage/cp/block-chain/block-chain.submit_for_evidence') }}</button>
                                {{ Form::close() }}
                        @elseif (\App\Models\EntryAnzhengEvidence::STATUS_REQUEST_DOWNLOAD_ERROR == $entry->anzhengEvidence->status) 
                            <a href="{{ route('manage.cp.block-chain.get-receipt', [$property_id, $entry->id]) }}" class="btn btn-normal btn-m">{{ __('manage/cp/block-chain/block-chain.get_receipt') }}</a>
                        @endif
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
                            'manage/cp/block-chain/block-chain.showing_from_to_evidences',
                            [
                                'from'=>$entries->firstItem(),
                                'to'=>$entries->lastItem(),
                                'total'=>$entries->total()
                            ]
                        )
                    }}
                @else
                    {{ __('manage/cp/block-chain/block-chain.no_videos') }}
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
<script type="text/javascript" src="{{ asset('/js/format_timezones.js') }}"></script>
