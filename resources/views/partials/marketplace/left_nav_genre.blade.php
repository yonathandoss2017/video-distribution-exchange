<li class="nav-item">
 <div class="dropdown"><a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ __('marketplace/common.genres') }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
     <ul class="dropdown-menu multi-column columns-2">
         <div class="row">
             @php
                 $genres = collect(config('enums.genre'));
            @endphp
            @foreach ($genres->chunk(4) as $genreGroup)
                @if($loop->iteration % 2 == 1)
                    @php $paddingClass = 'no-padding-right' @endphp
                @else
                    @php $paddingClass = 'no-padding-left' @endphp
                @endif
                <div class="col-sm-6 {{ $paddingClass }}">
                    <ul class="multi-column-dropdown">
                        @foreach ($genreGroup as $key => $genre)
                            <li>
                                <a href="{{ route('marketplace.search', ['genre' => $key]) }}">
                                    {{ isset($genre['text']) ? __('marketplace/common.'.$genre['text']) : ''}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
         </div>
     </ul>
    </div>
</li>
