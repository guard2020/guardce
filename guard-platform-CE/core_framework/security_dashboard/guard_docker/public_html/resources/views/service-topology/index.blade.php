@extends('layouts.app')

@php
    $extraBtns = (isset($userPermission) && $userPermission === true ) ?
                        '<button type="button" class="btn btn-guard mr-2" id="chainBtn" data-toggle="modal" data-target="#modal_chain">Discover New Service Chain </button>'.
                        '<a type="button" class="btn btn-info" id="chainBtn" href="'.route('service-topology.chain.index').'">Manage Service Chains</a>' :
                        null;

@endphp


@section('page-header')
    @include('layouts.page-header', [
        'pageHeadTitle' => 'Service Topology',
        'extraBtns' => $extraBtns,
        'breadcrumbs' => [
            [
                'name' => 'Topology',
                'link' => '',
                'icon' => 'fas fa-code-branch'
            ]
        ]
    ])
@endsection
@section('content')
    @include('flash-message')
    <div class="row row-topology topology-card">
        <div class="card col-xl-8 col-topology px-0">
            <div class="card-header bg-transparent header-elements-inline">
                <h5 class="card-title">Topology</h5>
            </div>
            <div class="card-body bg-white topology-card">
                @if($error)
                    <div class="w-100 alert alert-warning " role="alert">
                        <h6 class="alert-heading">Data error!</h6>
                        <p>
                            The current data in the CB Manager is not enough. It might be that the Context Broker has not been setup yet. The Service Topology requires that
                            the Execution Environment index has data.
                        </p>
                    </div>
                @else
                    <div id="chart" class="w-100 topology-card"></div>
                @endif
            </div>
        </div>
        <div class="col-xl-4 topology-info">
            <div class="card m-0">
                <div class="card-header bg-transparent header-elements-inline">
                    <h5 class="card-title">Service Details <i class="fas fa-link text-info ml-2 d-none" id="iconChain" data-toggle="tooltip" title=""></i></h5>
                    <div class="header-elements">
                        <div class="list-icons">
                            <form action="" method="POST" class="delete-method" >
                                @method('delete')
                                @csrf
                                <button class="btn btn-danger btn-sm d-none" type="submit" id="deleteChain" data-toggle="tooltip" title="">
                                    <i class="fa fa-trash" aria-hidden="true" ></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body mb-4 ">
                    <ul class="service-info list-group p-2">
                        <span class="alert alert-info">
                            Select a service to get more information.
                        </span>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @include('service-topology.partials.chain-modal')

@endsection
@section('scripts')
    @include('service-topology.js.cytoscape')
    @include('service-topology.js.cytoscape-functions')
    @include('service-topology.js.select-field')
    <script src="{!! asset('limitless/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js') !!}"></script>
    <script type="text/javascript">
        const env = JSON.parse('{!! json_encode($environments) !!}');
        const con = JSON.parse('{!! json_encode($connections) !!}');
        const net = JSON.parse('{!! json_encode($networks) !!}');
        const rootEnvs = JSON.parse('{!! json_encode($rootEnvs) !!}');

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        })


    </script>
@endsection

@section('stylesheets')
    <style>
        .topology-card{
            width:  100%;
            height: 100%;
            margin: 0;
        }

        .bg-specific{
            background-color: #eeeded;
        }

        li.heading {
            background: #ededed;
        }

        .form-control{
            display: block;
            width: 100%;
            height: calc(1.5385em + .875rem + 2px);
            padding: .4375rem .875rem;
            font-size: .8125rem;
            font-weight: 400;
            line-height: 1.5385;
            color: #333333;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ddd !important;
            border-radius: .1875rem;
            box-shadow: 0 0 0 0 transparent;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }

        .form-control:focus{
            border-top-color: #007065 !important;
            border-top: 1px;
        }
    </style>
@endsection