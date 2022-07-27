<fieldset class="card-body">
    <div class="row py-3">
        <div class="col-12">
            <div>
                <h6 class="font-weight-bold">Available Agents</h6>
            </div>
            <div class="table-responsive agent-list-form">
                <table class="table table-sm table-bordered tableFixHead" id="pipelineFormTable" style="border-top:none;">
                    <thead>
                    <tr style="height: 42px;">
                        <th class="text-center">#</th>
                        <th class="text-center">ID</th>
                        <th colspan="5" class="text-center">Description</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($agentWiseInsAndEnv as $key=>$agt)
                        <tr id="agent_{!! $agt['agents']['id'] !!}">
                            <td style="text-align: -webkit-center !important; width: 100px;">
                                <div class="align-content-center">
                                    <input type="radio" name="agent_id" value="{!! $agt['agents']['id'] !!}" required="required"
                                           class="form-check-input-styled" id="{!! $agt['agents']['id'] !!}"
                                           @if(isset($pipeline))
                                           {!! ($pipeline['agent_id'] === $agt['agents']['id']) ? 'checked': "" !!}
                                           @else
                                           {!! ($key === 0) ? "checked" : "" !!}
                                           @endif
                                           data-fouc>
                                </div>
                            </td>
                            <td class="pl-3">{!! $agt['agents']['id'] !!}</td>
                            <td class="pl-3" colspan="5">
                                @if(isset($agt['agents']['description']))
                                    {!! $agt['agents']['description'] !!}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if(!empty($agt['environment']) && isset($agt['environment']))
                            <tr id="agent_env_{!! $agt['agents']['id'] !!}" class="agent_info env-table">
                                <td colspan="4" style="padding: 0 0 0 0;" class="env-table">
                                    <table class="table table-env table-sm borderless">

                                        @foreach($agt['environment'] as $envKey=>$env)
                                            @php
                                                $agentInstanceParams = isset($agt['instance'][$envKey]['parameters']) ? $agt['instance'][$envKey]['parameters']: null;
                                                $agentInstanceParams = htmlspecialchars(json_encode($agentInstanceParams), ENT_QUOTES, 'UTF-8');
                                                $checked = false;

                                                if(isset($agt['instance'][$envKey]['id']) && $agt['instance'][$envKey]['exec_env_id'] === $env['id']){
                                                    $agentInstanceId = $agt['instance'][$envKey]['id'];
                                                }else{
                                                    $agentInstanceId = null;
                                                }

                                                if(!empty($pipeline)){
                                                    if(!empty($pipeline['agent_configs']) && is_array($pipeline['agent_configs'])) {
                                                        $pipelineInstances = array_column($pipeline['agent_configs'], 'agent_instance_id');
                                                        if(in_array($agentInstanceId, $pipelineInstances)){
                                                            $checked = true;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            @if($env['duplicate'])
                                                <tr data-toggle="tooltip" data-html="true" title="Currently used by pipeline: <i>{!! $env['pipelineName'] !!}</i>" {!!  ($env['duplicate']) ? 'style="background-color:#DEDEDE;"' : ""; !!}>
                                            @else
                                                <tr>
                                            @endif
                                                    <td class="text-center align-baseline parsley-custom-container" id="{!! $env['id'] !!}" style="width: 100px; text-align: -webkit-center !important;">
                                                        @if($env['duplicate'] && !$checked)
                                                            <span><i class="fas fa-lock custom-lock"></i></span>
                                                        @else
                                                            <div class="align-content-center">
                                                                <input type="hidden" name="instance_param" id="instance-{!! $agt['agents']['id'] !!}-{!! $env['id'] !!}" value='{!! $agentInstanceParams !!}'>
                                                                <input type="checkbox" value="{!! $env['id'] !!}" {!! ($env['duplicate'] && !$checked) ? 'disabled' : "" !!} class="form-check-input-styled"
                                                                       name="environment_id[]"
                                                                       @if(isset($pipeline))
                                                                       {!! ($checked) ? 'checked' : "" !!}
                                                                       @else
                                                                       {!! ($key === 0) ? "" : "" !!}
                                                                       @endif
                                                                       data-fouc>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{!! $env['id'] !!} </td>
                                                    <td>{!! isset($env['hostname']) ? $env['hostname'] : "-" !!}</td>
                                                    <td>{!! isset($env['description']) ? $env['description'] : "-" !!}</td>
                                                    <td>{!! isset($env['type_description']) ? $env['type_description']['name'] : "-" !!}</td>
                                                    <td>{!! isset($env['partner']) ? $env['partner'] : "-" !!}</td>
                                                </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</fieldset>
<fieldset class="card-body">
    <div class="row pt-3">
        <div class="col-12">
            <h6 class="font-weight-bold">Parameters for selected agent</h6>
            <div class="text-center" id="agentParameter">{{--Agents paramertes populated dynamically--}}</div>
        </div>
    </div>
</fieldset>