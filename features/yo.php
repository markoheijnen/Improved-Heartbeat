<?php

class Improved_Heartbeat_Feature_Yo {
	const author = 'Marko Heijnen';

	public function __construct() {
		add_filter( 'user_row_actions', array( $this, 'add_link_to_user_table' ), 10, 2 );
		add_action( 'wp_ajax_ih_feature_yo', array( $this, 'ajax_yo_user' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'wc_heartbeat_honk_script_enqueue' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'wc_heartbeat_honk_js' ), 20 );
	}


	public function add_link_to_user_table( $actions, $user_object ) {
		if ( get_current_user_id() == $user_object->ID ) {
			$title = __( 'Yo yourself', 'improved-hearthbeat' );
		}
		else {
			$title = __( 'Yo me!', 'improved-hearthbeat' );
		}

		if ( ! $user_object->display_name ) {
			$name = $user_object->user_login;
		}
		else {
			$name = $user_object->display_name;
		}

		$actions['yo'] = '<a href="#" class="yo-user" data-userid="' . $user_object->ID . '" data-username="' . $name . '">' . $title . '</a>';

		return $actions;
	}

	public function ajax_yo_user() {
		if ( isset( $_POST['user_id'] ) ) {
			$sender   = wp_get_current_user();
			$receiver = get_userdata( absint( $_POST['user_id'] ) );

			if ( ! $receiver ) {
				wp_send_json_error();
			}

			if( Improved_Heartbeat_Dispatcher::user_add( $receiver->ID, 'yo', $sender->user_login ) ) {
				wp_send_json_success();
			}
		}

		wp_send_json_error();
	}

	
	public function wc_heartbeat_honk_script_enqueue( $hook_suffix ) {
		// Make sure the JS part of the Heartbeat API is loaded.
		wp_enqueue_script( 'heartbeat' );
		wp_enqueue_script( 'humane' );

		wp_enqueue_style( 'humane-theme' );
	}

	public function wc_heartbeat_honk_js() {
		$honk_audio   = plugins_url( 'assets/car-honk.mp3', dirname( __FILE__ ) );
		$msg_yo       = __( 'Yo!', 'improved-hearthbeat' );
		$msg_feedback = __( 'You just yo-ed %s.', 'improved-hearthbeat' );
		?>

		<script>
		jQuery(document).ready( function($) {
			$('.yo-user').on( 'click', function( e ) {
				e.preventDefault();

				var link = $(this)

				jQuery.post(
					ajaxurl, 
					{
						'action': 'ih_feature_yo',
						'user_id': link.data('userid')
					}, 
					function(response){
						var msg = '<?php echo $msg_feedback; ?>'.replace( '%s', link.data('username') );

						if (typeof humane !== 'undefined') {
							humane.log( msg );
						}
						else {
							alert( msg );
						}
					}
				);
			});

			$(document).on( 'heartbeat-tick.yo', function( e, data ) {
				// Double-check we have the data we're listening for
				if ( ! data['yo'] ) {
					return;
				}

				if (typeof humane !== 'undefined') {
					humane.log('<?php echo $msg_yo;?>');
				}
				else {
					alert('<?php echo $msg_yo;?>');
				}
			});
		});
		</script>

		<?php
	}

}

$features[] = new Improved_Heartbeat_Feature_Yo;