@extends('layouts.app')

@section('page-header')
    @include('layouts.page-header', [
        'pageHeadTitle' => 'User Profile',
        'breadcrumbs' => [
            [
                'name' => 'Profile',
                'link' => '',
                'icon' => 'icon-user'
            ]
        ]
    ])
@endsection
@section('content')
    <div class="d-lg-flex align-items-lg-start">
        <!-- Left sidebar component -->
        <div class="sidebar sidebar-light bg-transparent sidebar-component sidebar-component-left wmin-300 border-0 shadow-none sidebar-expand-lg">
            <!-- Sidebar content -->
            <div class="sidebar-content">
                <!-- Navigation -->
                <div class="card" style="height: 480px;">
                    @include('users.partials.profile-left-sidebar')
                </div>
                <!-- /navigation -->
            </div>
            <!-- /sidebar content -->
        </div>
        <!-- /left sidebar component -->

        <!-- Right content -->
        <div class="tab-content flex-1">
            <div class="tab-pane fade active show" id="profile">
                <!-- Profile info -->
                <div class="card" id="profileSection" style="height: 480px;">
                    @include('flash-message')
                    <div class="card-header">
                        <h6 class="card-title">Profile information</h6>
                    </div>
                    <div class="card-body">
                        @include('users.partials.profile-info')
                    </div>
                </div>
                <!-- /profile info -->

                <!-- Account settings -->
                <div class="card" id="settingSection">
                    <div class="card-header">
                        <h6 class="card-title">Account settings</h6>
                    </div>
                    <div class="card-body">
                       @include('users.partials.profile-setting')
                    </div>
                </div>
                <!-- /account settings -->
                <!-- Users List -->
                <div class="card" id="usersSection">
                    <div class="card-body">
                        @include('users.partials.user-list')
                    </div>
                </div>
                <!-- Users List -->
            </div>
        </div>
        <!-- /right content -->
    </div>
@endsection

@section('scripts')
    @include('users.partials.js.user-profile-scripts')

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
            let table = $('#usersTable').DataTable({
                drawCallback: function () {
                    $(".user-del-btn").click(function () {
                        let r = confirm('{!! __('Are you sure you want to delete this user?') !!}');
                        return (r === true);
                    });

                    $(".status-change").on("change paste keyup", function() {
                        let status = $(this).val();
                        let r = confirm('{!! __('Are you sure you want to change the user status?') !!}');
                        if(r === true) {
                            let userId = $(this).attr('id');
                            updateStatus(status, userId);
                        } else {
                            $(this).val(status);
                            return false;
                        }
                    });

                    $(".role-change").on("change paste keyup", function() {
                        let roleId = $(this).val();
                        let r = confirm('{!! __('Are you sure you want to change the user role?') !!}');
                        if(r === true) {
                            let userId = $(this).attr('id');
                            updateRole(roleId, userId);
                        } else {
                            $(this).val(roleId);
                            return false;
                        }

                    });
                },
                "processing": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    sProcessing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
                ajax: {
                    url: '{!! '/users/index/dt' !!}',
                    method: 'GET',
                },
                columns: [
                    { data: 'id', name: 'id', id: 'id'},
                    { data: 'name', name:'name' },
                    { data: 'email', name: 'email', class : 'text-center'},
                    { data: 'status', name: 'status', class : 'text-center'},
                    { data: 'role_id', name: 'role_id', class : 'text-center'},
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', class : 'text-center' },
                ],
                 columnDefs: [
                     {
                         orderable: false,
                         targets: [6]
                     },
                     {
                         searchable: false,
                         targets: [6]
                     }
                 ]
            });
        });

        function updateStatus($this, $userId){

            $.ajax({
                type: "POST",
                url: '{!! route('users.update-status') !!}',
                data: {
                    'status' : $this,
                    'user_id': $userId,
                },
                success: function (data) {
                    $('.spinloading').html('');
                    if(data.status === 'success') {
                       displaySuccessMsg(data.message);
                    }
                }
            });
        }

        function updateRole($this, $userId){
            $.ajax({
                type: "POST",
                url: '{!! route('users.update-role') !!}',
                data: {
                    'role' : $this,
                    'user_id': $userId,
                },
                success: function (data) {
                    $('.spinloading').html('');
                    if(data.status === 'success') {
                        displaySuccessMsg(data.message);
                    }
                }
            });
        }

        function displaySuccessMsg($message){
            $('.card-header').after('<div class="col-12 bg-primary-300 text-left my-2 py-2 border border-grey reload-msg" role="tab">'+$message+'</div>')
            $('.reload-msg').delay(3000).fadeOut("slow");
        }

    </script>
@endsection

@section('stylesheets')
    @include('users.partials.css.user-profile-style')
@endsection