<!-- Start::main-header-container -->
<div class="main-header-container container-fluid">

    <!-- Start::header-content-left -->
    <div class="header-content-left">

        <!-- Start::header-element -->
        @php
            $admin_logo = Config('constant.SETTINGS_IMAGE_URL') . Config('Site.admin_logo');
        @endphp
        <div class="header-element">
            <div class="horizontal-logo">
                <a href="index.html" class="header-logo">
                    <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo" class="desktop-logo">
                    <!-- <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo" class="toggle-logo">
                    <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo" class="desktop-dark">
                    <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo" class="toggle-dark">
                    <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo" class="desktop-white">
                    <img src="{{ env('WEBSITE_URL').'/public/assets/front/8thniqjewells/assets/img/logo/logo.svg' }}" alt="logo" class="toggle-white"> -->
                </a>
            </div>
        </div>
        <!-- End::header-element -->

    </div>
    <!-- End::header-content-left -->

    <!-- Start::header-content-right -->
    <div class="header-content-right">
        <div class="header-element">
            <!-- Notification Bell Icon with Unread Count -->
            <a href="#" class="header-link dropdown-toggle" id="notificationBell" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="bx bx-bell header-link-icon"></i>
                @if ($unreadNotificationsCount > 0)
                    <span class="badge bg-danger" id="notification-count">{{ $unreadNotificationsCount }}</span>
                @endif
            </a>
            <!-- Notification Dropdown Menu -->
            <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-notification-dropdown dropdown-menu-end"
                aria-labelledby="notificationBell">
                @foreach ($notifications as $notification)
                    <li class="dropdown-item notificationchng">
                        <a href="#" class="dropdown-item notificationchng" data-id="{{ $notification->id }}">
                            <div class="d-flex justify-content-between">
                                <p class="fw-semibold">{{ $notification->title }}</p>
                                @if (!$notification->is_read)
                                    <span class="badge bg-warning">New</span>
                                @endif
                            </div>
                            <p class="mb-0">{{ $notification->message }}</p>
                        </a>
                    </li>
                @endforeach
                @if ($notifications->isEmpty())
                    <li class="dropdown-item notificationchng">No new notifications</li>
                @endif
            </ul>
        </div>
        <!-- Start::header-element -->
        <div class="header-element header-theme-mode">
            <!-- Start::header-link|layout-setting -->
            <a href="javascript:void(0);" class="header-link layout-setting">
                <span class="light-layout">
                    <!-- Start::header-link-icon -->
                    <i class="bx bx-moon header-link-icon"></i>
                    <!-- End::header-link-icon -->
                </span>
                <span class="dark-layout">
                    <!-- Start::header-link-icon -->
                    <i class="bx bx-sun header-link-icon"></i>
                    <!-- End::header-link-icon -->
                </span>
            </a>
            <!-- End::header-link|layout-setting -->
        </div>
        <!-- End::header-element -->

        <!-- Start::header-element -->
        <div class="header-element">
            <!-- Start::header-link|dropdown-toggle -->
            <a href="#" class="header-link dropdown-toggle" id="mainHeaderProfile" data-bs-toggle="dropdown"
                data-bs-auto-close="outside" aria-expanded="false">
                <div class="d-flex align-items-center">
                    <div class="me-sm-2 me-0">
                        <img src="{{ ucfirst(auth()->user()->image) }}" alt="img" width="32" height="32"
                            class="rounded-circle">
                    </div>
                    <div class="d-sm-block d-none">
                        <p class="fw-semibold mb-0 lh-1">{{ ucfirst(auth()->user()->name) }}</p>
                    </div>
                </div>
            </a>
            <!-- End::header-link|dropdown-toggle -->
            <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                aria-labelledby="mainHeaderProfile">
                <li><a class="dropdown-item d-flex" href="{{ route('admin-settings.prefix') }}"><i
                            class="ri-tools-fill side-menu__icon fs-18 me-2 op-7"></i> Settings</a></li>

                <li><a class="dropdown-item d-flex" href="{{ route('admin-settings.changepassword') }}"><i
                            class="ri-lock-fill side-menu__icon fs-18 me-2 op-7"></i> Change Password</a></li>

                <li><a class="dropdown-item d-flex" href="{{ route('admin-logout') }}"><i
                            class="ti ti-logout fs-18 me-2 op-7"></i>Log Out</a></li>
            </ul>
        </div>
        <!-- End::header-element -->

    </div>
    <!-- End::header-content-right -->

</div>
<!-- End::main-header-container -->
