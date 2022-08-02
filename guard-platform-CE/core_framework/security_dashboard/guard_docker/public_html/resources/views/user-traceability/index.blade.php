@extends('layouts.app')

@section("page-header")
    @include('layouts.page-header', [
        'pageHeadTitle' => 'User Data Traceability'
    ])
@endsection


@section("content")
    <div class="d-flex justify-content-center min-height-400">
        <div class="align-self-center">

        </div>
    </div>
@endsection
@section("stylesheets")
    <style>
        .min-height-400 {
            min-height: 400px;
            font-size: 5em;
        }
    </style>
@endsection