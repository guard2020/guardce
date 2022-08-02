@extends('layouts.app')

@php
    $extraBtns = (isset($userPermission) && $userPermission === true && $agent_instances > 0) ?
                    '<a href="'.route('security-pipeline.create').'" class="btn btn-success"><i class="fas fa-plus"></i> Create Pipeline</a>' :
                    null;
@endphp

@section('page-header')
    @include('layouts.page-header', [
        'pageHeadTitle' => 'Security Pipeline',
        'extraBtns' => $extraBtns,
        'breadcrumbs' => [
            [
                'name' => 'Pipelines',
                'link' => '',
                'icon' => 'fas fa-stream'
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
                            <h5 class="card-title">Pipelines</h5>
                            <div class="header-elements">
                                <div class="list-icons">
                                    <a class="list-icons-item" data-action="collapse"></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                @if($error)
                                    <div class="w-100 alert alert-warning " role="alert">
                                        <h6 class="alert-heading">Data error!</h6>
                                        <p>
                                            The current data in the CB Manager is not enough. The Security Pipeline requires that
                                            the Execution Environment, Agents and Agent Instances indexes/tables contain data.
                                        </p>
                                    </div>
                                @else
                                    <div class="col">
                                        <table class="table table-sm table-bordered" id="pipelineTable" style="border-top: 1px solid darkgray;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ID</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">Agents</th>
                                                    <th class="text-center">Created</th>
                                                    <th class="text-center">Modified</th>
                                                    <th class="text-center">Creator (User)</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Chain</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <div class="row">
                                            <div class="col-8">
                                            </div>
                                            <div class="col-4">
                                                <div class="float-right">
                                                    <button class="btn btn-sm pipeline-restart-btn btn-info d-none" data-id="">Restart</button>
                                                    <button class="btn btn-sm btn-primary pipeline-start-stop-btn d-none" data-id=""></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Table -->
    <script src="{!! asset('limitless/js/plugins/tables/datatables/datatables.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/tables/datatables/extensions/responsive.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/tables/datatables/extensions/buttons.min.js') !!}"></script>
    @include('security-pipeline.partials.js.update-status-scripts')
    <script type="text/javascript">
        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let table = $('#pipelineTable').DataTable({

                drawCallback: function () {
                    $('#pipelineTable tbody').on( 'click', 'tr', function () {
                        let rowData = table.row( this ).data();
                        let actionButton = $('.pipeline-start-stop-btn');
                        let actionRestartButton = $('.pipeline-restart-btn');
                        let pipelineTableRow = $('#pipelineTable tbody tr');
                        let actionButtonLabel;

                        $('[data-toggle="tooltip"]').tooltip();
                        // Adding and removing the selected class on click event
                        pipelineTableRow.removeClass('selected');
                        $(this).addClass('selected');

                        //If status is not start or stop, enable btn
                        let currentPipelineStatus = $(this).closest("tr").find(".status .badge").text().toLowerCase();

                        if(currentPipelineStatus !== 'start' || currentPipelineStatus !== 'stop'){
                            actionButton.removeClass('d-none');
                            if(currentPipelineStatus !== 'started'){
                                actionRestartButton.addClass('d-none');
                                actionButtonLabel = "Start";
                                actionButton.removeClass('btn-danger');
                                actionButton.addClass('btn-primary');
                            }else{
                                actionRestartButton.removeClass('d-none');
                                actionButtonLabel = "Stop";
                                actionButton.removeClass('btn-primary');
                                actionButton.addClass('btn-danger');
                            }
                        }else{
                            actionButton.addClass('d-none');
                            actionRestartButton.addClass('d-none');
                        }

                        // Setting up button label based on action taken
                        actionButton.text(actionButtonLabel);
                        actionButton.attr('data-id', rowData.id);
                        actionRestartButton.attr('data-id', rowData.id);
                    });




                    $(".pipeline-start-stop-btn").click(function (event) {
                        event.stopImmediatePropagation();
                        let r = confirm('{!! __('Are you sure you want to change the status of this pipeline?') !!}');
                        if(r === true) {
                            let pipelineId = $(this).attr('data-id');
                            let status = $('.pipeline-start-stop-btn').text().toLowerCase();
                            // this function used to update pipeline status
                            updatePipelineStatus(status, pipelineId);
                        } else {
                            return false;
                        }
                    });

                    $(".pipeline-restart-btn").click(function (event) {
                        event.stopImmediatePropagation();
                        let r = confirm('{!! __('Are you sure you want to restart this pipeline?') !!}');
                        if(r === true) {
                            let pipelineId = $(this).attr('data-id');
                            let status = $('.pipeline-restart-btn').text().toLowerCase();
                            // this function used to update pipeline status
                            updatePipelineStatus(status, pipelineId);
                        } else {
                            return false;
                        }
                    });

                    $(".pipeline-del-btn").click(function () {
                        let r = confirm('{!! __('Are you sure you want to delete this pipeline?') !!}');
                        return (r === true);
                    });
                    $(".pipeline-reload-btn").click(function () {
                        let r = confirm('{!! __('Are you sure you want to reload this pipeline?') !!}');
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
                    { data: 'id', name: 'id', id: 'id', class:'text-break m-width-350'},
                    { data: 'name', name:'name', class: 'm-width-350' },
                    { data: 'agents', name: 'agents', class : 'text-center text-break m-width-350'},
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'user', name: 'user' },
                    { data: 'status', name: 'status', id: 'status', class : 'status text-center' },
                    { data: 'chain', name: 'chain', id: 'chain', class: 'm-width-350 text-break' },
                    { data: 'actions', name: 'actions', class : 'text-center' },
                ],
                columnDefs: [
                    {
                        orderable: false,
                        targets: [7]
                    },
                    {
                        searchable: false,
                        targets: [7]
                    }
                ]
            });
        });
    </script>
@endsection

@section('stylesheets')
   @include('security-pipeline.partials.css.create-edit-style')
@endsection
