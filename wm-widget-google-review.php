<?php
/*
Plugin Name: WM Widget Google Review
Plugin URI: https://www.linkedin.com/in/illia-kuzoma-04b0a8a2/
Description: WM Widget of google reviews for posts
Author: Illia Kuzoma
Version: 1.0.0
Author URI: https://www.linkedin.com/in/illia-kuzoma-04b0a8a2/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WM_Widget_Google_Review {

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Minimum PHP version required
	 *
	 * @var string
	 */
	private $min_php = '5.6.0';

	/**
	 * @var object
	 *
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Initializes the WM_Widget_Google_Review() class
	 *
	 * @since 1.0.0
	 * @since 1.0.0 Rename `__construct` function to `setup` and call it only once
	 *
	 * Checks for an existing WM_Widget_Google_Review() instance
	 * and if it doesn't find one, creates it.
	 *
	 * @return object
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WM_Widget_Google_Review ) ) {
			self::$instance = new WM_Widget_Google_Review;
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Setup the plugin
	 *
	 * Sets up all the appropriate hooks and actions within our plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 *
	 */
	private function setup() {
		// dry check on older PHP versions, if found deactivate itself with an error
		register_activation_hook( __FILE__, array( $this, 'auto_deactivate' ) );

		if ( ! $this->is_supported_php() ) {
			return;
		}

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// instantiate classes
		$this->instantiate();

		// Loaded action
		do_action( 'wm_loaded' );
	}

	/**
	 * Check if the PHP version is supported
	 *
	 * @return bool
	 */
	public function is_supported_php() {
		if ( version_compare( PHP_VERSION, $this->min_php, '<' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Bail out if the php version is lower than
	 *
	 * @return void
	 */
	public function auto_deactivate() {
		if ( $this->is_supported_php() ) {
			return;
		}

		deactivate_plugins( basename( __FILE__ ) );

		$error = __( '<h1>An Error Occured</h1>', 'wm' );
		$error .= __( '<h2>Your installed PHP Version is: ', 'wm' ) . PHP_VERSION . '</h2>';
		$error .= __( '<p>The <strong>WM Widget Google Reviews</strong> plugin requires PHP version <strong>', 'wm' ) . $this->min_php . __( '</strong> or greater', 'wm' );
		$error .= __( '<p>The version of your PHP is ', 'wm' ) . '<a href="http://php.net/supported-versions.php" target="_blank"><strong>' . __( 'unsupported and old', 'wm' ) . '</strong></a>.';
		$error .= __( 'You should update your PHP software or contact your host regarding this matter.</p>', 'wm' );
		wp_die( $error, __( 'Plugin Activation Error', 'wm' ), array( 'response' => 200, 'back_link' => true ) );
	}

	/**
	 * Define the plugin constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'WM_VERSION', $this->version );
		define( 'WM_FILE', __FILE__ );
		define( 'WM_PATH', dirname( WM_FILE ) );
		define( 'WM_INCLUDES', WM_PATH . '/includes' );
		define( 'WM_TEMPLATES', WM_PATH . '/templates' );
		define( 'WM_URL', plugins_url( '', WM_FILE ) );
		define( 'WM_ASSETS', WM_URL . '/assets' );
		define( 'WM_GOOGLE_AVATAR', 'https://lh3.googleusercontent.com/-8hepWJzFXpE/AAAAAAAAAAI/AAAAAAAAAAA/I80WzYfIxCQ/s50-c/114307615494839964028.jpg' );
	}

	/**
	 * Include the required files
	 *
	 * @return void
	 */
	private function includes() {
		require_once WM_INCLUDES . '/class-autoloader.php';
	}

	/**
	 * Instantiate classes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function instantiate() {
		new WM_Admin();
	}

	/**
	 * @return WM_Widget
	 * @throws Exception
	 */
	public function wm_widget() {
		return WM_Widget::init();
	}

	/**
	 * @return WM_Google_Helper
	 * @throws Exception
	 */
	public function google() {
		return WM_Google_Helper::init();
	}

}

/**
 * Init the WM_Widget_Google_Review plugin
 *
 * @return WM_Widget_Google_Review the plugin object
 */
function WM() {
	return WM_Widget_Google_Review::init();
}

// kick it off
WM();