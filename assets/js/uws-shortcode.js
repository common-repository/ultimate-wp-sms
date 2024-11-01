jQuery(document).ready(function(){
    if(jQuery('#uws_confirm_code').length>0){
        jQuery('#uws_confirm_code').slideUp();
    }
    jQuery("select.uws_class").each(function(){
        let id = jQuery(this).attr('id');
        let args = {
            select: '#'+id,
            settings: {
                showSearch: true
            }
        };
        new SlimSelect(args)
    });
    jQuery(document).on("submit","#uws-form-id",function(e){
        e.preventDefault();
        let $form = jQuery(this);
        let btn_text = $form.find("#uws-button").text();
        $form.find("#uws-button").prop('disabled',true);
        $form.find("#uws-button").text('Processing...');
        submit_form($form,btn_text);
    });
    jQuery(document).on("click",".uws_open_popup",function(e){
        e.preventDefault();
        let $modalid = jQuery(this).data('modal-id');
        jQuery('#'+$modalid).show();
    });
    jQuery(document).on("click",".uws_close_popup",function(e){
        jQuery(this).parents(".uws-modal").hide();
    });
});
const submit_form = async ($form,btn_text) => {
    let formData = new FormData($form[0]);
    formData.append( 'action', 'uws_process_shortcode' );
    formData.append( '_security', uws_sc.securty_check );
    await fetch(uws_sc.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
        $form.find("#uws-button").text(btn_text);
        let notifi_type = 3;
        if(data.success){
            switch(formData.get("uws_form_id")){
                case "invite":
                case "confirmation":
                    after_invite_response(data,$form);
                break;
                case "contact":
                    $form.parents(".uws-modal,.uws-body").remove();
                break;
                case "opt_out":
                    after_opt_out_resposne(data,$form);
                break;
                default:
                    $form.parents(".uws-body").remove();
                break;
            }
            notifi_type = 1;
        }
        notie.alert({ type: notifi_type, text: data.msg, time: 10, position:"bottom" })
        $form.find("#uws-button").prop('disabled',false);
        
    })
    .catch(error => {
        $form.find("#uws-button").prop('disabled',false);
        $form.find("#uws-button").text(btn_text);
        console.log(error);
        notie.alert({ type: 2, text: "Something went wrong. Please refresh and try again.", time: 10, position:"bottom" })
        // handle the error
    });
}
const after_invite_response = async (data,$form) => {
    if(data.redirect){
        // if(data.redirect!=""){
        //     window.location = data.redirect;
        // } else {
            $form.parents(".uws-body").remove();
        // }
    } else {
        jQuery('#uws_confirm_code').slideDown();
        jQuery("#uws-button").text(data.btn_text);
        jQuery("input[name='uws_form_id']").val("confirmation");
        jQuery("input[name='uws-verified-number']").val(data.uws_verified_number);
        jQuery("input[name='uws-confirm-groupid']").val(data.uws_group_id);
    }
};
const after_opt_out_resposne = async (data) => {
    jQuery('#uws_confirm_code').slideDown();
    jQuery("#uws-button").text(data.btn_text);
    jQuery("input[name='uws_form_id']").val("opt_out_confirmation");
    jQuery("input[name='uws-verified-number']").val(data.uws_verified_number);
    jQuery("input[name='uws-confirm-groupid']").val(data.uws_group_id);
};