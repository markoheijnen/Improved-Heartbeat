<?php

class Improved_Heartbeat_WP_Session_Token_Manager {

	public function __construct() {
		
	}

	public function get_active_users() {
		$args = array(
			'meta_query' => array(
				array(
					'key' => 'session_tokens'
				)
			)
		);

		return get_users( $args );
	}

	public function get_all_sessions() {
		$users    = $this->get_active_users();
		$sessions = array();

		foreach ( $users as $user ) {
			//$sessions
		}
	}

}