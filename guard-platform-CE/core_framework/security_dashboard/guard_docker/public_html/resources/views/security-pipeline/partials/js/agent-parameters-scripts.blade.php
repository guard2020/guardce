<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $(".expand-params").on('click', function () {
            $(this).find('.rotate-icon').toggleClass('fa-chevron-up fa-chevron-down');
            $(this).find('.rotate-icon').toggleClass('text-guard-dark text-white');
        });

        $(".expand-source").on('click', function () {
            $(this).find('.rotate-icon').toggleClass('fa-chevron-up fa-chevron-down');
            $(this).find('.rotate-icon').toggleClass('text-guard-dark text-white');
        });

        $(".reloadButton").on('click', function(e) {

            e.stopPropagation();
            let Agentinstance = $(this).attr("id");
            let cleanedAgentInstance = cleanedjQueryString(Agentinstance);
            let instance = $(this).attr("data-content");

            let instanceData = JSON.parse($('#instance-'+cleanedAgentInstance).val());

            if(Array.isArray(instanceData) && instanceData.length) {
                let instanceInputId =  $('#collapse-'+instance);
                $.each(instanceData, function(key, value){
                    let inputID = "text_param_"+value['id'];
                    let inputType = instanceInputId.find('#'+inputID).attr('type');

                    if(inputType === 'checkbox') {
                        instanceInputId.find('#'+inputID).prop('checked', value['value']['old']);
                    } else {
                        instanceInputId.find('#'+inputID).val(value['value']['old']);
                    }
                });

                $('#heading-'+instance).after('<div class="col-12 bg-primary-300 text-left my-2 py-2 border border-grey reload-msg" role="tab">Instance reloaded default configuration successfully</div>')
                $('.reload-msg').delay(3000).fadeOut("slow");
            }
        });
    });

    /**
     * This function is used the download the resource content
     *
     * @param $this
     */
    function getResourceTextDownload($this){
        let filename = $this+'.yml';
        let text = $('#content_'+cleanedjQueryString($this)).val();
        let element = document.createElement('a');

        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }

    /**
     * This function cleans strings that contains special characters
     * @param string
     * @returns {*}
     */
    function cleanedjQueryString(string){
        if(typeof string !== 'undefined'){
            return string.replaceAll(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, "\\$&");
        }else{
            return string;
        }
    }

</script>