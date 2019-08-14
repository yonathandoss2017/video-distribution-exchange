<div class="main-header desktop">
	<div class="logo-FE">
        <a href="{!! URL::to('/marketplace') !!}"><img src="/images/app/logo-dashboard.png"></a>
    </div>
    <nav class="navbar navbar-light navbar-toggleable-md menu">
		<ul class="navbar-nav nav-main">

			@include('partials.marketplace.left_navbar')

		</ul>
		<div class="search-playlists">
			<form class="search-marketplace" action="/marketplace/search" method="get">
				<div class="input-group">
					<input type="search" placeholder="Search" name="keywords" class="input-sm form-control search-playlist" value="{{ app('request')->input('keyword') }}">
					<span class="input-group-btn">
						<button type="submit" class="btn btn btn-search global-search global-search"><i class="fa fa-search" aria-hidden="true"></i></button>
					</span>
				</div>
			</form>
		</div>
		<div class="right-menu">
			<div class="pull-left request-icon">
				<a href="{{ route('marketplace.checkout') }}">
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="31.5px" height="31.5px">
					<path fill-rule="evenodd"  stroke="rgb(186, 1, 51)" stroke-width="1px" stroke-linecap="butt" stroke-linejoin="miter" fill="none"
					 d="M15.500,0.500 C23.784,0.500 30.500,7.216 30.500,15.500 C30.500,23.784 23.784,30.500 15.500,30.500 C7.216,30.500 0.500,23.784 0.500,15.500 C0.500,7.216 7.216,0.500 15.500,0.500 Z"/>
					<path fill-rule="evenodd"  fill="rgb(186, 1, 51)"
					 d="M21.961,23.500 L9.038,23.500 C8.741,23.500 8.500,23.289 8.500,23.028 L8.500,7.971 C8.500,7.711 8.741,7.500 9.038,7.500 L17.895,7.500 C18.037,7.500 18.174,7.549 18.275,7.638 L22.342,11.199 C22.443,11.287 22.500,11.407 22.500,11.532 L22.500,23.028 C22.500,23.289 22.259,23.500 21.961,23.500 ZM18.313,9.005 L18.313,11.166 L20.781,11.166 L18.313,9.005 ZM21.423,12.109 L17.775,12.109 C17.477,12.109 17.236,11.898 17.236,11.637 L17.236,8.442 L9.577,8.442 L9.577,22.557 L21.423,22.557 L21.423,12.109 ZM11.521,9.728 L14.174,9.728 C14.471,9.728 14.713,9.939 14.713,10.200 C14.713,10.460 14.471,10.671 14.174,10.671 L11.521,10.671 C11.224,10.671 10.983,10.460 10.983,10.200 C10.983,9.939 11.224,9.728 11.521,9.728 ZM11.521,13.728 L19.333,13.728 C19.631,13.728 19.872,13.939 19.872,14.200 C19.872,14.460 19.631,14.671 19.333,14.671 L11.521,14.671 C11.224,14.671 10.983,14.460 10.983,14.200 C10.983,13.939 11.224,13.728 11.521,13.728 ZM11.521,15.386 L19.333,15.386 C19.631,15.386 19.872,15.597 19.872,15.858 C19.872,16.118 19.631,16.329 19.333,16.329 L11.521,16.329 C11.224,16.329 10.983,16.118 10.983,15.858 C10.983,15.597 11.224,15.386 11.521,15.386 ZM11.521,18.738 L15.308,18.738 C15.606,18.738 15.847,18.949 15.847,19.209 C15.847,19.470 15.606,19.681 15.308,19.681 L11.521,19.681 C11.224,19.681 10.983,19.470 10.983,19.209 C10.983,18.949 11.224,18.738 11.521,18.738 ZM11.521,20.198 L15.308,20.198 C15.606,20.198 15.847,20.409 15.847,20.669 C15.847,20.929 15.606,21.141 15.308,21.141 L11.521,21.141 C11.224,21.141 10.983,20.929 10.983,20.669 C10.983,20.409 11.224,20.198 11.521,20.198 ZM18.328,18.424 C19.290,18.424 20.073,19.109 20.073,19.951 C20.073,20.793 19.290,21.479 18.328,21.479 C17.366,21.479 16.584,20.793 16.584,19.951 C16.584,19.109 17.366,18.424 18.328,18.424 ZM18.328,20.536 C18.696,20.536 18.996,20.273 18.996,19.951 C18.996,19.629 18.696,19.367 18.328,19.367 C17.960,19.367 17.661,19.629 17.661,19.951 C17.661,20.273 17.960,20.536 18.328,20.536 Z"/>
					</svg>
				</a>
				@if($requested_playlist > 0)
                    <span class="label label-license">{{ $requested_playlist }}</span>
                @endif
			</div>
			@include('partials.marketplace.right_navbar')
		</div>

	</nav>
</div>

