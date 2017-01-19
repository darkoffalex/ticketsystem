$(document).ready(function () {

    /********************************** M O D A L  M A N A G E M E N T  W I N D O W ***********************************/

    /**
     * Overriding submit action for form (to send via ajax) and reload table if returned OK
     */
    $(document).on('click','[data-ajax-form]',function(){

        var form = $($(this).data('ajax-form'));
        var formData = new FormData(form[0]);
        var okReload = $($(this).data('ok-reload'));

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            async: true,
            beforeSend: function(){
                $.LoadingOverlay("show");
            },
            success: function (data) {
                if(data != 'OK'){
                    $('.modal-content').html(data);
                }else{
                    $.ajax({
                        url: okReload.data('reload-url'),
                        type: 'GET',
                        async: false,
                        success: function(reloaded_data){
                            okReload.html(reloaded_data);
                            $('.modal').modal('hide');
                        }
                    });
                }
                $.LoadingOverlay("hide");
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    });

    /**
     * Reloading links (updates container's html via ajax)
     */
    $(document).on('click','[data-ajax-reload]', function(){

        var confirmMsg = $(this).data('confirm-ajax');

        if(confirmMsg){
            if(!confirm(confirmMsg)){
                return false;
            }
        }

        var container = $($(this).data('ajax-reload'));
        var link = $(this);

        $.ajax({
            url: link.attr('href'),
            type: 'GET',
            async: true,
            beforeSend: function(){
                $.LoadingOverlay("show");
            },
            success: function(reloaded_data){
                container.html(reloaded_data);
                $.LoadingOverlay("hide");
            }
        });

        return false;
    });
});

