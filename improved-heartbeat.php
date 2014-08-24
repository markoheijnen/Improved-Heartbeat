<?php
/*
	Plugin Name: Improved Heartbeat
	Description: A play project to have more support in WordPress
	Author: Marko Heijnen
	Version: 0.1
	Author URI: http://www.markoheijnen.com
*/

include 'inc/dispatcher.php';

class Improved_Heartbeat {
	private $actions;
	private $dispatcher;

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_features' ) );

		$this->dispatcher = new Improved_Heartbeat_Dispatcher;
	}

	public function load_features() {
		$features = array();

		include 'features/honk.php';
	}

}

$_GLOBALS['improved_heartbeat'] = new Improved_Heartbeat;