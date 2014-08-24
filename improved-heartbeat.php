<?php
/*
	Plugin Name: Improved Heartbeat
	Description: A play project to have more support in WordPress
	Author: Marko Heijnen
	Version: 0.1
	Author URI: http://www.markoheijnen.com
*/

class Improved_Heartbeat {
	private $actions;

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_actions' ) );
	}

	public function load_actions() {
		$actions = array();

		include 'actions/honk.php';
	}

}

$_GLOBALS['improved_heartbeat'] = new Improved_Heartbeat;