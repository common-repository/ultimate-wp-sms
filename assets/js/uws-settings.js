jQuery(document).ready(function(){
    jQuery(document).on('submit','.setting_from',function(e){
        e.preventDefault();
        save_settings(jQuery(this));
    });
    jQuery('select').each(function(){
        var id = jQuery(this).attr('id');
        new SlimSelect({
            select: '#'+id,
            settings: {
                showSearch: true,
            },
        });
    });
});
const save_settings = async ($form) => {
    jQuery('#preloader').addClass('loading').fadeIn('slow');
    let formData = new FormData($form[0]);
    formData.append( 'action', 'uws_save_setting' );
    formData.append( '_security', uws_settings.securty_check );
    await fetch(uws_settings.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(async (data) => {
        jQuery('#preloader').addClass('loading').fadeOut('slow');
        let notifi_type = 3;
        if(data.success){
            let formData = new FormData();
            formData.append( 'action', 'uws_after_save_setting' );
            formData.append( '_security', uws_settings.securty_check );
            await fetch(uws_settings.ajax,{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then((data_html) => {
                notifi_type = 1;
                jQuery('.left_wraper .body_content_sec').html(data_html.html);
                jQuery('select').each(function(){
                    var id = jQuery(this).attr('id');
                    new SlimSelect({
                        select: '#'+id,
                        settings: {
                            showSearch: true,
                        },
                    });
                });
                setup_hash_tab();
            })
            .catch(error => {
                jQuery('#preloader').addClass('loading').fadeOut('slow');
                console.log(error);
                notie.alert({ type: 2, text: "Something went wrong. Please refresh and try again.", time: 2 })
                // handle the error
            });
        } else {
            //jQuery('.uws_message').html(data.msg);
        } 
        notie.alert({ type: notifi_type, text: data.msg, time: 2 })
    })
    .catch(error => {
        jQuery('#preloader').addClass('loading').fadeOut('slow');
        console.log(error);
        notie.alert({ type: 2, text: "Something went wrong. Please refresh and try again.", time: 2 })
        // handle the error
    });
}