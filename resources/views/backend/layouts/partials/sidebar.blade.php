<!-- sidebar menu area start -->
<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('admin/assets/images/icon/logo.png') }}" alt="logo"></a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}"><i class="ti-dashboard"></i><span>Dashboard</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.universities.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.universities.index') }}"><i class="ti-home"></i><span>Universities</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.programs.index') }}"><i class="ti-book"></i><span>Programs</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}"><i class="ti-user"></i><span>Users</span></a>
                    </li>
                    @if (auth()->user()->is_admin || auth()->user()->hasRole('Super Admin'))
                        <li class="{{ request()->routeIs('admin.roles.*', 'admin.permissions.*') ? 'active' : '' }}">
                            <a href="javascript:void(0)"><i class="ti-settings"></i><span>Roles &amp; Permissions</span><i class="ti-angle-down"></i></a>
                            <ul class="collapse">
                                <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.roles.index') }}">Roles</a>
                                </li>
                                <li class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.permissions.index') }}">Permissions</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    <li class="{{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.applications.index') }}"><i class="ti-files"></i><span>Applications</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.documents.review.index') }}"><i class="ti-folder"></i><span>Document Review</span></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
