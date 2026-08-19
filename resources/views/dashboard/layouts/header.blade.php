<div id="header" class="mdk-header js-mdk-header m-0" data-fixed>
    <div class="mdk-header__content">

        <div class="navbar navbar-expand-sm navbar-main navbar-light pr-0" id="navbar" data-primary>
            <div class="container-fluid p-0">

                <button class="navbar-toggler navbar-toggler-right d-block d-lg-none" type="button" data-toggle="sidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <button class="navbar-toggler navbar-toggler-right d-none d-lg-block" type="button" data-toggle="sidebar">
                    <i class="material-icons">menu</i>
                </button>

                <a href="{{ route('dashboard.index') }}" class="navbar-brand d-flex align-items-center">
                    <img
                        src="{{ asset('dashboard/images/logos/main-logo.png') }}"
                        alt="{{ __('Atariqi') }}"
                        class="navbar-brand-icon mr-2"
                        style="height: 36px; width: auto;"
                    >
                    <span>{{ __('Atariqi') }}</span>
                </a>

                <ul class="nav navbar-nav ml-auto border-left navbar-height align-items-center">
                    <li class="nav-item dropdown">
                        <a href="#account_menu" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown" data-caret="false">
                            <i class="material-icons mr-1">account_circle</i>
                            <span class="text-dark">{{ auth()->guard('admin')->user()->name }}</span>
                        </a>
                        <div id="account_menu" class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-item-text dropdown-item-text--lh">
                                <div>
                                    <strong>{{ auth()->guard('admin')->user()->name }}</strong>
                                </div>
                                <div class="text-muted">{{ auth()->guard('admin')->user()->email }}</div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('dashboard.index') }}"><i class="material-icons">dvr</i> {{ __('Dashboard') }}</a>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fa fa-edit"></i> {{ __('Edit Profile') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('dashboard.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-arrow-alt-circle-left"></i> {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('dashboard.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>

            </div>
        </div>

    </div>
</div>
