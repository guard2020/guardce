<script type="text/javascript">
    $(function(){
        $('#dataModal').on('show.bs.modal', function (event) {
            let notificationData = JSON.parse(decodeURIComponent($('.data-modal').attr('data-notification')));
            let modal = $(this);

            modal.find('.modal-body').append()
        })
    });
</script>