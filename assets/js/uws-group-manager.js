let exicuting = true;
jQuery(document).ready(function(){
    //const controller = new AbortController();
    load_group_list();
    
    jQuery(document).on('click','.member_extra_details',function(){
        jQuery(this).toggleClass('active');
        jQuery(this).parents('tr').next('tr.collpased_row').toggleClass('collapsed');
    });
    
    wp.hooks.addAction( 'modal_open', 'message_history_modal',function($type,$this,$id){
        console.log('here');
        if($type == "MemberList" && $id){
            load_group_members();
            new SlimSelect({
                select: '#uws-per-page-member',
                settings: {
                  showSearch: false,
                },
                events: {
                    afterChange: (newVal) => {
                        let html = jQuery('#uws-member-list .uws-placeholder').clone();
                        jQuery('#uws-member-list').html(html);
                        load_group_members();
                    }
                }
            });
            jQuery(".sidebar_body_content").scroll(function() {
                // End of the document reached?
                console.log(jQuery('.sidebar_body_content').height(),jQuery('.sidebar_body_content').scrollTop())
                if (jQuery('.sidebar_body_content').height()-250 < jQuery('.sidebar_body_content').scrollTop() && !exicuting) {
                  //  console.log('scroll');
                    if(jQuery('#uws-member-list input[name="page"]').val()!='done' && jQuery('#uws-member-list>tr').length>0){
                        exicuting = true;
                        let html = jQuery('#uws-member-list .uws-placeholder').clone();
                        jQuery('#uws-member-list .uws-placeholder').remove();
                        jQuery('#uws-member-list').append(html);
                        load_group_members();
        
                    }
                }
            });
        }
    });
    wp.hooks.addAction( 'modal_close', 'message_history_modal',function(modal_type){
        if(jQuery.inArray( modal_type,['group-manage','member-list'] )>=0){
            let html = jQuery('#main_group .uws-placeholder').clone();
            jQuery('#main_group').html(html);
            load_group_list();
        }
    });

    wp.hooks.addAction( 'after_procced_confirmation', 'after_group_delete',function($action){
        if(jQuery.inArray( $action, ['RemoveMemberGroup'] )>=0){
            let html = jQuery('#uws-member-list .uws-placeholder').clone();
            jQuery('#uws-member-list').html(html);
            load_group_members();
        }
    });

    jQuery(document).on('change','#select_all_member',function(){
        jQuery('input[name="member_id[]"]').prop('checked',false);
        if(jQuery(this).is(':checked')){
            jQuery('input[name="member_id[]"]').prop('checked',true);
        }
    });
    jQuery(document).on('change','input[name="member_id[]"]',function(){
        if(jQuery(this).is(':checked') && jQuery('input[name="member_id[]"]').length == jQuery('input[name="member_id[]"]:checked').length){
            jQuery('input[name="member_id[]"]').prop('checked',true);
        } else {
            jQuery('#select_all_member').prop('checked',false);
        }
    });
    
    jQuery(document).on('click','.bulk_member_action',function(){
        let selected_member = [];
        jQuery('input[name="member_id[]"]:checked').each(function(){
            selected_member.push(jQuery(this).val());  
        });
        if(selected_member.length>0){
            jQuery(this).data('member_ids',selected_member.toString());
            get_alert_form(jQuery(this));
        } else {
            notie.alert({ type: 2, text: 'Please select members', time: 5 });
        }
    })

    jQuery(document).on('click','#addnewgroup',function(){
        add_edit_group();
    });
    
    jQuery(document).on('submit','#add-new-member',function(e){
        e.preventDefault();
        jQuery('#add_new').text('Processing...');
        jQuery('#add-new-member .add_btn').prop('disabled',true);
        add_group_member('add-new-member');
    });
    jQuery(document).on('change','#uws-gm-member-filter input[name="search"]',function(){
        let html = jQuery('#uws-member-list .uws-placeholder').clone();
        jQuery('#uws-member-list').html(html);
        load_group_members();
    });
    
    jQuery(document).on('click',".edit_member",function(e){
        e.preventDefault();
        jQuery(this).parents('.custom_dropdown').toggleClass('active');
        get_member_edit_form(jQuery(this));
    });
    jQuery(document).on('submit',"#edit-group-member",function(e){
        e.preventDefault();
        jQuery('#edit_member').text('Processing...');
        jQuery('#edit-group-member .btn.primary_btn').prop('disabled',true);
        add_group_member('edit-group-member');
    });
    jQuery(document).on('click','.member_action',function(e){
        e.preventDefault();
        jQuery(this).parents('.custom_dropdown').toggleClass('active');
        get_alert_form(jQuery(this));
    });
    
});

