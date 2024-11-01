var sender_select = null;
jQuery(window).load(function(){
  setTimeout(()=>{
    jQuery('.uws-placeholder').fadeOut('slow');  
  },1000) 
});
jQuery(document).ready(function(){
  jQuery(document).on('keyup','.ss-open-below .ss-search input',function(e){
    var code = e.key;
    if(code==="Enter"){
        jQuery('.ss-open-below .ss-addable').trigger('click');
    }
  });
  jQuery('#reset_form').click(function(){
    reset_form();
  });
  update_preview();
  setup_select();
    jQuery('#uws_send_message').submit(function(e){
      e.preventDefault();
      jQuery('#send_btn_text').text('Processing....');
      jQuery('#send_btn_text').prop('disabled',true);
      queue_message();
    });
    jQuery('#uws-message').keyup(function(){
      update_preview();
    });
});
const setup_select = () => {
  if(sender_select){
    sender_select.destroy();
  }
  sender_select = new SlimSelect({
    select: '#uws-reciptant-number',
    settings: {
        showSearch: false,
    }
  });
};


const update_preview = () => {
  let $text = jQuery("#uws-message").val();
  if($text.length>=160){
    $text = $text.substr(0, $text.lastIndexOf('', 160));
    jQuery("#uws-message").val($text);
  }
  let $unit = parseInt($text.length/160);
  jQuery('.phone_wraper .message_card p').text($text);
  jQuery('#uws-used-letter').text($text.length);
  jQuery('#uws-used-unit').text($unit+1);
};

const queue_message = async () => {
  jQuery('#preloader').addClass('loading').fadeIn('slow');
  let errors = validate_request();
  if(errors.length>0){
    notie.alert({ type: 3, text: errors.join(','), time: 10 });
    jQuery('#send_btn_text').prop('disabled',false);
    jQuery('#send_btn_text').text('Send Your Message');
    return false;
  }
  let formData = new FormData(jQuery('#uws_send_message')[0]);
  formData.append( 'action', 'uws_queue_message' );
  formData.append( '_security', uws_ss.securty_check );
  await fetch(uws_ss.ajax,{
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then((data) => {
    jQuery('#send_btn_text').prop('disabled',false);
    jQuery('#send_btn_text').text('Send Your Message');
    if(data.success){
        notie.alert({ type: 1, text: data.msg, time: 10 });
        reset_form();
    } else {
      notie.alert({ type: 3, text: data.msg, time: 10 });
    } 
      
  })
  .catch(error => {
      console.log(error);
      notie.alert({ type: 3, text: "Something Went Wrong.", time: 5 });
      jQuery('#send_btn_text').prop('disabled',false);
      jQuery('#send_btn_text').text('Send Your Message');
      // handle the error
  });
};
const validate_request = () => {
  var error = [];
  if(jQuery('#uws-reciptant-number').val() == "" && jQuery('#uws-reciptant-custom-number').val() == ""){
    error.push('No Recipient selected');
  }
  if(jQuery('#uws-message').val().trim() == "" && jQuery('input[name="uws-message-type"]:checked').val() == 'uws-sms'){
    error.push('Cannot send blank message');
  }
  return error;
};
const reset_form = () => {
  let message = jQuery('#uws-message').val();
  jQuery('#uws_send_message')[0].reset();
  jQuery('#uws-message').val(message);
  update_preview();
  setup_select();
};