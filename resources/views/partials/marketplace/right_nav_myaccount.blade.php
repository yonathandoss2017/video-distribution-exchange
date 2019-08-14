<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">	
	{{ __('marketplace/common.my_account') }}<i class="fa fa-caret-down" aria-hidden="true"></i>
</a>
<ul class="dropdown-menu">
	<li><a href="{!! URL::to('/manage/profile') !!}">{{ __('marketplace/common.profile') }}</a></li>
	<li><a href="{!! URL::to('/logout') !!}">{{ __('marketplace/common.logout') }}</a></li>
</ul>