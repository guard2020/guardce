<div class="card mb-2 ">
    <div class="card-header bg-transparent header-elements-inline sidebar-right-toggle">
        <!--data target is empty, as the functionality is not working yet-->
        <a href="#" class="font-weight-bold font-size-sm text-dark" id="collapse-right">
            <i class="icon-arrow-right13 "></i>
        </a>
        <span class="text-uppercase ml-auto mr-auto font-size-sm font-weight-semibold">Server monitoring</span>
    </div>
    <div class="card-body p-0">

        <ul class="nav nav-sidebar" id="v-pills-tab" role="tablist" data-nav-type="accordion">

            <li class="mt-3 mb-1">
                <a href="#server-nav " class="server-nav nav-item-header dropdown-toggle " aria-expanded="true" data-toggle="collapse">
                    Available Servers
                    <i class="icon-arrow-down12 float-right mr-3 d-inline-block server-collapse-icon" ></i>
                </a>
            </li>
            <div class="collapse show" id="server-nav">
                @foreach($servers as $server)
                    <a class="nav-link pt-3 border-bottom {!! $server['status'] === 2?'bg-danger text-light':($server['status'] === 1?'bg-warning text-light':'') !!}"
                       id="v-pills-{!! $server['name'] !!}-tab" data-toggle="pill" href="#v-pills-{!! $server['name'] !!}"
                       role="tab" aria-controls="v-pills-{!! $server['name'] !!}" aria-selected="false"><i class="icon-server mr-4"></i>
                        Server: {!! $server['name'] !!}
                        <i class=" {!! $server['status'] === 1 || $server['status'] === 2 ?'icon-warning':'' !!} ml-3"></i>
                    </a>
                @endforeach
            </div>
        </ul>
    </div>
</div>