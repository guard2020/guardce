@php

if(!isset($extraBtns)){
    $extraBtns = '';
}

$count = 1;

@endphp


<div class="page-header page-header-light">
    <div class="page-header-content header-elements-lg-inline">
        <div class="page-title d-flex">
            <h4><i class="mr-2"></i> {!! $pageHeadTitle !!}</h4>
            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            <div class="d-flex justify-content-center">
                {{--<a href="#" class="btn btn-labeled btn-labeled-right bg-primary">Button <b><i class="icon-menu7"></i></b></a>--}}
                {{--TODO Buttons here--}}
                {!! $extraBtns !!}
            </div>
        </div>
    </div>

    <div class="breadcrumb-line breadcrumb-line-light header-elements-lg-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{!! route('dashboard') !!}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                @foreach($breadcrumbs as $breadcrumb)
                    @if($count === count($breadcrumbs))
                        <span class="breadcrumb-item active">
                            <i class="{!! isset($breadcrumb['icon']) ? $breadcrumb['icon'].' mr-2' : '' !!}"></i>
                            {!! $breadcrumb['name'] !!}
                        </span>
                    @else
                        <a href="{!! $breadcrumb['link'] !!}" class="breadcrumb-item"><i class="{!! $breadcrumb['icon'] ? $breadcrumb['icon'] : '' !!} mr-2"></i> {!! $breadcrumb['name'] !!}</a>
                    @endif
                    @php($count++)
                @endforeach
            </div>

            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>