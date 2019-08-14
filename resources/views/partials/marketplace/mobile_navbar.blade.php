<li class="nav-item">
  <a class="nav-link" href="{!! URL::to('/marketplace') !!}">{{ __('marketplace.marketplace') }}</a>
</li>
@include('partials.marketplace.left_nav_genre')
@include('partials.marketplace.left_nav_channel')
<li class="nav-item">
  <a class="nav-link" href="{!! URL::to('/manage') !!}">{{ __('marketplace.dashboard') }}</a>
</li>
<li class="user dropdown">
	@include('partials.marketplace.right_nav_myaccount')
</li>
