
<!-- Main navbar -->
<div class="navbar navbar-expand-md navbar-light navbar-static">

    <!-- Header with logos -->
    <div class="navbar-header navbar-guard d-none d-md-flex align-content-center">
        <div class="navbar-brand navbar-brand-md">
            <a href="" class="d-inline">
                <img class="guard-img-margin" style="height: 1.25rem;" src="{!! asset('images/guard/icons/main_icon_transparent.png') !!}" alt="Guard Image">
            </a>
        </div>

        <div class="navbar-brand navbar-brand-xs">
            <a href="" class="d-inline-block">
                <img src="{!! asset('images/guard/icons/main_icon_mini_transparent.png') !!}" alt="Guard Image">
            </a>
        </div>
    </div>
    <!-- /header with logos -->


    <!-- Mobile controls -->
    <div class="d-flex flex-1 d-md-none">
        <div class="navbar-brand mr-auto">
            <a href="../full/index.html" class="d-inline-block">
                <img src="" alt="Guard Image">
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
            <i class="icon-tree5"></i>
        </button>

        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-arrow-left12"></i>
        </button>
    </div>
    <!-- /mobile controls -->


    <!-- Navbar content -->
    <div class="collapse navbar-collapse" id="navbar-mobile">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block p-0">
                    <i class="mainbar-collape-left icon-arrow-left12 " ></i>
                    <i class="mainbar-collape-right icon-arrow-right13 "  ></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto dropdown dropdown-notifications">
{{--            TODO-Add alerts or no?--}}
{{--            <li class="nav-item">--}}
{{--                <a href="#" class="navbar-nav-link navbar-nav-link-toggler" data-toggle="dropdown">--}}
{{--                    <i class="icon-exclamation icon-1-5x"></i>--}}
{{--                    <span class="badge badge-warning badge-pill ml-auto ml-lg-0 mt-1">4</span>--}}
{{--                </a>--}}
{{--            </li>--}}


            <li class="nav-item dropdown dropdown-user">
                <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
                    <img src="{!! asset('images/user_icon.png') !!}" class="rounded-circle mr-2" height="34" width="34" alt="User Image">
                    <span>{!! isset(auth()->user()->name) ? auth()->user()->name : '' !!}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ url('users/profile') }}" class="dropdown-item"><i class="icon-user-plus"></i> User profile</a>
                    <a href="{{ url('users/setting') }}" class="dropdown-item"><i class="icon-cog5"></i> Account settings</a>
                    @if(!empty(Auth::user()) && Auth::user()->role_id === 1)
                        <a href="{{ url('users/list') }}" class="dropdown-item"><i class="icon-users"></i> Users List</a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a href="{!! route('logout') !!}" class="dropdown-item"><i class="icon-switch2"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
    <!-- /navbar content -->

</div>
<!-- /main navbar -->
<style>
    .guard-img-margin{
        margin-left: 4.5rem !important;
    }

    body:not([class=sidebar-xs]) .mainbar-collape-right {
        display: none;
    }

    body[class=sidebar-xs] .mainbar-collape-left {
        display: none;
    }

    body[class=sidebar-xs] .mainbar-collape-right {
        display: inline;
    }

    .icon-1-5x{
        font-size: 1.5rem;
    }

</style>