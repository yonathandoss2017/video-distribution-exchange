@extends('partials.layout_cp')

@push('title')
{{ __('app.title') }} | {{ $entry->name }}
@endpush

@section('content')

    <!-- Begin page content -->
    <div class="container hotspots">
        <div class="row">
            <div class="col-md-12">
                <div class="title-header ">
                    <div class="min-menu row">
                        <div class="col-md-8">
                            <div class="title">{{ $entry->name }}</div>
                        </div>

                    </div>
                </div>
                <div class="row">
                <div class="col-md-6">
                    <div class="video home viscovery">
                        <div class="video-grid embed-responsive embed-responsive-16by9" id="video">
                            <div class="viscovery-spot"><span class="viscovery-name"></span></div>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title"><h5>__('manage/cp/contents/videos.top_tags')</h5></div>
                            <div class="ibox-content tag">
                                <table class="table tag">
                                    <tbody>
                                        @php
                                            $position = 0;
                                        @endphp

                                        @foreach ($top_tags as $tag_key => $tag)
                                            @if ($position % 2 == 0)
                                            <tr>
                                            @endif

                                            <td>
                                                <a href="#" onclick="activeTag(this);" data-category-name="{{ $tag['category_name'] }}" data-tag-name="{{ $tag_key }}">
                                                    {{ $tag_key }}
                                                    <span class="label label-grey tag">{{ $tag['frames_count'] }}</span>
                                                </a>
                                            </td>

                                            @if ($position % 2 != 0)
                                            </tr>
                                            @endif

                                            @php
                                                $position++;
                                            @endphp
                                        @endforeach

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="viscovery-box">

                            <div class="tag-content">

                                <div class="tag-column">
                                    <div class="tag-title2"><div class="tag-header">__('manage/cp/contents/videos.categories')</div></div>
                                    <div class="tag-list">
                                        <ul> 
                                            @foreach ($analyze_results as $analyze_result)
                                                <li><a class="category-item" href="#" onclick="loadCategory(event, this);" data-key="{{ $analyze_result['category_name'] }}">{{ $analyze_result['category_name'] }} <span class="label label-grey tag">{{ $analyze_result['tags_count'] }}</span></a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="tag-column">
                                    <div class="tag-title2"><div class="tag-header">__('manage/cp/contents/videos.tags')</div></div>
                                    <div class="tag-list"> 
                                        <ul id="tag-list" class="hidden">
                                            @foreach ($tags as $groupTag)
                                                @foreach ($groupTag as $key=>$tag)
                                                <li>
                                                    <a href="#" class="tag-item" onclick="loadTag(event, this);" data-category-name="{{ $tag['category_name'] }}" data-tag-name="{{ $key }}">
                                                        {{ $key }}
                                                        <span class="label label-grey tag">{{ $tag['frames_count'] }}</span>
                                                    </a>
                                                </li>
                                                @endforeach
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="tag-column">
                                    <div class="tag-title2"><div class="tag-header">__('manage/cp/contents/videos.time_frames')</div></div>
                                    <div class="tag-list">

                                        <ul class="time_frame hidden">
                                            @foreach ($tags as $groupTag)
                                                @foreach ($groupTag as $frames)
                                                    @foreach ($frames['frames'] as $frame)
                                                    <li class="small_time_frame">
                                                        <a href="#" class="frame-item" onclick="loadFrame(event);" data-category-name="{{ $frame['category_name'] }}" data-tag-key="{{ $frame['tag_name'] }}" data-milliseconds="{{ $frame['milliseconds'] }}" data-frame-rect-percentage="{{ $frame['frame_rect_percentage'] }}">{{ $frame['frame_time'] }}</a>
                                                    </li>
                                                    @endforeach
                                                @endforeach
                                            @endforeach

                                        </ul>

                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>


@stop


