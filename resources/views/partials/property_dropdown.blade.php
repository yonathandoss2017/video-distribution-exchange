@if (count($navbarDetails['organizations']) == 0)
    <div class="service-platform{{ is_null($navbarDetails['property']) ? ' active' : '' }}">
        <a href="{{ route('manage.property.select') }}">{{ $navbarDetails['org_name'] }}</a>
        @if (isset($navbarDetails['property']))
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        @endif
    </div>
@else
    <div class="service-platform dropdown{{ is_null($navbarDetails['property']) ? ' active' : '' }}">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $navbarDetails['org_name'] }}
            <i class="fa fa-caret-down" aria-hidden="true"></i>
        </a>
        <ul class="dropdown-menu">
            @foreach ($navbarDetails['organizations'] as $organization)
                <li><a href="{{ route('manage.organization.select', $organization->id) }}">{{ $organization->name }}</a></li>
            @endforeach
        </ul>
        @if (isset($navbarDetails['property']))
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        @endif
    </div>
@endif
    

@if (isset($navbarDetails['property']))
    <div class="service-p active dropdown">
        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $navbarDetails['property']->name }} <small class="label {{ $navbarDetails['property']->color }}">{{ $navbarDetails['types'][$navbarDetails['property']->display_type] }}</small>
            <i class="fa fa-caret-down" aria-hidden="true"></i>
        </a>
            <ul class="dropdown-menu">
                @foreach ($navbarDetails['properties'] as $property)
                    @php
                        $type = str_replace('_', '-', $property->type);
                        $color = 'cp';
                        switch ($type) {
                            case 'cp':
                                $route = "manage.cp.playlists.index";
                                break;
                            case 'sp':
                                $color = 'sp-wp';
                                $route = "manage.sp.playlists.index";
                                break;
                            case 'sp-plus':
                                $route = "manage.cp.sources";
                                break;
                            default:
                                $route = "manage.cp.sources";
                        }
                    @endphp
                    <li><a href="{{ route($route, $property->id) }}">{{ $property->name }} <small class="label {{ $color }}">{{ $navbarDetails['types'][$type] }}</small></a></li>
                @endforeach
            </ul>
    </div>
@endif
