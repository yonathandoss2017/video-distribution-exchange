<div id="section-filter" class="welcome-content">
	<div class="welcome-title">
		@yield('welcome-title')
	</div>
	@php
		$genres = collect(config('enums.genre'));
	@endphp
	<div class="search-filter" id="searchform-holder">
        <div class="load-content">
            <div class="loader"></div>
            <div class="text-center animated flash infinite">{{ __('marketplace/common.loading') }}...</div>
        </div>
	</div>

	<search-form genres="{{ collect(config('enums.genre')) }}"></search-form>

	<!-- related-result after search -->
	@yield('relate-result')
</div>
