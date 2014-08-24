<?php

class Improved_Heartbeat_Dispatcher {
	const option = 'heartbeat_actions';
	private static $running = false;

	public function __construct() {
		add_filter( 'heartbeat_send', array( $this, 'run' ), 10, 2 );
	}


	public static function get( $key, $screen_id = null ) {
		if ( ! $screen_id ) {
			$screen_id = 'all';
		}

		$actions = self::get_actions();

		if ( isset( $actions[ $screen_id ], $actions[ $screen_id ][ $key ] ) ) {
			return $actions[ $screen_id ][ $key ];
		}

		return false;
	}

	public static function add( $key, $value, $screen_id = null, $overwrite = false ) {
		$action = false;

		if ( ! $screen_id ) {
			$screen_id = 'all';
		}

		if ( ! $overwrite ) {
			$action = self::get( $key, $screen_id );
		}

		if ( ! $action ) {
			$actions = self::get_actions();

			$actions[ $screen_id ][ $key ] = $value;

			return update_option( self::option, $actions );
		}

		return false;
	}


	public static function user_get( $user_id, $key, $screen_id = null ) {
		if ( ! $screen_id ) {
			$screen_id = 'all';
		}

		$actions = self::get_actions_for_user( $user_id );

		if ( isset( $actions[ $screen_id ], $actions[ $screen_id ][ $key ] ) ) {
			return $actions[ $screen_id ][ $key ];
		}

		return false;
	}

	public static function user_add( $user_id, $key, $value, $screen_id = null, $overwrite = false ) {
		$action = false;

		if ( ! $screen_id ) {
			$screen_id = 'all';
		}

		if ( ! $overwrite ) {
			$action = self::user_get( $user_id, $key, $screen_id );
		}

		if ( ! $action ) {
			$actions = self::get_actions_for_user( $user_id );

			$actions[ $screen_id ][ $key ] = $value;

			return update_user_meta( $user_id, self::option, $actions );
		}

		return false;
	}


	public function run( $response, $screen_id ) {
		if ( ! self::$running && doing_action('heartbeat_send') ) {
			self::$running = true;

			$actions      = $this->get_actions();
			$user_actions = $this->get_actions_for_user( get_current_user_id() );


			if ( $actions ) {
				$response = $this->run_actions( $response, $actions, $screen_id );

				update_option( self::option, $actions );
			}

			if ( $user_actions ) {
				$response = $this->run_actions( $response, $user_actions, $screen_id );

				update_user_meta( get_current_user_id(), self::option, $user_actions );
			}


			self::$running = false;
		}

		return $response;
	}



	private static function get_actions() {
		return get_option( self::option, array( 'all' => array() ) );
	}

	private static function get_actions_for_user( $user_id ) {
		$actions = (array) get_user_meta( $user_id, self::option, true );

		if ( ! isset( $actions['all'] ) ) {
			$actions['all'] = array();
		}

		return $actions;
	}


	private function run_actions( $response, &$actions, $screen_id ) {
		// Check for all screens
		foreach ( $actions['all'] as $key => $value ) {
			$response[ $key ] = $value;

			unset( $actions['all'][ $key ] );
		}

		// Check for specific screens
		if ( isset( $actions[ $screen_id ] ) ) {
			foreach ( $actions[ $screen_id ] as $key => $value ) {
				$response[ $key ] = $value;

				unset( $actions[ $screen_id ][ $key ] );
			}
		}

		return $response;
	}

}