const load_group_list = async () => {
    
    let formData = new FormData();
    jQuery('#main_group .uws-placeholder').fadeIn('slow');
    formData.append( 'action', 'get_group_cards' );
    formData.append( '_security', uws_gm.securty_check );
    
    await fetch(uws_gm.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
        jQuery('#main_group .uws-placeholder').fadeOut('slow');
        if(data.html){
            jQuery('#main_group input[name="page"]').remove();
            jQuery('#main_group').append(data.html);
        }
        if(data.error){
            notie.alert({ type: 3, text: data.error, time: 5 });
        }
    })
    .catch(error => {
        jQuery('#main_group .uws-placeholder').fadeOut('slow');
        notie.alert({ type: 2, text: 'something went Wrong!!', time: 5 });
        // handle the error
    });
};
const load_group_members = async () =>{
    jQuery('#uws-member-list .uws-placeholder').fadeIn('slow');
    let formData = new FormData(jQuery('#uws-gm-member-filter')[0]);
    let page = 1;
    if(jQuery('#uws-member-list input[name="page"]').length>0){
        page = jQuery('#uws-member-list input[name="page"]').val();
    }
    formData.append( 'action', 'get_group_members' );
    formData.append( 'page', page );
    formData.append( '_security', uws_gm.securty_check );
    
    await fetch(uws_gm.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
        exicuting = false;
        jQuery('#uws-member-list .uws-placeholder').fadeOut('slow');
        if(data.html){
            jQuery('#uws-member-list input[name="page"]').remove();
            jQuery('#uws-member-list').append(data.html);
        }
        if(data.error){
            notie.alert({ type: 3, text: data.error, time: 5 });
        }
    })
    .catch(error => {
        exicuting = false;
        jQuery('#uws-member-list .uws-placeholder').fadeOut('slow');
        console.log(error);
        notie.alert({ type: 2, text: 'something went Wrong!!', time: 5 });
    });
};
const add_edit_group = async () => {
    jQuery('#save_group').text('Processing....');
    jQuery('#addnewgroup').prop('disabled',true);
    let formData = new FormData(jQuery('#group_manager')[0]);
    formData.append( 'action', 'add_edit_group' );
    formData.append( '_security', uws_gm.securty_check );
    
    await fetch(uws_gm.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
        let notifi_type = 3;
        jQuery('#save_group').text('Save Group');
        jQuery('#addnewgroup').prop('disabled',false);
        if(data.success){
            notifi_type = 1;
            if(data.reset){
                hide_modal('group-manage');
            }
        } 
        notie.alert({ type: notifi_type, text: data.msg, time: 5 });
    })
    .catch(error => {
        jQuery('#save_group').text('Save Group');
        jQuery('#addnewgroup').prop('disabled',false);
        notie.alert({ type: 2, text: 'something went Wrong!!', time: 5 });
    });
};

const add_group_member = async (form_id) => {
    let formData = new FormData(jQuery('#'+form_id)[0]);
    formData.append( 'action', 'add_edit_group_member' );
    formData.append( '_security', uws_gm.securty_check );
    await fetch(uws_gm.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
        let notifi_type = 3;
        if(form_id == 'add-new-member'){
            jQuery('#add_new').text('Add New');
            jQuery('#add-new-member .btn.add_btn').prop('disabled',false);
        } else {
            jQuery('#edit_member').text('Save Changes');
            jQuery('#edit-group-member .btn.primary_btn').prop('disabled',false);
        }
        if(data.success){
            notifi_type = 1;
            let html = jQuery('#uws-member-list .uws-placeholder').clone();
            jQuery('#uws-member-list').html(html);
            load_group_members();
            jQuery('#'+form_id)[0].reset();
            remove_modal();
        } else {
            
        }
        notie.alert({ type: notifi_type, text: data.msg, time: 5 })
        
    })
    .catch(error => {
        if(form_id == 'add-new-member'){
            jQuery('#add_new').text('Add New');
            jQuery('#add-new-member .btn.add_btn').prop('disabled',false);
        } else {
            jQuery('#edit_member').text('Save Changes');
            jQuery('#edit-group-member .btn.primary_btn').prop('disabled',false);
        }
        notie.alert({ type: 2, text: 'something went Wrong!!', time: 5 });
    });
};
const get_member_edit_form = async ($this) => {
    jQuery('#preloader').addClass('loading').fadeIn('slow');
    let formData = new FormData();
    formData.append( 'action', 'get_member_edit_form' );
    formData.append( 'member_id', $this.data('id') );
    formData.append( 'group_id', $this.data('grp-id') );
    formData.append( '_security', uws_gm.securty_check );
    await fetch(uws_gm.ajax,{
        method: 'POST',
        headers: {
          //accept: 'application.json',
          //'Content-Type': 'application/json'
        },
        //signal: controller,
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
        jQuery('#preloader').addClass('loading').fadeOut('slow');
        if(data.success){
            //jQuery('#group_manager')[0].reset();
            jQuery(data.html).insertAfter(".cs_app_main");
        } else {
            notie.alert({ type: 3, text: data.msg, time: 5 });
        }
        
    })
    .catch(error => {
        jQuery('#preloader').addClass('loading').fadeOut('slow');
        console.log(error);
        notie.alert({ type: 2, text: 'something went Wrong!!', time: 5 });
    });
};