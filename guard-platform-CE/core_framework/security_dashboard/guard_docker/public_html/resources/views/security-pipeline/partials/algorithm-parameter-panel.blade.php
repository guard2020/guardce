@foreach($selectedInstanceArr as $paramKey=>$paramVal)
    @php
        $instance = $paramVal['algoInstanceId'];
        $instanceParam = $paramVal['algoInstanceParam'];
        $angleClass = ($paramKey === 0) ? 'fa-chevron-up text-guard-dark' : 'fa-chevron-down text-white';
        $instanceCleanString = preg_replace('/[^A-Za-z0-9\-]/', '\\\\$0', $instance);

    @endphp

    <div class="my-2">
        <div class="row d-flex bg-guard text-left my-0 py-2 mx-auto w-100 align-items-center justify-content-between" role="tab" id="heading-{!! $instance !!}">
            <div class="col-12">
                <div class="heading d-flex">
                    <a data-toggle="collapse" class="algo-expand-params w-100" data-parent="#accordionEx" href="#collapse-{!! $instanceCleanString !!}" aria-expanded="true" >
                        <div class="d-inline-block mx-3 w-100">
                            <h6 class="mb-0 d-inline text-white">{!! $instance !!} </h6>
                            <i class="fas {!! $angleClass !!} fa-custom-size rotate-icon d-inline float-right pr-3 align-items-center justify-content-center"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div id="collapse-{!! $instance !!}" class="collapse {!! $paramKey === 0 ? 'show' : ''; !!} row mx-auto" role="tabpanel" aria-labelledby="heading-{!! $instance !!}" data-parent="#accordionEx">
            <!--Accordion wrapper-->
            <div class="accordion md-accordion col-12 px-0" id="accordionEx" role="tablist" aria-multiselectable="true">
                <div class=" table-responsive ">
                    <table class="table table-sm table-bordered ">
                        <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Description</th>
                            <th class="text-center">Example</th>
                            <th class="text-center">Config/Schema</th>
                            <th class="text-center">Config/Source</th>
                            <th class="text-center">Config/Path</th>
                            <th class="text-center">Input</th>
                        </tr>
                        </thead>


                        @if(empty($algorithmParams))
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="w-100 alert alert-info " role="alert">
                                        <h6 class="alert-heading">No parameters</h6>
                                        <p>
                                            The algorithm of the selected algorithm instance <strong>{!! $instance !!}</strong> does not have any parameters. Note: The instance might accept a configuration file.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @if(!empty($algorithmParams['parameters']))
                                @foreach($algorithmParams['parameters'] as $key=>$parameter)
                                    @php
                                        $algorithmConfigParam = $parameter;
                                        unset($algorithmConfigParam['example']);
                                        $inputType = "text";
                                        $errorContainer = null;
                                        $switch = null;
                                        $value = null;
                                        $required = "";
                                        if(!empty($parameter['type'])){
                                            if($parameter['type'] === "integer" || $parameter['type'] === "number"){
                                               $inputType = "number";
                                            } else if($parameter['type'] === "string" || $parameter['type'] === "binary"){
                                               $inputType = "text";
                                            } else if($parameter['type'] === "time-duration"){
                                               $inputType = "time-duration";
                                            } else if($parameter['type'] === "boolean"){
                                               $required = "";
                                               $inputType = "checkbox";
                                               $switch = 'data-toggle="switch"';
                                               $value = 'value="true"';
                                               $errorContainer = 'data-parsley-errors-container=".parsley-error"';
                                            } else if($parameter['type'] === "choice"){
                                               $inputType = "select";
                                            }
                                        }
                                        $existingValue = "";
                                        if(!empty($pipeline[$instance][$key]) && is_array($pipeline[$instance][$key])){
                                            if(!empty($pipeline[$instance][$key]['input'])){
                                                $pipeline[$key]["input"] = json_encode($pipeline[$instance][$key]['input']);
                                            }

                                            $existingValue = ( $pipeline[$instance][$key]['input'] !== false) ?  $pipeline[$instance][$key]['input'] : "";

                                        }elseif (!empty($instance['parameters'])){
                                            foreach ($instance['parameters'] as $item){
                                                if($item['id'] === $parameter['id']){
                                                    if(is_array($item["value"]["new"])){
                                                        $input = json_encode( $item["value"]["new"]);
                                                        $existingValue = ($item["value"]["new"] !== false) ? $input : "";
                                                    }else{
                                                        $existingValue = ($item["value"]["new"] !== false) ? $item["value"]["new"] : "";
                                                    }
                                                }
                                            }
                                        }

                                    @endphp

                                    <tr>
                                        <input type="hidden" name='algorithmParam[{!! $instance !!}][{!! $key !!}][algo_config]' value='{!! json_encode($algorithmConfigParam) !!}'>
                                        <td>{!! $parameter['id'] !!} </td>
                                        <td>{!! isset($parameter['description']) ? $parameter['description'] : "-" !!}</td>
                                        <td class="px-1">{!! isset($parameter['example']) ? $parameter['example'] : "-" !!}</td>
                                        <td>{!! isset($parameter['config']['schema']) ? $parameter['config']['schema'] : "-" !!}</td>
                                        <td class="px-1">{!! isset($parameter['config']['source']) ? $parameter['config']['source'] : "-" !!}</td>
                                        <td>{!! isset($parameter['config']['path']) && (is_array($parameter['config']['path'])) ? implode(', ', $parameter['config']['path']) : "-" !!}</td>
                                        @if($inputType === "select")
                                            <td>
                                                <div>
                                                    <select name='algorithmParam[{!! $instance !!}][{!! $key !!}][input]' id="text_param_{!! $parameter['id'] !!}" required="required" style="width: 9.8rem; height: 1.7rem;">
                                                        @if(is_array($parameter['values']))
                                                            @foreach($parameter['values'] as $item)
                                                                <option value="{!! $item !!}">{!! $item !!}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </td>
                                        @elseif($inputType === "text")
                                            <td class="text-center"><textarea rows="5" name='algorithmParam[{!! $instance !!}][{!! $key !!}][input]' {!! !empty($existingValue) && $inputType === 'checkbox' ? "checked" : "" !!} {!! $switch !!} {!! $required !!}
                                                {!! $errorContainer !!} data-parsley-multiple="false" id="text_param_{!! $parameter['id'] !!}">{!! !empty($existingValue) ? htmlspecialchars($existingValue, ENT_QUOTES, "UTF-8") : $value !!}</textarea></td>
                                        @elseif($inputType === "time-duration")
                                            <td class="text-center"><input type="number" name='algorithmParam[{!! $instance !!}][{!! $key !!}][input]' {!! !empty($existingValue) ? "value='".htmlspecialchars($existingValue, ENT_QUOTES, "UTF-8")."'" : $value !!} {!! $required !!}
                                                {!! $errorContainer !!} data-parsley-multiple="false" id="text_param_{!! $parameter['id'] !!}" placeholder="Time in seconds" style="width: 9.8rem; height: 1.7rem;"></td>
                                        @else
                                            <td class="text-center"><input type="{!! $inputType !!}" name='algorithmParam[{!! $instance !!}][{!! $key !!}][input]' {!! !empty($existingValue) && $inputType === 'checkbox' ? "checked" : "" !!} {!! !empty($existingValue) ? "value='".htmlspecialchars($existingValue, ENT_QUOTES, "UTF-8")."'" : $value !!}  {!! $switch !!} {!! $required !!}
                                                {!! $errorContainer !!} data-parsley-multiple="false" id="text_param_{!! $parameter['id'] !!}" {!! $inputType !== 'checkbox' ? 'style="width: 9.8rem; height: 1.7rem;"' : '' !!}></td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="7" class="text-center">No parameter found to this algorithm!</td></tr>
                            @endif
                        @endif

                    </table>
                </div>

            </div>
            <!-- Accordion wrapper -->
        </div>
    </div>
@endforeach