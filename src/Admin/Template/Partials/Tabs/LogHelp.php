<?php
namespace UWS\LITE\SMS\Admin\Template\Partials\Tabs;
class LogHelp{
    public function __construct($args){ 
        global $uws;
        $args['log_files'] = [];
        if(is_dir($uws->file_path. "/logs")){
            $args['log_files'] = array_diff(scandir($uws->file_path. "/logs"), array('..', '.'));
        }
        $this->html($args);
    }
    private function html($args){ ?>
        <div id="log_help" class="log_help tabcontent">
            <h2 class="common_heading">Check the log file date wise</h2>
            <form id="log_file">
                <div class="form_fields">
                    <div class="form-group">
                        <label class="form-label">Select Log file</label>
                        <p class="helper_text">Select the country you are in, so UWS can convert your number into an international format.</p>
                        <div class="input_wrap">
                            <select id="uws-log-file" name="uws-log-file">
                                <?php foreach($args['log_files'] as $log): ?>
                                    <option value="<?php echo $log; ?>"><?php echo $log; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Log File Data</label>
                        <div class="input_wrap">
                            <textarea disabled readonly id="log_file_data" rows="20"></textarea>
                        </div>
                    </div>
                </div>
                <div class="action_btn_wrapper">
                    <button class="btn primary_btn mr-10 view-log">
                        <?php esc_html_e( "View Logs","ultimate-wp-sms"); ?>
                    </button>
                    <button class="btn primary_btn mr-10" id="clear-log" type="button">
                        <?php esc_html_e( "Clear Logs","ultimate-wp-sms"); ?>
                    </button>
                </div>
            </form>
        </div>
    <?php }
}