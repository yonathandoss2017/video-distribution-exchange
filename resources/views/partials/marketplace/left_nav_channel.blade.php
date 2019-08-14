<li class="nav-item all-channels">
 <div class="dropdown"><a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ __('marketplace/common.channels') }}<i class="fa fa-caret-down" aria-hidden="true"></i></a>
     <ul class="dropdown-menu multi-column columns-2-wide">
         <div class="row">
            @for ($i = 0; $i < $cps_count; $i += 2)
            <div class="col-sm-6 no-padding-right">
                <ul class="multi-column-dropdown">
                    <li><a href="/marketplace/cp-detail?propertyId={{ $cps[$i]->id }}">{{ $cps[$i]->name }}</a></li>
                </ul>
            </div>
                @if (isset($cps[$i+1]))
                <div class="col-sm-6 no-padding-left">
                    <ul class="multi-column-dropdown">
                        <li><a href="/marketplace/cp-detail?propertyId={{ $cps[$i+1]->id }}">{{ $cps[$i+1]->name }}</a></li>
                    </ul>
                </div>
                @endif
            @endfor
         </div>
     </ul>
	</div>
</li>
