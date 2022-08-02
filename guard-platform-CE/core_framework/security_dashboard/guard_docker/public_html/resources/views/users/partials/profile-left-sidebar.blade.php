<div class="card-body text-center">
    <div class="card-img-actions d-inline-block mb-3">
        <img class="img-fluid rounded-circle" src="/images/user_icon.png" width="170" height="170" alt="">
    </div>

    <h6 class="font-weight-semibold mb-0">{{ $user->name }}</h6>
    <span class="d-block opacity-75">{{ ($user->role_id === 1) ? "Administrator" : "User" }}</span>
</div>

<ul class="nav nav-sidebar">
    <li class="nav-item">
        <a href="#profile" id="profile" class="nav-link active" data-toggle="tab">
            <i class="icon-user"></i>
            My profile
        </a>
    </li>
    <li class="nav-item">
        <a href="#setting" id="setting" class="nav-link" data-toggle="tab">
            <i class="icon-cog5"></i>
            Account Settings
        </a>
    </li>
    @if(isset($permission))
        <li class="nav-item">
            <a href="#users" id="users" class="nav-link" data-toggle="tab">
                <i class="icon-users"></i>
                Users List
            </a>
        </li>
    @endif

    <li class="nav-item-divider"></li>
    <li class="nav-item">
        <a href="{{ url('logout') }}" class="nav-link">
            <i class="icon-switch2"></i>
            Logout
        </a>
    </li>
</ul>
