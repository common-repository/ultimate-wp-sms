<?php global $uws,$post;
$post_id = isset($post->ID) ?  $post->ID : ""; ?>
<div class="uws-body">
    <div id="uws-form">
    <h3 class="uws-h3"><?php echo $args['grpinvdesc']; ?></h3>
    <form id="uws-form-id" class="uws-form-class">
        <div class="uws-form-group">
            <input type="hidden"  name="uws_form_id" value="invite">
            <input type="hidden"  name="uws-form-special" value="">
            <input type="hidden"  name="uws-post-id" value="<?php echo esc_attr($post_id); ?>">
        </div>
        <div class="uws-form-group">
            <label for="uws-sub-name" class="uws-label"><?php echo esc_html($args['grpinvnametxt']); ?></label>
            <div class="uws-input-group">
                <input type="text" id="uws-sub-name" name="uws-sub-name" class="uws-form-control" required>
            </div>
        </div>
        <div class="uws-form-group">
            <label for="uws-sub-number" class="uws-label"><?php echo esc_html($args['grpinvnumtxt']); ?></label>
            <div class="uws-input-group">
                <input type="text" id="uws-sub-number" name="uws-sub-number" class="uws-form-control" required>
            </div>
        </div>
        <?php 
        if($args['grpinvconfirm']=='1'):
            $uws->get_uws_template_part('confirmation','',$args); 
        endif;
        ?>
        <?php if(!empty($args['grpgdprchk']) && $args['grpgdprchk']=='yes'): ?>
            <div class="uws-credit" id="uws-grdp">
                <?php echo esc_html( $args['grpgdprtxt']); ?>
            </div>
        <?php endif; ?>
        <div class="uws-form-group">
            <button type="submit" id="uws-button" class="uws-btn uws-btn-primary uws-btn-lg uws-btn-block"><?php $args['grpinvconfirm']=='1'?_e( "Get confirmation code", "ultimate-wp-sms" ):_e( "Subscribe", "ultimate-wp-sms" ); ?></button>
        </div>
        
    </form>
    </div>
</div>