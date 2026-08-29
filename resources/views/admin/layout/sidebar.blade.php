<!-- Start::main-sidebar-header -->
@php
    // $admin_logo = Config('constant.SETTINGS_IMAGE_URL') . Config('Site.admin_logo');
    $logo = \App\Models\Setting::where('key','Site.admin_logo')->first();
 @endphp
<div class="main-sidebar-header">
    <a href="{{ route('admin-admin.dashboard') }}" class="header-logo">
        <img src="{{ env('WEBSITE_URL').'uploads/settings/'.@$logo->value }}" alt="logo" class="desktop-dark">
    </a>
</div>
<!-- End::main-sidebar-header -->

<!-- Start::main-sidebar -->
<div class="main-sidebar" id="sidebar-scroll">
    <?php
    $segment2 = Request()->segment(1);
    $segment3 = Request()->segment(2);
    $segment4 = Request()->segment(3);
    $segment5 = Request()->segment(4);
    ?>
    <!-- Start::nav -->
    <nav class="main-menu-container nav nav-pills flex-column sub-open">
        <div class="slide-left" id="slide-left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
            </svg>
        </div>
        @if (Auth::user()->user_role_id == 4 && 0)
            <ul class="main-menu">
                <li class="slide">
                    <a href="{{ route('admin-partners.show', base64_encode(Auth::user()->id)) }}"
                        class="side-menu__item ">
                        <i class="bx bx-user side-menu__icon"></i><span class="menu-text">View Profile</span><i
                            class=""></i>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('admin-referral_histories.index') }}" class="side-menu__item ">
                        <i class="bx bx-user side-menu__icon"></i><span class="menu-text">referral-histories</span><i
                            class=""></i>
                    </a>
                </li>
        @else
            <?php
            $menus = getMenusFromAcl(); //Session()->get('acls');
            
            echo sideBarNavigationNew($menus);
            ?>
        @endif


        <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                height="24" viewBox="0 0 24 24">
                <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
            </svg></div>
    </nav>
    <!-- End::nav -->

</div>
<!-- End::main-sidebar -->
