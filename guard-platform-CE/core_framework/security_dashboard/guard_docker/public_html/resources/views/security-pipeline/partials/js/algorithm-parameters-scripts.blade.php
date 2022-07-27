<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $(".algo-expand-params").on('click', function () {
            $(this).find('.rotate-icon').toggleClass('fa-chevron-up fa-chevron-down');
            $(this).find('.rotate-icon').toggleClass('text-guard-dark text-white');
        });

        $(".algo-expand-source").on('click', function () {
            $(this).find('.rotate-icon').toggleClass('fa-chevron-up fa-chevron-down');
            $(this).find('.rotate-icon').toggleClass('text-guard-dark text-white');
        });

    });

    /**
     * This function is used the download the resource content
     *
     * @param $this
     */
    function getResourceTextDownload($this){
        let filename = $this+'.yml';
        let text = $('#content_'+$this).val();
        let element = document.createElement('a');

        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }
</script>