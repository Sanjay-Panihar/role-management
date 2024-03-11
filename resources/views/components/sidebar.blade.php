<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="index.html" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary"></i>Admin Panel</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;" />
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0">{{ Auth::user()->name ?? 'John Doe' }}</h6>
                <span> {{ Auth::user()->roles->first()->name ?? 'No Role' }}</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <a href="{{ route('dashboard') }}" class="nav-item nav-link {{ Request::segment(1) === 'dashboard' ? 'active' : '' }} "><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
            <!-- <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-laptop me-2"></i>Elements</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="button.html" class="dropdown-item">Buttons</a>
                    <a href="typography.html" class="dropdown-item">Typography</a>
                    <a href="element.html" class="dropdown-item">Other Elements</a>
                </div>
            </div> -->
            @can('view product')<a href="{{ route('product.index') }}" class="nav-item nav-link {{ Request::segment(1) === 'product' ? 'active' : '' }}"><i class="fa fa-th me-2"></i>Product</a>@endcan
            @can('view users')<a href="{{ route('user.index') }}" class="nav-item nav-link {{ Request::segment(1) === 'user' ? 'active' : '' }}"><i class="fa fa-keyboard me-2"></i>Users</a>@endcan
            @can('view permissions')<a href="{{route('permission.index') }}" class="nav-item nav-link {{ Request::segment(1) === 'permission' ? 'active' : '' }}"><i class="fa fa-table me-2"></i>Permissions</a>@endcan
            <!-- <a href="chart.html" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Charts</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>Pages</a>
                <div class="dropdown-menu bg-transparent border-0">
                    <a href="signin.html" class="dropdown-item">Sign In</a>
                    <a href="signup.html" class="dropdown-item">Sign Up</a>
                    <a href="404.html" class="dropdown-item">404 Error</a>
                    <a href="blank.html" class="dropdown-item">Blank Page</a>
                </div>
            </div> -->
        </div>
    </nav>
</div>
