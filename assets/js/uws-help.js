var categories=null,priorities=null;
jQuery(document).ready(function(){
    view_log_file();
    wp.hooks.addAction( 'modal_open', 'support_ticket_modal',function($type,$this,$id){
        if($type == "SupportTicket"){
            setup_select();
        }
    });
    jQuery(document).on('submit','#log_file',async function(e){
        e.preventDefault();
        view_log_file();
    });
    jQuery(document).on('click','#clear-log', async function(e){
        jQuery('#log_file button').prop('disabled',true);
        jQuery(this).text('Processing..');
        let formData = new FormData(jQuery('#log_file')[0]);
        formData.append( 'action', 'clear_log_file' );
        formData.append( '_security', uws_help.securty_check );
        await fetch(uws_help.ajax,{
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then((data) => {
            jQuery('#log_file button').prop('disabled',false);
            jQuery(this).text('Clear Log');
            if(data.success){
                jQuery("#log_file_data").val(data.log);
            } else {
                notie.alert({ type: 3, text: data.msg, time: 5 })
            }
            
        })
        .catch(error => {
            jQuery('#log_file button').prop('disabled',false);
            jQuery(this).text('Clear Log');
            notie.alert({ type: 3, text: 'Something Went Wrong!!', time: 5 })
            // handle the error
        });
    })
    
});
const view_log_file = async () => {
    jQuery('#log_file button').prop('disabled',true);
    jQuery('.view-log').text('Processing..');
    let formData = new FormData(jQuery('#log_file')[0]);
    formData.append( 'action', 'get_log_file' );
    formData.append( '_security', uws_help.securty_check );
    await fetch(uws_help.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
        jQuery('#log_file button').prop('disabled',false);
        jQuery('.view-log').text('View Log');
        if(data.success){
            jQuery("#log_file_data").val(data.log);
        } else {
            notie.alert({ type: 3, text: data.msg, time: 5 })
        }
        
    })
    .catch(error => {
        jQuery('#log_file button').prop('disabled',false);
        jQuery('.view-log').text('View Log');
        notie.alert({ type: 3, text: 'Something Went Wrong!!', time: 5 })
        // handle the error
    });
}
const setup_select = () => {
    if(categories){
        categories.destroy();
    }
    categories = new SlimSelect({
        select: '#category',
        settings: {
            showSearch: false,
            placeholderText: '',
            maxValuesShown: 20, // Default 20
            contentLocation: document.getElementById('text')
        },
    });
    if(priorities){
        priorities.destroy();
    }
    priorities = new SlimSelect({
        select: '#priority',
        settings: {
            showSearch: false,
            placeholderText: '',
            maxValuesShown: 20, // Default 20
            contentLocation: document.getElementById('text')
        },
    });
}