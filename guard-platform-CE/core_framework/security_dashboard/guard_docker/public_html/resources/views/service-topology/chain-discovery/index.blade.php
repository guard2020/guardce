@extends('layouts.app')

@php
    $extraBtns = (isset($userPermission) && $userPermission === true ) ?
                    '<button type="button" class="btn btn-guard" id="chainBtn" data-toggle="modal" data-target="#modal_chain">Discover New Service Chain </button>' :
                    null;
@endphp

@section('page-header')
    @include('layouts.page-header', [
        'pageHeadTitle' => 'Service Chains',
        'extraBtns' => $extraBtns,
        'breadcrumbs' => [
            [
                'name' => 'Topology',
                'link' => route('service-topology.index'),
                'icon' => 'fas fa-code-branch'
            ],
            [
                'name' => 'Chains',
                'link' => ''
            ]
        ]
    ])
@endsection
@section('content')
    <div class="row ">
        <div class="col-12 ">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @include('flash-message')
                        <div class="card-header bg-transparent header-elements-inline">
                            <h5 class="card-title">Chains</h5>
                            <div class="header-elements">
                                <div class="list-icons">
                                    <a class="list-icons-item" data-action="collapse"></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col">
                                    <table class="table table-sm table-bordered" id="chainTable" style="border-top: 1px solid darkgray;">
                                        <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th class="text-center">Hostname</th>
                                            <th class="text-center">Port</th>
                                            <th class="text-center">Https</th>
                                            <th class="text-center">Delete Chain</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('service-topology.partials.chain-modal')
@endsection
@section('scripts')
    <!-- Table -->
    <script src="{!! asset('limitless/js/plugins/tables/datatables/datatables.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/tables/datatables/extensions/responsive.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/tables/datatables/extensions/buttons.min.js') !!}"></script>
    <script type="text/javascript">
        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let table = $('#chainTable').DataTable({

                drawCallback: function () {

                    $(".chain-del-btn").click(function () {
                        let r = confirm('{!! __('Are you sure you want to delete this chain?') !!}');
                        return (r === true);
                    });
                },
                "processing": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    sProcessing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
                ajax: {
                    url: '{!! url()->current().'/index/dt' !!}',
                    method: 'GET',
                },
                columns: [
                    { data: 'id', name: 'id', id: 'id'},
                    { data: 'hostname', name:'hostname' },
                    { data: 'port', name: 'port' },
                    {
                        mRender: function(data, type, row){
                            return row.https === 'true' ? '<i class="fa fa-check text-success text-center" aria-hidden="true"></i>' : '<i class="fa fa-times text-danger text-center" aria-hidden="true"></i>';
                        }, class: 'text-center'
                    },
                    { data: 'actions', name: 'actions', class : 'text-center' }
                ],
                columnDefs: [
                ]
            });
        });
    </script>
@endsection

@section('stylesheets')
    @include('security-pipeline.partials.css.create-edit-style')
@endsection
