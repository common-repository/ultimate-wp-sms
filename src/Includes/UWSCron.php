<?php 
namespace UWS\LITE\SMS\Includes;
use UWS\LITE\SMS\Admin\SendSms;
class UWSCron{
    public $uws;
    private $name,
        $version,
        $file_path,
        $send_sms;
    public function __construct(string $name, string $version, string $file_path){
        global $uws;
        $this->uws = $uws;
        $this->name      = $name;
		$this->version   = $version;
		$this->file_path = $file_path;
        $this->send_sms = new SendSms();
    }
    public function uws_interval($interval){
        $interval['uws_minutes_5'] = array('interval' => 5*60, 'display' => 'UWS 5 minute interval');
        return $interval;
    }
    public function setup_crom(){
					
		wp_clear_scheduled_hook( 'uws_queue_sweeper' );
		$sweeper_timestamp = wp_next_scheduled( 'uws_queue_sweeper' );
		//If $timestamp == false schedule hasn't been done previously
		if( $sweeper_timestamp == false ){			  
			wp_schedule_event( time(), 'uws_minutes_5', 'uws_queue_sweeper');
		}
    }
    
    public function uws_run_queue_sweeper(){
        $process_args = array(
                            'uws-process-type' => "D",
                            'uws-process-maxbatchsize' => 50
                        );
        $this->send_sms->process_queue($process_args);
    }
}