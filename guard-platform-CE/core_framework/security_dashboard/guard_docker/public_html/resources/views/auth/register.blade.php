@extends('layouts.auth-app')
@section('content')
<div class="container">
    <div class="content d-flex justify-content-center align-items-center">
        <!-- Registration form -->
        <form method="post" action="{{ route('register') }}" class="flex-fill" id="smart-dashboard-form">
            @csrf
            <div class="row">
                <div class="col-lg-6 offset-lg-3">

                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="icon-plus3 icon-2x text-success border-success border-3 rounded-pill p-3 mb-3 mt-1"></i>
                                <h5 class="mb-0">Create account</h5>
                                <span class="d-block text-muted">All fields are required</span>
                            </div>
                            <div class="form-group form-group-feedback form-group-feedback-right">
                                <input id="name" type="text" data-parsley-required="true" class="form-control pl-2 @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" placeholder="Name" autocomplete="name"
                                       autofocus>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                                <div class="form-control-feedback pr-2">
                                    <i class="icon-user-check text-muted"></i>
                                </div>
                            </div>

                            <div class="form-group form-group-feedback form-group-feedback-right">
                                <input id="email" type="email" class="form-control pl-2 @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Your email">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <div class="form-control-feedback pr-2">
                                    <i class="icon-mention text-muted"></i>
                                </div>
                            </div>

                            <div class="form-group form-group-feedback form-group-feedback-right">
                                <input id="password" type="password" class="form-control pl-2 @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="new-password" placeholder="Password" data-parsley-minlength="8">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <div class="form-control-feedback pr-2">
                                    <i class="icon-user-lock text-muted"></i>
                                </div>
                            </div>

                            <div class="form-group form-group-feedback form-group-feedback-right">
                                <input id="password-confirm" data-parsley-equalto="#password" data-parsley-equalto-message="Password and confirm password should be the same." type="password" class="form-control pl-2"
                                       name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password">
                                @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <div class="form-control-feedback pr-2">
                                    <i class="icon-user-lock text-muted"></i>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer bg-transparent text-right">
                            <button type="submit" class="btn btn-primary btn-labeled btn-labeled-right"><b><i class="icon-plus3"></i></b> Create account</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /registration form -->
    </div>
</div>
@endsection
@section('scripts')
    @include('auth.partials.js.login-register-scripts')
@endsection

@section("stylesheets")
    @include('auth.partials.css.login-register-style')
@endsection