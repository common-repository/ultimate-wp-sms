var group_select=null,old_modal_html;
jQuery(document).ready(function(){
  setup_hash_tab();
  jQuery("#header_toggle").click(function() {
    jQuery('.cs_app_main').toggleClass("open_sidebar");
    jQuery('.menu_toogle').toggleClass("on");
  });
  pagination_slim();
  jQuery(document).on('click','.custom_dropdown>button',function(e){
    e.preventDefault();
    jQuery(this).parent().toggleClass('active');
  });
  jQuery(document).on('click','.tablinks',function(){
    let $this = jQuery(this);
    let $content_id = $this.data('tab');
    if($content_id == undefined){
      return;
    }
    jQuery('.tablinks,.tabcontent').removeClass('active');
    $this.addClass('active');
    window.location.hash = $content_id;
    jQuery('#'+$content_id).addClass('active');
    wp.hooks.doAction( 'opne_tab', $content_id,$this );
  });
  jQuery(document).on('click','.close-modal',function(){
    let type = jQuery(this).data('type');
    hide_modal(type);
  });
  
  jQuery(document).on('click','.delete-modal', async function(e){
    e.preventDefault();
    //preloader.addClass('loading').fadeIn('slow');
    let $this = jQuery(this); 
    get_alert_form($this);
  });
  jQuery(document).on('click','.remove_modal',function(e){
    e.preventDefault();
    remove_modal();
  });
  jQuery(document).on('submit','#confirmation_alert',function(e){
    e.preventDefault();
    procced_confirmation(jQuery(this));
  });
  jQuery(document).on('click','.modal-show',async function(e){
    e.preventDefault();
    jQuery(this).addClass('uws-loading');
    let $this = jQuery(this); 
    let $type = $this.data('type');
    let $id = $this.data('id');
    show_modal();
    var formData = new FormData();
    formData.append( 'action', 'show_modal' );
    formData.append( 'type', $type );
    if($id){
      formData.append( 'id', $id );
    }
    formData.append( '_security', uws_script.securty_check );
    const response = await fetch(uws_script.ajax,{
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then((data) => {
      jQuery(this).removeClass('uws-loading');
      if(data.html){
          old_modal_html = jQuery('#uws_modal').html();
          jQuery('#uws_modal').html(data.html);
          pagination_slim();
          wp.hooks.doAction( 'modal_open', $type,$this,$id );
          
      } 
      if(data.error){
        notie.alert({ type: 3, text: data.error, time: 5 });
      }
        
    })
    .catch(error => {
      jQuery(this).removeClass('uws-loading');
      notie.alert({ type: 3, text: 'something went Wrong', time: 5 });
    });
  });
});

const setup_hash_tab = () => {
  let hash = window.location.hash;
  if(hash != '' && jQuery('.tablinks').length>0){
    let content_id = hash.replace('#','');
    jQuery('.tablinks,.tabcontent').removeClass('active');
    jQuery("button[data-tab="+content_id+"]").addClass('active');
    jQuery('#'+content_id).addClass('active');
    wp.hooks.doAction( 'opne_tab', content_id,jQuery("button[data-tab="+content_id+"]") );
  }
};
const pagination_slim = () => {
  new SlimSelect({
    select: '#uws-per-page',
    settings: {
      showSearch: false,
    },
    events: {
      afterChange: (newVal) => {
        wp.hooks.doAction( 'change_per_page');
      }
    }
  });
};
const show_modal = () => {
  jQuery("body").addClass("uws_overflow");
  jQuery("#uws_modal").addClass("sidebar_active");
  jQuery("#uws_modal_backdrop").addClass("active");
};
const hide_modal = (modal_type) => {
  modal_type = modal_type??"";
  jQuery("body").removeClass("uws_overflow");
  jQuery("#uws_modal").removeClass("sidebar_active");
  jQuery("#uws_modal_backdrop").removeClass("active");
  wp.hooks.doAction( 'modal_close',modal_type );
  jQuery('#uws_modal').html(old_modal_html);
};
const remove_modal = () => {
  jQuery('.modal').remove();
}
const get_alert_form = async ($this) => {
  //jQuery('#preloader').addClass('loading').fadeIn('slow');
    let formData = new FormData();
    jQuery.each($this.data(),function(i,v){
      formData.append( i, v );
    })
    formData.append( 'action', 'show_alert_form' );
    formData.append( '_security', uws_script.securty_check );
    await fetch(uws_script.ajax,{
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
        //jQuery('#preloader').addClass('loading').fadeOut('slow');
        if(data.success){
            //jQuery('#group_manager')[0].reset();
            jQuery(data.html).insertAfter(".cs_app_main");
        } else {
          notie.alert({ type: 3, text: data.msg, time: 5 })
        }
        
    })
    .catch(error => {
        //jQuery('#preloader').addClass('loading').fadeOut('slow');
        console.log(error);
        notie.alert({ type: 3, text: 'Something Went Wrong!!', time: 5 })
        // handle the error
    });
}
const procced_confirmation = async ($this) => {
  //jQuery('#preloader').addClass('loading').fadeIn('slow');
  let formData = new FormData($this[0]);
  await fetch(uws_script.ajax,{
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then((data) => {
      //jQuery('#preloader').addClass('loading').fadeOut('slow');
      let notifi_type = 3;
      if(data.success){
        notifi_type = 1;
        wp.hooks.doAction( 'after_procced_confirmation', formData.get("action") );
      }
      remove_modal();
      notie.alert({ type: notifi_type, text: data.msg, time: 5 });
  })
  .catch(error => {
      //jQuery('#preloader').addClass('loading').fadeOut('slow');
      notie.alert({ type: 3, text: "Something Went Wrong!!!", time: 5 });
      // handle the error
});
  
};