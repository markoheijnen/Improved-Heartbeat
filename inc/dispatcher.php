<?php

class Improved_Heartbeat_Dispatcher {
	const option = 'heartbeat_actions';
	private static $running = false;

	private $actions;

	public function __construct() {
		add_filter( 'heartbeat_send', array( $this, 'run' ), 10, 2 );
	}

	public static function get( $key ) {
		$actions = get_option( self::option, array() );

		if ( isset( $actions[ $key ] ) ) {
			return $actions[ $key ];
		}

		return false;
	}

	public static function add( $key, $value, $overwrite = false ) {
		$action = false;

		if ( ! $overwrite ) {
			$action = self::get( $key );
		}

		if ( ! $action ) {
			$actions = get_option( self::option, array() );

			$actions[ $key ] = $value;

			update_option( self::option, $actions );
		}
	}


	public function run( $response, $screen_id ) {
		if ( ! self::$running ) {
			self::$running = true;

			$actions = get_option( self::option, array() );

			$response['screen'] = $screen_id;

			foreach ( $actions as $key => $value ) {
				$response[ $key ] = $value;
			}

			update_option( self::option, array() );

			self::$running = false;
		}

		return $response;
	}

}