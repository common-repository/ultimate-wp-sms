<?php if($args['popup']=='yes'): 
    $id = uniqid(); ?>
    <button class="uws_open_popup" data-modal-id="<?php echo $id; ?>"><?php echo esc_html($args['open_button_text']); ?></button>
    <div class="uws-modal" id="<?php echo $id; ?>">
<?php endif; ?>
<div class="uws-body">
    <?php if($args['popup']=='yes'): ?>
        <span class="uws_close_popup">&times;</span>
    <?php endif; ?>
    <div id="uws-form">
    <h3 class="uws-h3"><?php echo esc_html($args['title_text']); ?></h3>
    <form id="uws-form-id" class="uws-form-class">
        <div class="uws-form-group">
            <input type="hidden"  name="uws_form_id" value="contact">
            <input type="hidden"  name="uws-form-special" value="">
            <label for="uws-name" class="uws-label"><?php echo esc_html($args['name_text']); ?></label>
            <div class="uws-input-group">
                <input type="text" id="uws-name" name="uws-name" class="uws-form-control" required>
            </div>
        </div>
        <div class="uws-form-group">
            <label for="uws-number" class="uws-label"><?php echo esc_html($args['number_text']); ?></label>
            <div class="uws-input-group">
                <input type="text" id="uws-number" name="uws-number" class="uws-form-control" required>
            </div>
        </div>
        <div class="uws-form-group">
            <label for="uws-message" class="uws-label"><?php echo esc_html($args['message_text']); ?></label>
            <div class="uws-input-group">
                <textarea id="uws-message" name="uws-message" class="uws-form-control" rows="6" maxlength="160" required></textarea>
            </div>
        </div>
        <div class="uws-form-group">
            <button type="submit" id="uws-button" class="uws-btn uws-btn-primary uws-btn-lg uws-btn-block"><?php echo esc_html($args['send_button_text']); ?></button>
        </div>
    </form>
    </div>
</div>
<?php 
if($args['popup']=='yes'): ?>
    </div>
<?php endif; ?>