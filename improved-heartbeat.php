<?php
/*
	Plugin Name: Improved Heartbeat
	Description: A play project to have more support in WordPress
	Author: Marko Heijnen
	Version: 0.1
	Author URI: http://www.markoheijnen.com
*/

include 'inc/dispatcher.php';
include 'inc/session-manager.php';

class Improved_Heartbeat {
	private $actions;
	private $dispatcher;

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_features' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts_styles' ), 1 );

		$this->dispatcher = new Improved_Heartbeat_Dispatcher;
	}

	public function load_features() {
		$features = array();

		$dir = dirname( __FILE__ ) . '/features/';

		$_features = apply_filters( 'improved_heartbeat_features', array( 'honk', 'yo' ) );

		foreach ( $_features as $feature ) {
			if ( file_exists( $dir . $feature . '.php' ) ) {
				include $dir . $feature . '.php';
			}
		}
	}

	public function register_scripts_styles() {
		wp_register_script( 'humane', plugins_url( 'assets/humane.min.js', __FILE__ ), array(), '3.2.0' );

		wp_register_style( 'humane-theme', plugins_url( 'assets/bigbox.css', __FILE__ ), array(), '3.2.0' );
	}

}

$_GLOBALS['improved_heartbeat'] = new Improved_Heartbeat;