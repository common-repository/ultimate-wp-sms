<?php global $uws; ?>
<div class="uws-body">
    <div id="uws-form">
        <h3 class="uws-h3"><?php echo $args['title_text']; ?></h3>
        <form id="uws-form-id" class="uws-form-class">
            <div class="uws-form-group">
                <input type="hidden" name="uws_form_id" value="opt_out">
                <input type="hidden" name="uws-form-special" value="">
                <input type="hidden" name="uws-opt-out-next" value="<?php echo esc_attr($args['unsubscribe_button']); ?>"> 
            </div>
            <div class="uws-form-group">
                <label for="uws-optout-number" class="uws-label"><?php echo esc_html($args['enter_number_text']); ?></label>
                <div class="uws-input-group">
                    <input type="text" id="uws-optout-number" name="uws-optout-number" class="uws-form-control" required>
                </div>
            </div>
            <?php $uws->get_uws_template_part('confirmation','',$args);  ?>
            <div class="uws-form-group">
                <button type="submit" id="uws-button" class="uws-btn uws-btn-primary uws-btn-lg uws-btn-block"><?php echo esc_html($args['enter_number_button']); ?></button>
            </div> 
        </form>
    </div>
</div>