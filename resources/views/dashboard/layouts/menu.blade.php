<div class="mdk-drawer  js-mdk-drawer" id="default-drawer" data-align="start">
    <div class="mdk-drawer__content">
        <div class="sidebar sidebar-dark sidebar-left sidebar-p-t bg-dark" data-perfect-scrollbar>
            <div class="sidebar-heading">{{ __('Menu') }}</div>
            <ul class="sidebar-menu">
                @adminRoute('dashboard.index')
                <li class="sidebar-menu-item open">
                    <a class="sidebar-menu-button" href="{{ route('dashboard.index') }}">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left material-icons">dvr</i>
                        <span class="sidebar-menu-text"> {{ __('Dashboard') }} </span>
                    </a>
                </li>
                @endadminRoute

                @adminAnyRoute('homepage-sections.index', 'homepage-stats.index', 'testimonials.index', 'partner-achievements.index')
                <!-- Homepage Sections -->
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#homepage_sections">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-globe"></i>
                        <span class="sidebar-menu-text"> {{ __('Homepage Sections') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="homepage_sections">
                        @adminRoute('homepage-sections.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('homepage-sections.edit', ['homepage_section' => 'about_us']) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('About Us') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('homepage-sections.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('homepage-sections.edit', ['homepage_section' => 'about_app']) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('About App') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('homepage-sections.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('homepage-sections.edit', ['homepage_section' => 'sliders']) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Sliders') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('homepage-stats.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('homepage-stats.index') }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Our Numbers') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('partner-achievements.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('partner-achievements.index', ['type' => 'partners']) }}">
                                <i class="fa fa-users"></i>
                                <span class="sidebar-menu-text"> {{ __('Our Partners') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('testimonials.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('testimonials.index') }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Testimonials') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('partner-achievements.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('partner-achievements.index', ['type' => 'achievements']) }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Our Achievements') }}</span>
                            </a>
                        </li>
                        @endadminRoute
                    </ul>
                </li>
                @endadminAnyRoute

                @adminAnyRoute('drivers.index', 'new-drivers.index', 'edit-info-request.index', 'drivers.packages', 'packages.index', 'features.index', 'drivers.rates', 'drivers.trips', 'general-dues-percentage.show')
                <!-- Drivers -->
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#drivers">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-globe"></i>
                        <span class="sidebar-menu-text"> {{ __('Drivers') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="drivers">
                        @adminRoute('drivers.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('drivers.index') }}">
                                <i class="fa fa-user"></i>
                                <span class="sidebar-menu-text"> {{ __('Drivers') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('new-drivers.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('new-drivers.index') }}">
                                <i class="fa fa-user"></i>
                                <span class="sidebar-menu-text"> {{ __('New Drivers') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('edit-info-request.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('edit-info-request.index') }}">
                                <i class="fa fa-info"></i>
                                <span class="sidebar-menu-text"> {{ __('Edit Info Request') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('drivers.packages')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('drivers.packages') }}">
                                <i class="fa fa-gift"></i>
                                <span class="sidebar-menu-text"> {{ __('Driver Package Management') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('packages.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('packages.index') }}">
                                <i class="fa fa-box"></i>
                                <span class="sidebar-menu-text"> {{ __('Packages') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('features.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('features.index') }}">
                                <i class="fa fa-box"></i>
                                <span class="sidebar-menu-text"> {{ __('Features') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('drivers.rates')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('drivers.rates') }}">
                                <i class="fa fa-star"></i>
                                <span class="sidebar-menu-text"> {{ __('Driver Passenger Rates') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('drivers.trips')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('drivers.trips') }}">
                                <i class="fa fa-route"></i>
                                <span class="sidebar-menu-text"> {{ __('Driver Trips') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('general-dues-percentage.show')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('general-dues-percentage.show') }}">
                                <i class="fa fa-percent"></i>
                                <span class="sidebar-menu-text"> {{ __('General Dues Percentage') }}</span>
                            </a>
                        </li>
                        @endadminRoute
                    </ul>
                </li>
                @endadminAnyRoute

                @adminAnyRoute('passengers.index', 'passengers.all-trips', 'passengers.profile-update-requests', 'users.unride-rates')
                <!-- Users -->
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#users">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-users"></i>
                        <span class="sidebar-menu-text"> {{ __('Users') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="users">
                        @adminRoute('passengers.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('passengers.index') }}">
                                <i class="fa fa-users"></i>
                                <span class="sidebar-menu-text"> {{ __('Passengers') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('passengers.all-trips')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('passengers.all-trips') }}">
                                <i class="fa fa-route"></i>
                                <span class="sidebar-menu-text"> {{ __('All Passenger Trips') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('passengers.profile-update-requests')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('passengers.profile-update-requests') }}">
                                <i class="fa fa-user-edit"></i>
                                <span class="sidebar-menu-text"> {{ __('Profile Update Requests') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('users.unride-rates')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('users.unride-rates') }}">
                                <i class="fa fa-star-half-alt"></i>
                                <span class="sidebar-menu-text"> {{ __('Unride Rates') }}</span>
                            </a>
                        </li>
                        @endadminRoute
                    </ul>
                </li>
                @endadminAnyRoute

                @adminRoute('support-tickets.index')
                <!-- Technical Support -->
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#support_tickets">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-headset"></i>
                        <span class="sidebar-menu-text"> {{ __('Technical Support') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="support_tickets">
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('support-tickets.index', 'complaints') }}">
                                <i class="fa fa-exclamation-circle"></i>
                                <span class="sidebar-menu-text"> {{ __('Complaints') }}</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('support-tickets.index', 'inquiries') }}">
                                <i class="fa fa-question-circle"></i>
                                <span class="sidebar-menu-text"> {{ __('Inquiries') }}</span>
                            </a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('support-tickets.index', 'technical') }}">
                                <i class="fa fa-wrench"></i>
                                <span class="sidebar-menu-text"> {{ __('Technical Issues') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endadminRoute

                @adminAnyRoute('announcements.index', 'universities.index', 'cities.index', 'delivery-services.index', 'documents.index')
                <!-- Platform Management -->
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#platform_management">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-cogs"></i>
                        <span class="sidebar-menu-text"> {{ __('Platform Management') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="platform_management">
                        @adminRoute('announcements.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('announcements.index') }}">
                                <i class="fa fa-bullhorn"></i>
                                <span class="sidebar-menu-text"> {{ __('Announcements') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('universities.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('universities.index') }}">
                                <i class="fa fa-university"></i>
                                <span class="sidebar-menu-text"> {{ __('Universities') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('cities.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('cities.index') }}">
                                <i class="fa fa-map-marker"></i>
                                <span class="sidebar-menu-text"> {{ __('Cities & Neighborhoods') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('delivery-services.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('delivery-services.index') }}">
                                <i class="fa fa-truck"></i>
                                <span class="sidebar-menu-text"> {{ __('Delivery Services') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('documents.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('documents.index') }}">
                                <i class="fa fa-file-pdf"></i>
                                <span class="sidebar-menu-text"> {{ __('Documents Management') }}</span>
                            </a>
                        </li>
                        @endadminRoute
                    </ul>
                </li>
                @endadminAnyRoute

                @adminAnyRoute('employees.index', 'roles.index', 'logs.index')
                <!-- Admin Management -->
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" data-toggle="collapse" href="#admin_management">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-users-cog"></i>
                        <span class="sidebar-menu-text"> {{ __('Admin Management') }} </span>
                        <span class="ml-auto sidebar-menu-toggle-icon"></span>
                    </a>
                    <ul class="sidebar-submenu collapse" id="admin_management">
                        @adminRoute('employees.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('employees.index') }}">
                                <i class="fa fa-users"></i>
                                <span class="sidebar-menu-text"> {{ __('Employee Management') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('roles.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('roles.index') }}">
                                <i class="fa fa-user-shield"></i>
                                <span class="sidebar-menu-text"> {{ __('Roles Management') }}</span>
                            </a>
                        </li>
                        @endadminRoute

                        @adminRoute('logs.index')
                        <li class="sidebar-menu-item">
                            <a class="sidebar-menu-button" href="{{ route('logs.index') }}">
                                <i class="fa fa-history"></i>
                                <span class="sidebar-menu-text"> {{ __('Logs Management') }}</span>
                            </a>
                        </li>
                        @endadminRoute
                    </ul>
                </li>
                @endadminAnyRoute

                @adminRoute('settings.index')
                {{-- Settings --}}
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" href="{{ route('settings.index') }}">
                        <i class="sidebar-menu-icon sidebar-menu-icon--left fa fa-cog"></i>
                        <span class="sidebar-menu-text"> {{ __('Settings') }} </span>
                    </a>
                </li>
                @endadminRoute

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
