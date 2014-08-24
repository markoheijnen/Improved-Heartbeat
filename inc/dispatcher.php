<?php

class Improved_Heartbeat_Dispatcher {
	const option = 'heartbeat_actions';
	private static $running = false;

	private $actions;

	public function __construct() {
		add_filter( 'heartbeat_send', array( $this, 'run' ), 10, 2 );
	}

	public static function get( $key, $screen_id = null ) {
		if ( ! $screen_id ) {
			$screen_id = 'all';
		}

		$actions = self::get_actions();

		if ( isset( $actions[ $screen_id ][ $key ] ) ) {
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

			update_option( self::option, $actions );
		}
	}


	public function run( $response, $screen_id ) {
		if ( ! self::$running ) {
			self::$running = true;

			$actions = $this->get_actions();

			foreach ( $actions['all'] as $key => $value ) {
				$response[ $key ] = $value;

				unset( $actions['all'][ $key ] );
			}

			if ( isset( $actions[ $screen_id ] ) ) {
				foreach ( $actions[ $screen_id ] as $key => $value ) {
					$response[ $key ] = $value;

					unset( $actions[ $screen_id ][ $key ] );
				}
			}

			update_option( self::option, $actions );

			self::$running = false;
		}

		return $response;
	}


	private static function get_actions() {
		return get_option( self::option, array( 'all' => array() ) );
	}
}