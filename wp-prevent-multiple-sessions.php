<?php

/*
Plugin Name: WP Prevetn Multiple Sessions
Version: 0.1.0
Description: Plugin for preventing mutilpe sessions of a user.
Author: Subair T C
Author URI:
Plugin URI:
Text Domain: wp-prevent-multiple-sessions
Domain Path: /languages
*/

defined( 'ABSPATH' ) or exit;

class wpPreventMutipleSessions {
     /**
	 * Construct the plugin
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'initialise' ) );
	}

    public function initialise( ) {
        add_action('set_logged_in_cookie',array( $this,'prevent_multiple_session'),5,6 );
        add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields') );
        add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields') );

        add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields') );
        add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields') );
    }

    function extra_user_profile_fields( $user ) {
        ?>
        <h3><?php _e("Allow Multiple sessions", "blank"); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="allowed_other_sessions"><?php _e("Allow Multiple Sessions"); ?></label></th>
                <td>
                    <?php
                     if(  get_user_meta(  $user->ID,'allowed_other_sessions',true ) ) {
                         $checked = 'checked';
                     } else {
                         $checked = ' ';
                     }

                    ?>

                    <input type="checkbox" name="allowed_other_sessions" id="allowed_other_sessions" value="1" class="checkbox"  <?php echo  $checked; ?> /><br />
                    <span class="description"><?php _e("Please check if allow multiple sessions for this user."); ?></span>
                </td>
            </tr>
        </table>

    <?php
    }

    function save_extra_user_profile_fields( $user_id ) {
        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }
        $meta_value = 0;
        if( isset( $_POST['allowed_other_sessions'] ) && !empty( $_POST['allowed_other_sessions'] )) {
            $meta_value = 1;
        }
        update_user_meta( $user_id, 'allowed_other_sessions', $meta_value );
    }

    public function prevent_multiple_session( $logged_in_cookie, $expire, $expiration, $user_id, $logged_in_text, $token ) {
        $manager = WP_Session_Tokens::get_instance( $user_id );
        
        /*$sessions =  $manager->get_all();
        $default_count = 1;
        $allowed_count = get_usermeta( $user_id, 'allowed_no_of_sessions',true );
        //var_dump($sessions);
        if( $allowed_count > count( $sessions) ) {
            
        }
        exit;
        */
        $allow_other_sessions = 0;
        $allow_other_sessions = get_usermeta( $user_id, 'allowed_other_sessions',true );
        if( !$allow_other_sessions ) {
            $manager->destroy_others( $token  );
        }
        
    }


    

}
$wpPreventMutipleSessions =  new wpPreventMutipleSessions;
