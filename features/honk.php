<?php

class Improved_Heartbeat_Action_Honk {
	const author = 'Mike Schroder & Ryan McCue';

	public function __construct() {
		add_filter( 'json_endpoints', array( $this, 'json_endpoints' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'wc_heartbeat_honk_script_enqueue' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'wc_heartbeat_honk_js' ), 20 );
	}
	
	public function json_endpoints( $routes ) {
		$routes[ '/honk_horn' ] = array(
			function () {
				Improved_Heartbeat_Dispatcher::add( 'wc-heartbeat-honk', 1 );
				return array( 'result' => true );
			},
			WP_JSON_Server::READABLE
		);

		return $routes;
	}
	
	public function wc_heartbeat_honk_script_enqueue( $hook_suffix ) {
		// Make sure the JS part of the Heartbeat API is loaded.
		wp_enqueue_script( 'heartbeat' );
	}

	public function wc_heartbeat_honk_js() {
		$honk_audio = plugins_url( 'assets/car-honk.mp3', dirname( __FILE__ ) );
		?>

		<script>
		jQuery(document).ready( function($) {
			var honk;

			$(document).on( 'heartbeat-send.wp-heartbeat-honk', function( e, data ) {

				window.wp.heartbeat.interval(5);

			// Listen for the tick custom event.
			}).on( 'heartbeat-tick.wc-heartbeat-honk', function( e, data ) {
				// Double-check we have the data we're listening for
				if ( ! data['wc-heartbeat-honk'] ) {
					return;
				}

				if ( ! honk ) {
					honk = new Audio('<?php echo $honk_audio; ?>');
				}

				for ( var count = data['wc-heartbeat-honk']; count--; count > 0 ) {
					honk.play();
				}
			});

			// Initial connection to cement our new interval timing
			window.wp.heartbeat.connectNow();
		});
		</script>

		<?php
	}

}

$features[] = new Improved_Heartbeat_Action_Honk;