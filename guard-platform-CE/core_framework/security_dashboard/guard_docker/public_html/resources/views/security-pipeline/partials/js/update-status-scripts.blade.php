<script type="text/javascript">

    let selected;
    $('.form-check-input-styled').uniform();

    // update pipeline status
    function updatePipelineStatus(status, pipelineId){

        updateStatusLabel(status, pipelineId);
        $.ajax({
            type: "POST",
            url: '{!! route('security-pipeline.status') !!}',
            data: {
                'status' : status,
                'pipelineId': pipelineId,
            },

        })
        .done(function(data){
            if(data.status === 'success') {
                let updatedStatus = data.updatedStatus;
                updateStatusLabel(updatedStatus, pipelineId);

                new PNotify({
                    title: updatedStatus.charAt(0).toUpperCase() + updatedStatus.slice(1),
                    text: 'Pipeline '+updatedStatus+' successfully',
                    addclass: 'bg-success-600 border-success-800',
                    delay: 3000,
                    closer: true,
                });

            }else if(data.status === 'failed') {
                if(data.updatedStatus === 'stop'){
                    updateStatusLabel('started', pipelineId);
                }else if(data.updatedStatus === 'start'){
                    updateStatusLabel('stopped', pipelineId);
                }
                new PNotify({
                    title: 'Start/stop',
                    text: 'Action was unsuccessful. Try again. If the issue persists, please contact support.',
                    addclass: 'bg-danger-600 border-danger-800',
                    delay: 3000,
                    closer: true,
                });
            }
            return true;
        })
        .fail(function(data){
            new PNotify({
                title: 'Start/stop',
                text: 'Action was unsuccessful. Try again. If the issue persists, please contact support.',
                addclass: 'bg-danger-600 border-danger-800',
                delay: 3000,
                closer: true,
            });
        });
    }

    function updateStatusLabel(status, pipelineId){

        let pipeLineElementId = $('#'+pipelineId);
        let actionButton = $('.pipeline-start-stop-btn');

        //Starting, Stopping
        if(status === 'start') {
            pipeLineElementId.addClass('badge-light');
            pipeLineElementId.removeClass('badge-primary');
            pipeLineElementId.text('Starting');
            //action btn
            actionButton.text('Starting');
            actionButton.attr('disabled', true);
        }else if(status === 'stop'){
            pipeLineElementId.addClass('badge-light');
            pipeLineElementId.removeClass('badge-primary');
            pipeLineElementId.text('Stopping');
            actionButton.attr('disabled', true);
        }else if(status === 'started'){
            if(pipeLineElementId.hasClass('badge-danger')){
                pipeLineElementId.removeClass('badge-danger');
            }
            if(pipeLineElementId.hasClass('badge-light')){
                pipeLineElementId.removeClass('badge-light');
            }
            pipeLineElementId.addClass('badge-primary');
            pipeLineElementId.text('Started');
            //action btn
            actionButton.attr('disabled', false);
            actionButton.text('Stop');
            if(actionButton.hasClass('btn-primary')) {
                actionButton.removeClass('btn-primary');
            }
            actionButton.addClass('btn-danger');
        }else if(status === 'stopped'){
            if(pipeLineElementId.hasClass('badge-primary')){
                pipeLineElementId.removeClass('badge-primary');
            }
            if(pipeLineElementId.hasClass('badge-light')){
                pipeLineElementId.removeClass('badge-light');
            }
            pipeLineElementId.addClass('badge-danger');
            pipeLineElementId.text('Stopped');
            //action btn
            actionButton.attr('disabled', false);
            actionButton.text('Start');
            if(actionButton.hasClass('btn-danger')) {
                actionButton.removeClass('btn-danger');
            }
            actionButton.addClass('btn-primary');
        }
    }

</script>