<div class="mdk-drawer  js-mdk-drawer" id="default-drawer" data-align="start">
    <div class="mdk-drawer__content">
        <div class="sidebar sidebar-dark sidebar-left sidebar-p-t bg-dark" data-perfect-scrollbar>
            <div class="sidebar-heading">{{ __('Menu') }}</div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item open">
                    <a class="sidebar-menu-button" href="{{ route('dashboard.index', ['type' => 'ticket']) }}">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">dvr</i>
                        <span class="sidebar-menu-text"> {{ __('Dashboard') }} </span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#homepage_sections">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-globe"></i>
                        <span class="sidebar-menu-text"> {{ __('Homepage Sections') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="homepage_sections">
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('homepage-sections.edit', ['homepage_section' => 'about_us']) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('About Us') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('homepage-sections.edit', ['homepage_section' => 'about_app']) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('About App') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('homepage-stats.index', ) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Our Numbers') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('partner-achievements.index', ['type' => 'partners']) }}">
                                <i class="fa fa-users"></i>
                                <span class="sidebar-menu-text"> {{ __('Our Partners') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('testimonials.index') }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Testimonials') }}</span>
                            </a>
                        </li>

                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('partner-achievements.index', ['type' => 'achivements']) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Partner Achievements') }}</span>
                            </a>
                        </li>

                    </ul>
                </li>

                {{-- Settings --}}
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" href="{{ route('settings.index') }}">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-cog"></i>
                        <span class="sidebar-menu-text"> {{ __('Settings') }} </span>
                    </a>
                </li>

                {{-- Languages --}}
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#dashboard_language">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-globe"></i>
                        <span class="sidebar-menu-text"> {{ __('Language') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="dashboard_language">
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('language', 'ar') }}">
                                <i class="fa fa-flag"></i>
                                <span class="sidebar-menu-text"> {{ __('ar') }}</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('language', 'en') }}">
                                <i class="fa fa-flag"></i>
                                <span class="sidebar-menu-text"> {{ __('English') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>


            </ul>
        </div>
    </div>
</div>
