<div class="container-fluid playlist-breadcrumb">
	<div class="container">
		<div class="row">
			<div class="col-md-6 page-title"><span>@stack('page-title')</span></div>
			<nav aria-label="breadcrumb" class="col-md-6">
				<div class="d-flex justify-content-end flex-wrap">
					<div class="home-item">
						<a href="{!! URL::to('/marketplace') !!}" class="text-white">{{ __('marketplace/common.home') }}</a>
						<span class="breadcrumb-divider">/</span>
					</div>
					<div class="breadcrumb-item active ellipsis-300" aria-current="page"><span>@stack('breadcrumb-item-active')</span></div>
				</div>
			</nav>
		</div>
	</div>
</div>
