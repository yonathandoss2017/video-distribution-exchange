
@foreach ($frames as $key => $groupFrame)
    <ul>
        @php
            $count = count($groupFrame);
        @endphp

        @for ($i = 0; $i < $count; $i++)
            @php $frame = $groupFrame[$i]; @endphp
            <li>
                <a href="#" class="frame-item" onclick="loadFrame(event);" data-category-name="{{ $frame['category'] }}" data-tag-key="{{ $frame['tag_name'] }}" data-milliseconds="{{ $frame['milliseconds'] }}" data-frame-rect="{{ $frame['frame_rect'] }}">{{ $frame['frame_time'] }}</a>
            </li>
        @endfor

    </ul>
@endforeach