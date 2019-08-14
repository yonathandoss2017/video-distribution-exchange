<div class="video-list spwp">
    <table class="table">
        <thead>
        <tr>
            <th></th>
            <th class="video-name" style="width: 40%">{{ __('common.playlist') }}</th>
            <th class="video-name" style="width: 40%">{{ __('manage/cp/exchange/request_logs.marketplace_terms') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($requestLog->playlists as $playlist)
            <tr>
                <td class="image-small">
                    <div class="video-img video-img-small">
                        <a href="{{ route('manage.cp.videos', [$property_id, 'playlist' => $playlist->id]) }}">
                            <img src="{{ \App\Services\Serve\PlaylistImageService::getImageUrl($playlist, $property_id) }}">
                        </a>
                    </div>
                </td>
                <td class="playlist-title">
                    <a href="{{ route('manage.cp.videos', [$property_id, 'playlist' => $playlist->id]) }}">{{ $playlist->name }}</a> <br/>
                    <small><b>{{ $playlist->entries_count }}</b> {{ __('common.videos') }}</small>
                </td>
                <td>{{ $playlist->marketplaceTerm ? optional($playlist->marketplaceTerm)->name : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-5">
            <div class="dataTables_info">
                {{ $requestLog->playlists->count() }} {{ __('manage/cp/exchange/request_logs.playlists_requested') }}
            </div>
        </div>
    </div>
</div>