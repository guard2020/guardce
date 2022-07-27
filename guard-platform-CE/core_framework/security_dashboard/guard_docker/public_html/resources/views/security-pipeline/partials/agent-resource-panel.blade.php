@foreach($selectedInstanceArr as $resourceKey=>$InstanceParamVal)
    @php
        $instance = $InstanceParamVal['instanceId'];
        $showResource = ($resourceKey === 0) ? 'show' : '';
        $angleSource = ($resourceKey === 0) ? 'fa-chevron-up text-guard-dark' : 'fa-chevron-down text-white';
    @endphp

    <div class="my-2">
        <div class="row d-flex bg-guard text-left my-0 py-2 mx-auto w-100 align-items-center justify-content-between" role="tab" id="heading-{!! $instance !!}">
            <div class="col-12">
                <div class="heading d-flex">
                    <a data-toggle="collapse" class="expand-source w-100" data-parent="#accordionEx" href="#collapse-resource-{!! $instance !!}" aria-expanded="true"
                       aria-controls="collapseOne1">
                        <div class="d-inline-block mx-3 w-100">
                            <h6 class="mb-0 ml-2 d-inline text-white">{!! $instance !!} </h6>
                            <i class="fas {!! $angleSource !!} fa-custom-size rotate-icon d-inline float-right pr-3 align-items-center justify-content-center"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div id="collapse-resource-{!! $instance !!}" class="collapse {!! $showResource !!} row mx-auto" role="tabpanel" aria-labelledby="heading-{!! $instance !!}" data-parent="#accordionEx">
            <!--Accordion wrapper-->
            <div class="accordion md-accordion col-12 px-0" id="accordionEx" role="tablist" aria-multiselectable="true">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center">File name</th>
                            <th class="text-center">Upload file</th>
                            <th class="text-center">Download file content</th>
                        </tr>
                        </thead>
                        @if(!empty($resourceOptions))
                            @foreach($resourceOptions as $key => $resource)
                                @php
                                    $sourceContent = "";
                                    $disabledClass = 'disabled';
                                    if(isset($pipeline['resources'][$instance][$key]['content']) && !empty($pipeline['resources'][$instance][$key]['content'])){
                                        $sourceContent = $pipeline['resources'][$instance][$key]['content'];
                                        $disabledClass = null;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        {!! $resource !!}
                                        <input type="hidden" name="resource[{!! $instance !!}][{!! $key !!}][{!! $resource !!}][resource_id]" value="{!! $resource !!}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary btn-file btn-labeled btn-labeled-left rounded-pill">
                                            <b style="border-radius: 50rem;"><i class="icon-file-upload2"></i></b>
                                            Upload
                                            <input type="hidden" id="content_{!! $instance !!}_{!! $resource !!}" name="resource[{!! $instance !!}][{!! $key !!}][{!! $resource !!}][content]" value="{!! $sourceContent !!}">
                                            <input type="file" name="resource[{!! $instance !!}][{!! $key !!}][{!! $resource !!}][content_file]" class="file-input" data-show-caption="false" data-show-upload="false" data-fouc="">
                                        </button>
                                    </td>
                                    <td>
                                        <div class="tool-tip" data-toggle="tooltip" data-placement="left" title="{!! $disabledClass ? 'No file available': '' !!}">
                                            <button type="button" id="{!! $instance !!}_{!! $resource !!}"
                                                    class="btn btn-sm btn-primary btn-file btn-labeled btn-labeled-left rounded-pill {!! $disabledClass ? "btn-secondary" : "btn-info" !!}" {!! $disabledClass !!}
                                                    onclick="getResourceTextDownload('{!! $instance !!}_{!! $resource !!}'); return false;">
                                                <b style="border-radius: 50rem;"><i class="icon-file-download2"></i></b>
                                                Download
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="7" class="text-center">No config file found for this agent instance!</td></tr>
                        @endif

                    </table>
                </div>
            </div>
            <!-- Accordion wrapper -->
        </div>
    </div>
@endforeach