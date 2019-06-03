<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WM Admin
 *
 * @class       WM_Admin
 * @version     1.0.0
 * @package     WM/Classes
 * @category    Class
 */
class WM_Admin {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'wm_scripts' ) );
		add_action( 'widgets_init', array( &$this, 'register_widgets' ) );
	}

	public function register_widgets() {
		register_widget( WM()->wm_widget()->get_name_of_class() );
	}

	public function wm_scripts() {
		wp_enqueue_style( 'wm-style', WM_ASSETS . '/css/wm-google-review.css', '', WM_VERSION, 'all' );
		wp_enqueue_script( 'wm-functions', WM_ASSETS . '/js/wm-functions.js', array('jquery'), WM_VERSION, true );
	}
}