@push('js')
{!! \App\Services\Serve\VideoPlayerService::player($property, $entry, "video") !!}
<script type="text/javascript">

    // Variable to collect active timeFrame
    var timeFrame = null,
        frames = null,
        tags = null,
        analyze = {!! json_encode($analyze_results); !!};

    // Listen to the playing video
    window.addEventListener("message",function(e){ 
        if(e.data.eventName == "currenttime") {
            var currentTime = Math.floor(e.data.eventValue) * 1000;
            
            $('.viscovery-spot').css({ 'display': 'none'});                

            if (frames != null) {
                frames.find(function(frame){
                    var duration = Math.floor(frame.milliseconds / 1000) * 1000;
                    
                    if ( duration === currentTime){
                        rectVar = frame.frame_rect_percentage.split(',');
                        drawRect( rectVar, frame.tag_name);
                    }
                });
            }
            
        }
    });

    function boldFrameItems(frameItems) {
        var count = frameItems.length;
        if (!count) {
            return;
        }

        // Active first items
        var firstItem = frameItems[0];
        resetClassFrameItem(firstItem.parentElement);

        for (var i = 1; i < count; i++) {
            var currentItem = frameItems[i];
            var prevItem = frameItems[i - 1];
            var liTag = currentItem.parentElement;

            resetClassFrameItem(liTag);
            if ($(currentItem).data('milliseconds') - $(prevItem).data('milliseconds') <= 1000) {
                $(liTag).addClass('small_time_frame');
            } else {
                if ($(prevItem.parentElement).hasClass('small_time_frame')) {
                    $(prevItem.parentElement).addClass('mb-15');
                }
            }
        }
        $('.time_frame').show();
    }

    function resetClassFrameItem(frameItem) {
        var item = $(frameItem);
        item.removeClass('frame-item');
        item.removeClass('mb-15');
        item.removeClass('small_time_frame');
    }

    function activeTag(element) {
        $('.time_frame').hide();
        var tagName = element.getAttribute('data-tag-name');
        var categoryName = element.getAttribute('data-category-name');

        $('a[data-key="' + categoryName + '"]').click();
        $('a.tag-item[data-tag-name="' + tagName + '"]').click();
    }

    function loadTag(event, element) {
        event.preventDefault();
        var categoryKey = $(element).attr('data-category-name');
        var tagKey = $(element).attr('data-tag-name');

        frames = tags[tagKey].frames;
    
        // Clear style active
        $('.tag-item').removeClass('active');
        $('.category-item').removeClass('active');

        $('a[data-tag-name="' + tagKey + '"]').each(function(pos, item) {
            item.classList.add('active');
        });

        $('a.category-item[data-key="' + categoryKey + '"]').each(function(pos, item) {
            item.classList.add('active');
        });

        $('.frame-item').each(function(pos, item) {
            if (item.getAttribute('data-tag-key') == tagKey && item.getAttribute('data-category-name') == categoryKey) {
                $(item.parentElement).show();
            } else {
                $(item.parentElement).hide();
            }
        });
        var frameItems = $('a.frame-item[data-tag-key="' + tagKey + '"]');
        boldFrameItems(frameItems);
    }

    function loadFrame(event) {
        event.preventDefault();
        var el = $(event.target);
        var datas = {
            categoryName: $(el).attr('data-category-name'),
            tagKey: $(el).attr('data-tag-key'),
            milliseconds: parseInt($(el).attr('data-milliseconds')),
            frameRect: $(el).attr('data-frame-rect-percentage'),
        };
        $('.viscovery-spot').css({ 'display': 'none'});

        window.player.play();
        window.player.seekTo(datas.milliseconds/1000);
        window.player.pause();

        var rect = datas.frameRect.split(',');
        drawRect(rect, datas.tagKey);
    }

    function drawRect( rect, tag) {
        if (rect.length === 4) $('.viscovery-spot').css({ 'display': 'block', 'left': rect[0] + '%', 'top': rect[1] + '%', 'width': rect[2] + '%', 'height': rect[3] + '%'}).children('.viscovery-name').html(tag);
    }

    function loadCategory(event, element) {
        event.preventDefault();
        $('.time_frame').hide();
        var categoryKey = $(element).attr('data-key');
        $('.category-item').removeClass('active');
        $(element).addClass('active');

        tags = analyze[categoryKey].tags;

        $('.tag-item').each(function(pos, item) {
            $(item).removeClass('active');
            if (item.getAttribute('data-category-name') == categoryKey) {
                $(item.parentElement).show();
            } else {
                $(item.parentElement).hide();
            }
        });
        $('#tag-list').show();
    }

</script>

@endpush