<div class="main-header mobile-2">
	<nav class="navbar navbar-light navbar-toggleable-md menu">
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#containerNavbar" aria-controls="containerNavbar" aria-expanded="false" aria-label="Toggle navigation">
		 	<span class="navbar-toggler-icon"></span>
		</button>
		<div class="logo-FE">
			<a href="{!! URL::to('/marketplace') !!}"><img src="/images/app/logo-marketplace.png"></a>
		</div>

        <div class="request-icon pull-left">
            <a href="{{ route('marketplace.checkout') }}">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="31.5px" height="31.5px">
                <path fill-rule="evenodd"  stroke="rgb(186, 1, 51)" stroke-width="1px" stroke-linecap="butt" stroke-linejoin="miter" fill="none"
                 d="M15.500,0.500 C23.784,0.500 30.500,7.216 30.500,15.500 C30.500,23.784 23.784,30.500 15.500,30.500 C7.216,30.500 0.500,23.784 0.500,15.500 C0.500,7.216 7.216,0.500 15.500,0.500 Z"/>
                <path fill-rule="evenodd"  fill="rgb(186, 1, 51)"
                 d="M21.961,23.500 L9.038,23.500 C8.741,23.500 8.500,23.289 8.500,23.028 L8.500,7.971 C8.500,7.711 8.741,7.500 9.038,7.500 L17.895,7.500 C18.037,7.500 18.174,7.549 18.275,7.638 L22.342,11.199 C22.443,11.287 22.500,11.407 22.500,11.532 L22.500,23.028 C22.500,23.289 22.259,23.500 21.961,23.500 ZM18.313,9.005 L18.313,11.166 L20.781,11.166 L18.313,9.005 ZM21.423,12.109 L17.775,12.109 C17.477,12.109 17.236,11.898 17.236,11.637 L17.236,8.442 L9.577,8.442 L9.577,22.557 L21.423,22.557 L21.423,12.109 ZM11.521,9.728 L14.174,9.728 C14.471,9.728 14.713,9.939 14.713,10.200 C14.713,10.460 14.471,10.671 14.174,10.671 L11.521,10.671 C11.224,10.671 10.983,10.460 10.983,10.200 C10.983,9.939 11.224,9.728 11.521,9.728 ZM11.521,13.728 L19.333,13.728 C19.631,13.728 19.872,13.939 19.872,14.200 C19.872,14.460 19.631,14.671 19.333,14.671 L11.521,14.671 C11.224,14.671 10.983,14.460 10.983,14.200 C10.983,13.939 11.224,13.728 11.521,13.728 ZM11.521,15.386 L19.333,15.386 C19.631,15.386 19.872,15.597 19.872,15.858 C19.872,16.118 19.631,16.329 19.333,16.329 L11.521,16.329 C11.224,16.329 10.983,16.118 10.983,15.858 C10.983,15.597 11.224,15.386 11.521,15.386 ZM11.521,18.738 L15.308,18.738 C15.606,18.738 15.847,18.949 15.847,19.209 C15.847,19.470 15.606,19.681 15.308,19.681 L11.521,19.681 C11.224,19.681 10.983,19.470 10.983,19.209 C10.983,18.949 11.224,18.738 11.521,18.738 ZM11.521,20.198 L15.308,20.198 C15.606,20.198 15.847,20.409 15.847,20.669 C15.847,20.929 15.606,21.141 15.308,21.141 L11.521,21.141 C11.224,21.141 10.983,20.929 10.983,20.669 C10.983,20.409 11.224,20.198 11.521,20.198 ZM18.328,18.424 C19.290,18.424 20.073,19.109 20.073,19.951 C20.073,20.793 19.290,21.479 18.328,21.479 C17.366,21.479 16.584,20.793 16.584,19.951 C16.584,19.109 17.366,18.424 18.328,18.424 ZM18.328,20.536 C18.696,20.536 18.996,20.273 18.996,19.951 C18.996,19.629 18.696,19.367 18.328,19.367 C17.960,19.367 17.661,19.629 17.661,19.951 C17.661,20.273 17.960,20.536 18.328,20.536 Z"/>
                </svg>
            </a>
			@if($requested_playlist > 0)
				<span class="label label-license">{{ $requested_playlist }}</span>
			@endif
        </div>

		<div class="collapse navbar-collapse" id="containerNavbar">
			<ul class="navbar-nav mr-auto nav-main">
				@include('partials.marketplace.mobile_navbar')

				<li>
					<div class="search-playlists">
						<form id="search-video" action="/marketplace/search" method="get">
							<div class="input-group">
								<input type="search" placeholder="Search" name="keywords" class="input-sm form-control"> <span class="input-group-btn">
								<button type="submit" class="btn btn btn-search"><i class="fa fa-search" aria-hidden="true"></i></button></span>
							</div>
						</form>
					</div>
				</li>
			</ul>
		</div>
	</nav>
</div>
