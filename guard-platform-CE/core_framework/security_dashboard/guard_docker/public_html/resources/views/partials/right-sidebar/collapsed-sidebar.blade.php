@php
    $warningCount = 0;
    foreach($servers as $server){
        if($server['status'] != '0'){
        $warningCount++;
        }
    }
@endphp

<div class="card mb-2 ">
    <a href="#" class="font-weight-bold font-size-sm text-dark mr-auto ml-auto mt-3 pb-2 expand-left">
        <i class="icon-arrow-left12 "></i>
    </a>
    <div class="border-bottom"></div>
    <div class="card-body pt-2 pl-0 ml-auto mr-auto">
        <a href="#" class="navbar-nav-link caret-0 expand-left font-weight-bold font-size-sm text-dark">
            <i class="icon-server "></i>
            <span class="badge badge-pill bg-warning-400 m-sm-0 " style="">{!! $warningCount++ !!}</span>
        </a>
    </div>
</div>