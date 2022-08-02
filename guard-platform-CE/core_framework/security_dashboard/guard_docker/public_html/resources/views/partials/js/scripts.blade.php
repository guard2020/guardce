<script type="text/javascript">
    $(function(){

        ntopNav();

        $('#ntop').off('click').on('click', function() {

            if($('#ntop').hasClass('active')){
                $('#ntop').removeClass('active');
                ntopNav();
            }else{
                $('#ntop').addClass('active');
                ntopNav();
            }
        });


    });

    function ntopNav(){
        if($('#ntop').hasClass('active')){
            $('#sub-group').addClass('d-block');
            $('#sub-group').removeClass('d-none');
            $('.nav-item-submenu').addClass('nav-item-open');
        }else{
            $('#sub-group').addClass('d-none');
            $('#sub-group').removeClass('d-block');
            $('.nav-item-submenu').removeClass('nav-item-open');
        }
    }


</script>