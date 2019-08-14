<aside class="left-menu--vertical">
    <section class="welcome-user">
        <small class="welcome-text">{{ __('admin/sidebar.welcome') }}</small>
        <h5 class="welcome-username">{{ Auth::user()->name }}</h5>
        <small class="welcome-email">{{ Auth::user()->email }}</small>
    </section>
    <ul class="list-unstyled">
        <li class="{{ (Request::is('admin/organization')||Request::is('admin/organization/*'))?'active':'' }}"><a href="{{ route('admin.organization.index') }}"><i class="sprite-superadmin-icon icon-organizations"></i> {{ __('admin/sidebar.organizations') }}</a></li>
        <li class="{{ (Request::is('admin/cp', 'admin/cp/*')) ? 'active' : '' }}">
            <a href="{{ route('admin.cp.index') }}"><i class="sprite-superadmin-icon icon-cp"></i> {{ __('admin/sidebar.content_providers') }}</a>
        </li>
        <li class="{{ (Request::is('admin/sp', 'admin/sp/*'))  ? 'active' : '' }}">
            <a href="{{ route('admin.sp.index') }}"><i class="sprite-superadmin-icon icon-sp"></i> {{ __('admin/sidebar.service_providers') }}</a>
        </li>
        <li class="{{ (Request::is('admin/entry'))?'active':'' }}"><a href="{{ route('admin.entry.index') }}"><i class="sprite-superadmin-icon icon-entries"></i> {{ __('admin/sidebar.entries') }}</a></li>
        <li class="{{ (Request::is('admin/user')||Request::is('admin/user/*')) ? 'active' : '' }}"><a href="{{ route('admin.user.index') }}"><i class="sprite-superadmin-icon icon-user"></i> {{ __('admin/sidebar.users') }}</a></li>
        <li class="{{ (Request::is('admin/exchange')||Request::is('admin/exchange/*')) ? 'active' : '' }}"><a href="{{ route('admin.exchange.index') }}"><i class="sprite-superadmin-icon icon-entries"></i> {{ __('admin/sidebar.exchange') }}</a></li>
        <li class="{{ (Request::is('dpp/request/new')||Request::is('dpp/request/new')) ? 'active' : '' }}"><a href="{{ route('dpp.request.new.index') }}"><i class="sprite-superadmin-icon icon-dpp"></i> {{ __('admin/header.dpp') }}</a></li>
        @if(config('features.log_view'))
        <li class="{{ (Request::is('admin/logs')||Request::is('admin/logs/*')) ? 'active' : '' }}"><a href="{{ route('admin.logs.node1') }}"><i class="sprite-superadmin-icon icon-cp"></i> {{ __('admin/header.logs') }}</a></li>
        @endif
    </ul>
</aside>
