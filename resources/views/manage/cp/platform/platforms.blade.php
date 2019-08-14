@extends('partials.layout_cp')

@push('title')
	{{ __('app.title') }} | {{ __('manage/cp/sources/oauth_settings.oauths') }}
@endpush

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="title-header ">
				<div class="title">{{ __('manage/cp/sources/oauth_settings.oauths') }}</div>
			</div>

			<div class="row platforms row-eq-height">
                <div class="col-md-4 pt-a">
                    <div class="property-select main-page">
                        <div class="title-header">
                            <i aria-hidden="true"><img class="ali-oss-logo" src='/images/oss-logo.png'/></i>
                            <div class="title">{{ __('manage/cp/sources/oauth_settings.oss_oauth') }}</div>
                        </div>
                        <div class= "description">{{ __('manage/cp/sources/oauth_settings.connect_oss') }}</div>
                        <div class="ibox-content">
							@if(!$platformOss)
								<a href="{{ route('manage.cp.oauths.add.alioss', [$property_id]) }}" class="btn btn-normal btn-m">{{ __('common.connect') }}</a>
							@else
								<a href="{{ route('manage.cp.oauths.show.alioss', [$property_id, $platformOss->id]) }}" class="btn btn-normal btn-m">{{ __('common.view') }}</a>
							@endif
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>

@stop
