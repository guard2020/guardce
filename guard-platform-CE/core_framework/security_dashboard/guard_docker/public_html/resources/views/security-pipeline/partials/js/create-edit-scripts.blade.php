<script src="{!! asset('js/parsley.min.js') !!}"></script>
<script type="text/javascript">
    let selected;
    let agents = JSON.parse({!! json_encode($agentsJson) !!});
    let algorithms = {!! json_encode($algorithms) !!};
    let pipelineId = $('#pipelineId').val();
    $('.form-check-input-styled').uniform();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function () {
        let form = $('#smart-dashboard-form');
        form.parsley();

        // tooltip
        $('[data-toggle="tooltip"]').tooltip();

        let agentInfoLines = $('.agent_info');
        let agentId = $('input[type="radio"]:checked').attr('id');
        let pipelineFormRow = $('#pipelineFormTable tbody tr');
        let algoPipelineFormRow = $('#algoPipelineFormTable tbody tr');

        let selectedAgent = $('#agent_'+cleanedjQueryString(agentId));
        let preSelectedAgentEnv = $('#agent_env_'+cleanedjQueryString(agentId));
        let  selectedInstanceArr = [];
        let i = 0;

        let algoSelectedInstanceArr = [];
        let algorithmId = $('input[name="algorithm_id"]:checked').attr('id');
        let selectedAlgorithm = $('#algorithm_'+cleanedjQueryString(algorithmId));
        let preSelectedAlgorithmEnv = $('#algorithm_env_'+cleanedjQueryString(algorithmId));
        let algorithmInfoLines = $('.algorithm_info');

        // add selected class on agent and env rows on load
        agentInfoLines.hide();
        selectedAgent.addClass("agent-selected");
        preSelectedAgentEnv.addClass('agent-env-selected');

        algorithmInfoLines.hide();
        selectedAlgorithm.addClass("algorithm-selected");
        preSelectedAlgorithmEnv.addClass('algorithm-env-selected');


        // get parameters of the selected agent when page loads
        $('input[name="environment_id[]"]:checked').each(function() {
            selectedInstanceArr[i++] = {
                'instanceId': this.value,
                'instanceParam': $('#instance-'+cleanedjQueryString(agentId)+'-'+cleanedjQueryString(this.value)).val(),
            }
        });

        getAgentParameters(agentId, agents, pipelineId, selectedInstanceArr);

        preSelectedAgentEnv.show();

        $("input[name='agent_id']").click(function(){
            agentId = $(this).attr('id');
            let selectedAgent = $('#agent_env_'+cleanedjQueryString(agentId));
            // removing and adding selected class on agent and env rows on click
            pipelineFormRow.removeClass('agent-selected');
            $(this).closest("tbody tr").addClass("agent-selected");
            selectedAgent.addClass('agent-env-selected');

            //unselect any checkbox
            $('input[name="environment_id[]"]:checked').prop('checked',false).uniform('refresh');
            // get parameters of the selected agent

            getAgentParameters(agentId, agents);
            agentInfoLines.hide();
            selectedAgent.slideDown();
        });

        $('input[name="environment_id[]"]').click(function(){
            selectedInstanceArr=[];
            i = 0;
            $('input[name="environment_id[]"]:checked').each(function() {
                selectedInstanceArr[i++] = {
                    'instanceId': this.value,
                    'instanceParam': $('#instance-'+cleanedjQueryString(agentId)+'-'+cleanedjQueryString(this.value)).val(),
                }
            });

            getAgentParameters(agentId, agents, pipelineId, selectedInstanceArr);

        });

        // get parameters of the selected algorithm when page loads
        let j = 0;
        $('input[name="algorithm_environment_id[]"]:checked').each(function() {
            algoSelectedInstanceArr[j++] = {
                'algoInstanceId': this.value,
                'algoInstanceParam': $('#instance-'+cleanedjQueryString(algorithmId)+'-'+cleanedjQueryString(this.value)).val(),
            }
        });

        getAlgorithmParameters(algorithmId, algorithms, pipelineId, algoSelectedInstanceArr);

        preSelectedAlgorithmEnv.show();

        $("input[name='algorithm_id']").click(function(){
            algorithmId = $(this).attr('id');

            let algorithmIdCleaned = cleanedjQueryString(algorithmId);
            let selectedAlgorithm = $("#"+"algorithm_env_"+algorithmIdCleaned);
            // removing and adding selected class on agent and env rows on click
            algoPipelineFormRow.removeClass('algorithm-selected');
            $(this).closest("tbody tr").addClass("algorithm-selected");
            selectedAlgorithm.addClass('algorithm-env-selected');
            //unselect any checkbox
            $('input[name="algorithm_environment_id[]"]:checked').prop('checked',false).uniform('refresh');
            // get parameters of the selected agent

            getAlgorithmParameters(algorithmId, algorithms);
            algorithmInfoLines.hide();
            selectedAlgorithm.slideDown();
        });

        $('input[name="algorithm_environment_id[]"]').click(function(){
            algoSelectedInstanceArr=[];
            j = 0;
            $('input[name="algorithm_environment_id[]"]:checked').each(function() {

                algoSelectedInstanceArr[j++] = {
                    'algoInstanceId': this.value,
                    'algoInstanceParam': $('#instance-'+cleanedjQueryString(algorithmId)+'-'+cleanedjQueryString(this.value)).val(),
                }
            });

            getAlgorithmParameters(algorithmId, algorithms, pipelineId, algoSelectedInstanceArr);
        });

    });

    /**
     * list parameters of agent when agent get selected on pipeline add/edit page
     *
     * @param $this
     * @param $agents
     * @param $pipelineId
     * @param $selectedInstanceArr
     */
    function getAgentParameters($this, $agents, $pipelineId, $selectedInstanceArr){
        $('.spinloading').html('<i class="fa fa-spinner fa-spin fa-3x fa-fw mx-auto"></i><span class="sr-only">Loading...</span>');

        $.ajax({
            type: "POST",
            url: '{!! route('security-pipeline.agent-parameter') !!}',
            data: {
                'agentId' : $this,
                'agents': $agents,
                'pipelineId': $pipelineId,
                'selectedInstanceArr': $selectedInstanceArr
            },
            success: function (data) {
                $('.spinloading').html('');
                if(data.status === 'success') {
                    $('#agentParameter').html(data.html);
                }
            }
        });
    }

    /**
     *
     * @param $this
     * @param $algorithms
     * @param $pipelineId
     * @param $algoSelectedInstanceArr
     */
    function getAlgorithmParameters($this, $algorithms, $pipelineId, $algoSelectedInstanceArr){
        $.ajax({
            type: "POST",
            url: '{!! route('security-pipeline.algorithm-parameter') !!}',
            data: {
                'algorithmId' : $this,
                'algorithms': $algorithms,
                'pipelineId': $pipelineId,
                'algoSelectedInstanceArr': $algoSelectedInstanceArr
            },
            success: function (data) {
                if(data.status === 'success') {
                    $('#algorithmParameter').html(data.html);
                }
            }
        });
    }

    function cleanedjQueryString(string){
        if(typeof string !== 'undefined'){
            return string.replaceAll(/[!"#$%&'()*+,\/:;<=>?@[\\\]^`{|}~]/g, "\\$&");
        }else{
            return string;
        }
    }

</script>