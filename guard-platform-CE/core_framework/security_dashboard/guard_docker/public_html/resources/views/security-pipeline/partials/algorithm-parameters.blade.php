<div class="container p-0 m-0" style="max-width: inherit;">
    @if(!empty($selectedInstanceArr))
        <ul id="tabs" class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a id="tab-A" href="#pane-A" class="nav-link active" data-toggle="tab" role="tab">Configure Parameters</a>
            </li>
{{--            See TODO comment below --}}
{{--            <li class="nav-item">--}}
{{--                <a id="tab-B" href="#pane-B" class="nav-link {!! empty($resourceOptions) ? 'disabled' : ''; !!}" data-toggle="tab" role="tab">Upload Configuration</a>--}}
{{--            </li>--}}
        </ul>

        <div id="content" class="tab-content" role="tablist">
            <div id="pane-A" class="tab-pane fade show active " role="tabpanel" aria-labelledby="tab-A">
                @include('security-pipeline.partials.algorithm-parameter-panel')
            </div>

{{--            TODO-Tomas Currently algorithms cannot have resources/upload configuration. Check if this is true.  --}}
{{--            <div id="pane-B" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-B">--}}
{{--                @include('security-pipeline.partials.algorithm-resource-panel')--}}
{{--            </div>--}}
        </div>
    @else
        <div class="col-12">
            <div class="alert alert-info border-0 alert-dismissible">
                <span class="font-weight-semibold">No algorithm instance selected!</span> Please select at least one instance
            </div>
        </div>
    @endif
</div>

@include('security-pipeline.partials.js.algorithm-parameters-scripts')

