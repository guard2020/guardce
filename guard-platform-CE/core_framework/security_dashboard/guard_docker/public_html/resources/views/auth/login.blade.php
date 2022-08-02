@extends('layouts.auth-app')
@section("content")
    <div class="row offset-5">
        @php
            if(Session::get('success')){
                $message = Session::get('success');
                $class = 'alert-success';
            } else {
                $message = Session::get('error');
                $class = 'alert-danger';
            }
        @endphp

        @if(isset($message))
            <div class="alert {!! $class !!} alert-block w-message">
                <strong>{!! $message !!}</strong>
                <button type="button" class="close" data-dismiss="alert">x</button>
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="alert alert-danger w-message d-flex justify-content-center align-items-center">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
    @if(env('APP_ENV') === 'local')
        <div class="row offset-5">
            <div class="alert alert-info alert-block w-message">
                <strong>Email: admin@mindsandsparks.org</strong>
                <strong>Password: m&s2021</strong>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="content d-flex justify-content-center align-items-center">
        <!-- Login form -->
            <div class="card mb-0">
                <form class="login-form" action="{!! route('login.check') !!}" method="post" id="smart-dashboard-form">
                    {{csrf_field()}}
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="icon-reading icon-2x text-secondary border-secondary border-3 rounded-pill p-3 mb-3 mt-1"></i>
                            <h5 class="mb-0">Login to your account</h5>
                        </div>

                        <label for="email" class="d-block text-muted">Your email</label>
                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="email" required name="email" class="form-control" placeholder="Email">
                            <div class="form-control-feedback pl-1">
                                <i class="icon-user text-muted"></i>
                            </div>
                        </div>

                        <label for="password" class="d-block text-muted">Password</label>
                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="password" required name="password" class="form-control" placeholder="Password" autocomplete="off">
                            <div class="form-control-feedback pl-1">
                                <i class="icon-lock2 text-muted"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-lower">Login </button>
                        </div>

                        <div class="form-group">
                            <a href="{!! route('register') !!}" class="btn btn-light btn-block btn-lower">Register</a>
                        </div>

                    </div>
                </form>
            </div>
        <!-- Login form -->
        </div>
    </div>
@endsection

@section('scripts')
    @include('auth.partials.js.login-register-scripts')
@endsection

@section("stylesheets")
    @include('auth.partials.css.login-register-style')
@endsection