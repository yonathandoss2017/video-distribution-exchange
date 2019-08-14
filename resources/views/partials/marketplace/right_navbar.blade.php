<a href="{!! URL::to('/manage') !!}" class="btn btn-normal btn-m">{{ __('marketplace/common.dashboard') }}</a>
<div class="user dropdown">
	@include('partials.marketplace.right_nav_myaccount')
</div>
<div class="user dropdown local">
  @include('partials.marketplace.right_nav_language')
</div>
