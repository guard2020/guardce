@extends('layouts.app')

@section("stylesheets")
  <style>
    #data-tracking {
      position: relative;
      width: 100%;
      height: 100%;
    }

    #data-tracking > iframe {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      width: 100%;
      height: 100%
    }

    #data-tracking > .notice {
      background-color: #fff;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      padding: 16px;
    }

  </style>

@endsection

@section("content")
  <div id="data-tracking">
    @if (env('DATA_TRACKING_URL'))
        <iframe src="{{ env('DATA_TRACKING_URL') }}" frameborder="0"></iframe>
    @else
        <div class="notice">
            <h1>Data Tracking API is not configured</h1>
            <p>Please configure the <code>DATA_TRACKING_URL</code> environment variable.</p>
        </div class="notice">
    @endif
  </div>
@endsection