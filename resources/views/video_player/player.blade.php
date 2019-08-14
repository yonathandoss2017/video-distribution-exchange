<!-- MODAL -->
<div class="modal fade video" id="modal-video{{ $entry->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-video-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeVideo()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-video">
                    <div class="embed-responsive embed-responsive-16by9" id="video"></div>
                </div>
                <div class="modal-details">
                    <h5 class="modal-title">{{ $entry->name }}</h5>

                    <div class="video-header">{{ __('manage/cp/contents/videos.descriptions') }}</div>
                    <div class="description video">{{ $entry->description }}</div>

                    <div class="video-header-m-t">{{ __('manage/cp/contents/videos.tags') }}</div>
                    <div class="playlist-small">
                        <small>
                            <ul>
                                @if (isset($entry->metadata))
                                    @foreach(explode(',',$entry->metadata->tags) as $tag)
                                        <li>{{ $tag }}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!! \App\Services\Serve\VideoPlayerService::player($property, $entry, "video") !!}
