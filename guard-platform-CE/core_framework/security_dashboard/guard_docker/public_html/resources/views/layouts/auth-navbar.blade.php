<div class="navbar navbar-expand-lg navbar-light navbar-static navbar-guard">
    <!-- Header with logos -->
    <div class="navbar-header d-none d-md-flex align-content-center">
        <div class="navbar-brand navbar-brand-md">
            <a href="" class="d-inline">
                <img class="guard-img-margin" style="height: 1.25rem;" src="{!! asset('images/guard/icons/main_icon_transparent.png') !!}" alt="">
            </a>
        </div>

        <div class="navbar-brand navbar-brand-xs">
            <a href="" class="d-inline-block">
                <img src="{!! asset('images/guard/icons/main_icon_mini_transparent.png') !!}" alt="">
            </a>
        </div>
    </div>
    <!-- /header with logos -->

    <div class="d-flex justify-content-end align-items-center ml-auto">
        <ul class="navbar-nav flex-row">
            <li class="nav-item">
                <a href="{!! route('register') !!}" class="navbar-nav-link">
                    <i class="icon-user-plus"></i>
                    <span class="d-none d-lg-inline-block ml-2">Register</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{!! route('logout') !!}" class="navbar-nav-link">
                    <i class="icon-user-lock"></i>
                    <span class="d-none d-lg-inline-block ml-2">Login</span>
                </a>
            </li>
        </ul>
    </div>
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