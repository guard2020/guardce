@extends('layouts.app')
@section("page-header")
    @include('layouts.page-header', [
       'pageHeadTitle' => 'GUARD Dashboard',
       'breadcrumbs' => []
   ])

@endsection
@section("content")
    <div class="row mb-3">
        <div class="col-12 ">
            <div class="card card-body p-0">
                <div class="row">
                    <div class="col-12">
                        <div class="row text-center mx-0">
                            <a href="{!! route('service-topology.index') !!} " class="col click-col p-2  ">
                                <p><i class="fas fa-code-branch fa-2x d-inline-block text-violet"></i></p>
                                <h5 class="font-weight-semibold mb-0 text-violet">Service Topology</h5>
                                <span class="badge badge-violet align-top ">{!! str_pad($services, 2, '0', STR_PAD_LEFT) !!}</span>
                            </a>

                            <a href="{!! route('anomalies.index') !!} " class="col click-col p-2  ">
                                <p><i class="fas fa-desktop fa-2x d-inline-block text-warning"></i></p>
                                <h5 class="font-weight-semibold mb-0 text-warning">Anomalies Analysis</h5>
                            </a>
                            <a class="col click-col p-2  " href="{!! route('notifications.index') !!}">
                                <p><i class="fa fa-envelope fa-2x d-inline-block text-primary"></i></p>
                                <h5 class="font-weight-semibold mb-0 text-primary">Threat Notifications</h5>
                                <span class="badge badge-primary align-top ">{!! str_pad($notifications, 2, '0', STR_PAD_LEFT) !!}</span>
                            </a>
                            <a href="{!! route('security-pipeline.index') !!}" class="col click-col p-2  ">
                                <p class=""><i class="fas fa-stream fa-2x d-inline-block text-indigo hoverable"></i></p>
                                <h5 class="font-weight-semibold mb-0 text-indigo">Security Pipelines</h5>
                                <span class="badge badge-indigo align-top ">{!! str_pad($pipelines, 2, '0', STR_PAD_LEFT) !!}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="font-weight-bold text-white header fill pt-3">
        <div class="container p-4 text-center bg-guard-dark-50">
            <h1 class="fw-bolder mb-3">Welcome to the GUARD Security Dashboard</h1>
            <p class="lead mt-5 mb-3">Welcome <span class="font-weight-bolder">{{ auth()->user()->name }}</span>,
                the dashboard is responsible for managing and monitoring several components of the GUARD system. Use the menu on the left or the icons above to navigate to the desired dashboard page.</p>
            <p class="lead mb-3"> If you are interested in looking up more information about the <strong>GUARD project</strong> you can access the project website using the button below.</p>
            <a class="btn btn-lg btn-light slide-left mt-4 px-5" href="https://guard-project.eu/" target="_blank">Check out the project!</a>
        </div>
    </div>
@endsection
@section("stylesheets")
    <style>
        .header{
            height: 150px !important;
            background-image: url({!! asset('images/guard/icons/guard_project_lock_1610.jpg') !!});
        }

        .border{
            border-color: #09093F !important;
        }

        .dashboard-icons{
            height: 75px;
            width: 75px;
        }

        /*.click-col:hover{*/
        /*    background-color: #09093F;*/
        /*    transform: translate3D(5,0,0);*/
        /*    transition: all 1s;*/
        /*}*/

        /*.click-col:hover i{*/
        /*    color: white !important;*/
        /*}*/

        /*.click-col:hover h5{*/
        /*    color: white !important;*/
        /*}*/

        /*.click-col:hover span{*/
        /*    color: #000000 !important;*/
        /*    background-color: white !important;*/
        /*}*/

        .click-col:hover{
            background-color: rgba(52, 58, 64, 0.05);
        }

        .bg-guard-dark-50{
            background-color: rgba(9, 9, 63, 0.6);
        }

        .fill {
            min-height: 62vh;
            height: 100%;
        }

        .min-height-400 {
            min-height: 400px;
            font-size: 5em;
        }

        .slide-left:hover{
            background-color: #12439B;
            transform: translate3D(0,0,0);
            transition: all .5s;
        }

        .text-numbers{
            font-size: medium;
        }

        .badge-indigo {
            color: #fff;
            background-color: #5c6bc0;
        }

        .badge-violet {
            color: #fff;
            background-color: #9C27B0;
        }
    </style>
@endsection