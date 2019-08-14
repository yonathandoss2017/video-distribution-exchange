@extends('partials.layout_home')

@push('title')
    {{ __('manage/organization/property.select_property') }} | {{ __('app.title') }}
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                @include('partials.errors', ['err_type' => 'header'])
                <div class="property-select">
                    <div class="title-header">
                        @can('organization-admin')
                            <a href="{{ route('manage.property.add') }}" class="btn btn-normal btn-m">{{ __('manage/organization/property.add_property') }}</a>
                        @endcan
                        <div class="title">{{ __('manage/organization/property.select_property') }}</div>
                    </div>
                    <div class= "description">{{ __('manage/organization/property.select_one_to_get_started') }}</div>
                    <div class="ibox-content">
                        <ul class="property m-t small-list">
                            @foreach ($properties as $property)
                                @php
                                    $type = str_replace('_', '-', $property->type);
                                    if (!array_key_exists($type, $types)) {
                                        $types[$type] = $type;
                                    }

                                    if (array_key_exists($property->type, $routes)) {
                                        $route = $routes[$property->type];
                                    } else {
                                        $route = "manage.cp.settings";
                                    }
                                @endphp
                                <li>
                                    <a href="{{ route($route, $property->id) }}" class="check-link">{{ $property->name }} <small class="label {{ $type }}">{{ $types[$type] }}</small></a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-3"></div>
        </div>

    </div>
@stop
