@extends('partials.layout_home')

@push('title')
    {{ __('manage/organization/property.empty_property') }} | {{ __('app.title') }}
@endpush

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
                @include('partials.errors', ['err_type' => 'header'])
                <div class="property-select main-page">
                    <div class="title-header">
                        <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                        <div class="title">{{ __('manage/organization/property.create_property') }}</div>
                    </div>
                    <div class= "description">{{ __('manage/organization/property.create_one_to_get_started') }}.</div>
                    <div class="ibox-content">
                        @can('organization-admin')
                        <a href="{{ route('manage.property.add') }}" class="btn btn-normal btn-m">{{ __('manage/organization/property.add_property') }}</a>
                        @endcan
                    </div>
                </div>
			</div>
			<div class="col-md-3"></div>
		</div>

	</div>
@stop