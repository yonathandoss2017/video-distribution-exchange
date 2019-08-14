<header class="header">

    <div class="main-menu desktop">
        <div class="top-menu">
            <a href="{{ route('admin.home') }}" class="superadmin__logo">
                <img src="/images/app/logo-dashboard.png" alt="home"/>
                <span >Superadmin</span>
            </a>

            <div class="d-flex align-items-center">
                <div><a href="/marketplace" class="btn btn-normal marketplace" style="margin-top: 0">{{ __('manage/cp/layout.marketplace') }}</a></div>
                <ul class="nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> {{ __('common.logout') }}</a>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- .main-menu -->

</header>
