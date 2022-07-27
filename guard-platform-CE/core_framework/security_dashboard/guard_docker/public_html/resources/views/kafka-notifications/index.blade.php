@php
    $flashes = session('flash_notification', collect())->toJson();
@endphp
@extends('layouts.app')
@section('page-header')
    @include('layouts.page-header', [
        'pageHeadTitle' => 'Threat Notifications',
        'breadcrumbs' => [
            [
                'name' => 'Notifications',
                'icon' => 'fa fa-envelope',
                'link' => ''
            ]
        ]
    ])
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-2">
            <div class="sidebar-content">
                <!-- Sidebar search -->
                @include('kafka-notifications.partials.sidebar-search')
                <!-- /sidebar search -->
                <!-- Filter -->
                @include('kafka-notifications.partials.source-filter')
                <!-- /filter -->
            </div>
        </div>
        <div class="col-lg-10">
            @include('kafka-notifications.partials.table-card')
        </div>
    </div>
@endsection
@section('scripts')
    <!-- Table -->
    <script src="{!! asset('limitless/js/plugins/tables/datatables/datatables.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/tables/datatables/extensions/responsive.min.js') !!}"></script>
    <script src="{!! asset('limitless/js/plugins/tables/datatables/extensions/buttons.min.js') !!}"></script>
    <script type="text/javascript">
        $(function(){
            let lastTimestamp;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let table = $('#notifications_table');

            let sources = [];
            table.DataTable({
                drawCallback: function () {
                    setTimeout(filterCheckboxes, 1000);

                    $('#searchBar').off('keyup').on('keyup', function(){
                        table.dataTable().fnFilter(this.value);
                        table.dataTable().fnPageChange('first');
                    });
                },

                ajax: {
                    url: '{!! route('notifications.index.dt') !!}',
                    method: 'GET',
                },
                dom: '<"top">tr<"bottom"ip>',
                processing: true,
                ordering: true,
                order: [[ 4, "desc" ]],
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                language: {
                    lengthMenu: '<span>Show:</span> _MENU_',
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw "></i><span class="sr-only">Loading...</span> ',
                    emptyTable: "No notifications available. Kafka topic [detection-results] is empty."
                },
                columns: [
                    {
                        mRender: function(data, type, row){
                            return row.SOURCE ? row.SOURCE : '-';
                        }
                    },
                    {
                        mRender: function(data, type, row){
                            return row.SEVERITY ? row.SEVERITY : '-';
                        }
                    },
                    {
                        mRender: function(data, type, row){
                            return row.DESCRIPTION ? row.DESCRIPTION : '-';
                        }
                    },
                    {
                        mRender: function(data, type, row) {
                            return '<span class="small">'+JSON.stringify(row.DATA, null, "\t")+'</span>';
                        }
                    },
                    { data: 'unixTimestamp', name:'unixTimestamp', visible:false},
                    {
                        mRender: function(data, type, row) {
                            if(row.unixTimestamp > Math.floor(Date.now() / 1000)){
                                return '<div class="es-timestamp" data-value="'+row.timestamp+'">'+moment.unix(row.unixTimestamp/1000).format('DD/MM/YYYY HH:mm:ss')+'</div>';
                            }else{
                                return '<div class="es-timestamp" data-value="'+row.timestamp+'">'+moment.unix(row.unixTimestamp).format('DD/MM/YYYY HH:mm:ss')+'</div>';
                            }
                        }
                    },
                ],
                columnsDefs: [
                    {
                        searchable: false,
                        targets: [4]
                    },
                ],
                'initComplete': function(settings, json){
                    $.each(json.data, function (index, value) {
                        if(!sources.includes(value.SOURCE)){
                            sources.push(value.SOURCE);
                        }
                    });
                    addSourcesFilter(sources);
                    $('.form-check-input-styled').uniform();
                    if(!sources.length){
                        callbackKafkaStart('failed');
                    }
                    let testRow = $("#notifications_table tbody tr:first");
                    lastTimestamp = testRow.find('td:last').find('div').attr('data-value');
                }
            });

            $('#searchBar').click(function() {
                if($('#searchBar').val() !== ""){
                    $('#notifications_table').dataTable().fnFilter('');
                }
            });

          setInterval( function () {
            checkNewNotificationExists(lastTimestamp);
          }, 7000 );

            function addSourcesFilter(sources){

                let filter = $('.sources-filter');
                filter.empty();

                $.each(sources, function (index, value) {
                    filter.append(
                        '<div class="form-check form-check-right">' +
                        '<label class="form-check-label">' +
                        '<input type="checkbox" class="form-check-input-styled" value="'+value+'" checked data-fouc>' +
                        value +
                        '</label>' +
                        '</div>'
                    );
                });
            }

            function filterCheckboxes(){
                if(sources.length){
                    $('.sources-filter :checkbox').off('click').on('click', function () {
                        let checkbox = $(this);
                        $.uniform.update(checkbox);
                        let filter = $('.sources-filter :checkbox:checked').map(function(){
                            return this.value;
                        }).get().join('|');
                        if(filter.length === 0){
                            filter = "nodata";
                        }
                        table.DataTable().column(0).search(filter, true, false, false).draw(false);
                        table.dataTable().fnPageChange('first');

                    });
                }
            }

            function callbackKafkaStart(status){
                switch(status){
                    case 'successful':
                        new PNotify({
                            title: 'Kafka Consumer started',
                            text: 'Connection established. Consuming data...',
                            addclass: 'bg-success border-success',
                            delay: 3000,
                            closer: true,
                        });
                        break;
                    case 'updated':
                        new PNotify({
                            title: 'New Notifications!',
                            text: 'New notifications have been added to the table.',
                            addclass: 'bg-info border-info',
                            delay: 3000,
                            closer: true,
                        });
                        break;
                    case 'failed':
                        new PNotify({
                            title: 'Kafka Consumer started',
                            text: 'There are no notifications available.',
                            addclass: 'bg-warning border-warning',
                            delay: 3000,
                            closer: true,
                        });
                        break;
                    default:
                        break;
                }
            }

            // this function checks if new notification exists and returns last timestamp
            function checkNewNotificationExists(){
                $.ajax({
                    url: '{!! route('notifications.checkNew') !!}',
                    data: {
                        'timestamp': lastTimestamp,
                    },
                    method: 'GET',
                    dataType: 'JSON',
                    success: function (data) {
                        if(data.status === true){
                            getNotificationList();
                            lastTimestamp = data.newTimestamp;
                        } else {
                            return false;
                        }
                    }
                });
            }

            //TODO-when update filter by source no updated...-Shyam
            //function to retrieve data from es
            function getNotificationList() {
                let printTable = $("#notifications_table").DataTable();
                let updateStatus = false;
                $.ajax({
                    url: '{!! route('notifications.reload.index.dt') !!}',
                    data: {
                        'timestamp': lastTimestamp,
                    },
                    method: 'GET',
                    dataType: 'JSON',
                    success: function (data) {
                        if (data.data.length > 0) {
                            $.each(data.data, function (key, value) {
                                if(!sources.includes(value.SOURCE)){
                                    sources.push(value.SOURCE);
                                    updateStatus = true;
                                }

                                printTable.row.add(value).draw();
                            });
                            if(updateStatus=== true) {
                                addSourcesFilter(sources);
                                $('.form-check-input-styled').uniform();
                            }

                            callbackKafkaStart('updated');
                        }
                    }
                });
            }
        });
    </script>
    {{ session()->forget('flash_notification') }}
@endsection

@section('stylesheets')
    @include('kafka-notifications.css.page-style')
@endsection