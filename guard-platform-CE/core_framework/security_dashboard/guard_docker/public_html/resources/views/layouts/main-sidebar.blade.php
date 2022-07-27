<!-- Main sidebar -->
<div class="sidebar sidebar-guard sidebar-main sidebar-expand-md sidebar-custom ">
    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Navigation
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->
    <!-- Sidebar content -->
    <div class="sidebar-content">
        <!-- User menu -->
        <div class="sidebar-user-material">
            <div class="sidebar-user-material-body">
                <div class="card-body text-center">
                    <a href="#">
                        <img src="{!! asset('images/user_icon.png') !!}" class="img-fluid rounded-circle shadow-1 mb-3" width="80" height="80" alt="">
                    </a>
                    <h6 class="mb-0 text-white text-shadow-dark">{!! isset(auth()->user()->name) ? auth()->user()->name : '' !!}</h6>
                    <span class="font-size-sm text-white text-shadow-dark">Information security officer</span>
                </div>

                <div class="sidebar-user-material-footer">
                    <a href="#mainMenu" class="d-flex justify-content-between align-items-center text-shadow-dark dropdown-toggle
                    text-uppercase font-size-xs line-height-xs " id="mainToggle" aria-expanded="true" data-toggle="collapse"><span>Main Menu</span></a>
                </div>
            </div>

            <div class="collapse show" id="mainMenu">
                <ul class="nav nav-sidebar ">
                    <!-- Main -->
                    <li class="nav-item pt-0">
                        <a href="{!! route('dashboard') !!}" class="nav-link {!! request()->segment(1) === null?'active':''!!}">
                            <i class="icon-home4"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('service-topology.index') !!}" class="nav-link {!! request()->segment(1) === 'servicetopology'?'active':''!!}">
                            <i class="fas fa-code-branch"></i>
                            <span>Service Topology</span>
                            <span class="badge badge-light ml-auto " id="serviceStats"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{!! route('anomalies.index') !!}" class="nav-link {!! request()->segment(1) === 'anomalies-dashboard'?'active':''!!}">
                            <i class="fas fa-desktop"></i>
                            <span>Anomalies Analysis</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a href="{!! route('notifications.index') !!}" class="nav-link {!! request()->segment(1) === 'notifications'?'active':''!!}">
                            <i class="fa fa-envelope"></i>
                            <span>Threat Notifications</span>
                            <span class="badge badge-light ml-auto " id="notificationsStats"></span>
                        </a>
                    </li>
                    <li class="nav-item">
                      <a href="{!! route('data-trace.index') !!}" class="nav-link {!! request()->segment(1) === 'data-trace' ? 'active' : ''!!}">
                          <i class="fas fa-chart-line"></i>
                          <span>User Data Traceability</span>
                      </a>
                    </li>

                    <li class="nav-item">
                        <a href="{!! route('security-pipeline.index') !!}" class="nav-link {!! request()->segment(1) === 'security-pipeline'?'active':''!!}">
                            <i class="fas fa-stream"></i>
                            <span>Security Pipeline</span>
                            <span class="badge badge-light ml-auto" id="pipelineStats"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /user menu -->
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->
</div>
<!-- /main sidebar -->

<script type="text/javascript">

    $(function () {

        $.get('{!! route('data.counts') !!}')
            .done(response => {

                let serviceStats = $('#serviceStats');
                let notificationsStats = $('#notificationsStats');
                let pipelineStats = $('#pipelineStats');

                if(typeof response.services !== 'undefined' && $.isNumeric(response.services)){
                    serviceStats.html(response.services);
                }

                if(typeof response.notifications !== 'undefined' && ($.isNumeric(response.notifications) || typeof response.notifications == 'string')){
                    notificationsStats.html(response.notifications);
                }

                if(typeof response.pipelines !== 'undefined' && $.isNumeric(response.pipelines)){
                    pipelineStats.html(response.pipelines);
                }

            })
            .fail(response => {
                console.log('Could not retrieve count of general data (count of pipelines, count of services, count of notifications');
        });

    });

</script>
