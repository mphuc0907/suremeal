jQuery(document).ready(function(){
    jQuery('.orderid').change(function(){
        jQuery('.box-alert').css('display','block').html('Đang cập nhât...');
        var i = jQuery(this).attr('dataid');
        var c = 1;//menu
        var o = jQuery(this).val();
        jQuery.ajax({
            type:'POST',
            data:{action:'my_order', 'i': i, 'o':  o, 'c': c },
            url: ajaxurl,
            dataType: 'json',
            cache: false,
            success: function(res) {

                if(res.status==1){

                }
                jQuery('.box-alert').css('display','none')
            }
        });

    });



});