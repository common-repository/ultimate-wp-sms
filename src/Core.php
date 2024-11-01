<?php
namespace UWS\LITE\SMS;
use PDO;
use UWS\LITE\SMS\Includes\{
	UWSActivator,
	UWS,
	UWSCron,
};
/**
 * The core class, where logic is defined.
 */
class Core {
    /**
	 * Unique identifier (slug)
	 *
	 * @var string
	 */
	public $name;
	/**
	 * Current version.
	 *
	 * @var string
	 */
	public $version;
	public $file_path;
	private $uws_cron;
	/**
	 * Setup the class variables
	 *
	 * @param string $name      Plugin name.
	 * @param string $version   Plugin version. Use semver.
	 * @param string $file_path Plugin file path
	 */
	public function __construct( string $name, string $version, string $file_path ) {
		$this->name      = $name;
		$this->version   = $version;
		$this->file_path = $file_path;
		$this->uws_cron = new UWSCron($this->name,$this->version,$this->file_path);
		//cron
		add_filter('cron_schedules', array($this->uws_cron,'uws_interval'));
		add_action('uws_queue_sweeper', array($this->uws_cron,'uws_run_queue_sweeper') );	
	}

	/**
	 * Get the identifier, also used for i18n domain.
	 * @return string The unique identifier (slug)
	 */
	public function get_name() {
		return $this->name;
	}
	/**
	 * Get the current version.
	 *
	 * @return string The current version.
	 */
	public function get_version() {
		return $this->version;
	}

	public function activation() {
		register_activation_hook( $this->file_path, [$this, 'cron_activation'] );
	}
	/**
	 * Start the logic for this plugins.
	 *
	 * Runs on 'plugins_loaded' which is pre- 'init' filter
	 */
	public function init() {
		global $uws;
		$uws = new UWS($this->name,$this->version,$this->file_path);
		$uws->init();
		$this->set_locale();
        new Admin\Main($this->name,$this->version,$this->file_path);
		new Includes\UWSAjax($this->name,$this->version,$this->file_path);
	}
	/**
	 * Activate import cron schedules
	 */
	public function cron_activation() {
		global $uws;
		$uws = new UWS($this->name,$this->version,$this->file_path);
		update_option('uws-version',$this->version);
		UWSActivator::activate();
	}
	/**
	 * Load translations
	 */
	private function set_locale() {
		$i18n = new Includes\I18n( $this->name );
		$i18n->load_plugin_textdomain( dirname( $this->file_path ) );
	}

}