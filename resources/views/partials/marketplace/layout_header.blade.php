<div class="header-wraper">
	<div class="container">
		@include('partials.marketplace.navbar_home')

		@include('partials.marketplace.layout_search')
	</div>

	@yield('search-result')

	@yield('polygon')

</div>
