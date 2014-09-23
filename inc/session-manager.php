<?php

class Improved_Heartbeat_WP_Session_Token_Manager {

	public static function add_hooks() {
		add_filter( 'attach_session_information', array( __CLASS__, 'attach_session_information' ) );
	}


	public function attach_session_information( $session_data ) {
		$session_data[ Improved_Heartbeat_Dispatcher::option ] = array(
			'all' => array(),
		);

		return $session_data;
	}


	public static function get_active_users() {
		$args = array(
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key' => 'session_tokens',
				)
			)
		);

		return get_users( $args );
	}


	public static function get_actions() {
		$token = wp_get_session_token();

		if ( $token ) {
			$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
			$session = $manager->get( $token );
			
			if ( isset( $session[ Improved_Heartbeat_Dispatcher::option ] ) ) {
				if ( ! isset( $session[ Improved_Heartbeat_Dispatcher::option ]['all'] ) ) {
					$session[ Improved_Heartbeat_Dispatcher::option ]['all'] = array();
				}

				return $session[ Improved_Heartbeat_Dispatcher::option ];
			}
		}

		return array();
	}

	public static function add_action_to_all_users( $key, $value, $screen_id, $overwrite = false ) {
		$users    = self::get_active_users();
		$sessions = array();

		foreach ( $users as $user_id ) {
			// Our little hack to make things work
			$sessions = get_user_meta( $user_id, 'session_tokens', true );

			foreach ( $sessions as $token => $session ) {
				$actions = $session[ Improved_Heartbeat_Dispatcher::option ];

				if ( ! $overwrite && isset( $actions[ $screen_id ], $actions[ $screen_id ][ $key ] ) ) {
					continue;
				}

				$actions[ $screen_id ][ $key ] = $value;

				$sessions[ $token ][ Improved_Heartbeat_Dispatcher::option ] = $actions;
			}

			// Our little hack to make things work
			update_user_meta( $user_id, 'session_tokens', $sessions );
		}
	}

	public static function update_actions( $value ) {
		$token = wp_get_session_token();

		if ( $token ) {
			$manager = WP_Session_Tokens::get_instance( get_current_user_id() );
			$session = $manager->get( $token );

			// Add data to the session
			$session[ Improved_Heartbeat_Dispatcher::option ] = $value;

			$manager->update( $token, $session );
		}
	}

}

Improved_Heartbeat_WP_Session_Token_Manager::add_hooks();
