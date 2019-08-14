<div class="container-fluid breadcrumb-header">
	<div class="container">
		<div class="row breadcrumb-playlist">
			<div class="col-md-6">
				<div class="title-breadcrumb">{{ $playlist -> name }}</div>
			</div>
			@unless(Request::is('*/checkout'))
			<div class="col-md-6 right detail-page">
                @if($term)
				    <a href="{{ route('marketplace.playlist.checkout.show',['playlist_id' => $playlist -> id]) }}"><button type="submit" class="btn btn-primary">{{ __('marketplace.request_license') }}</button></a>
                @endif
			</div>
			@endunless
		</div>
	</div>
</div>