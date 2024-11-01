<?php
define ( 'BP_TWIRE_IS_INSTALLED', 1 );

define ( 'BP_TWIRE_DB_VERSION', '1' );

define ( 'BP_TWIRE_SLUG', apply_filters( 'bp_twire_slug', 'twire' ) );

include_once( WP_PLUGIN_DIR . '/twire/bp-twire-classes.php' );
include_once( WP_PLUGIN_DIR . '/twire/bp-twire-ajax.php' );
include_once( WP_PLUGIN_DIR . '/twire/bp-twire-templatetags.php' );
include_once( WP_PLUGIN_DIR . '/twire/bp-twire-notifications.php' );
include_once( WP_PLUGIN_DIR . '/twire/bp-twire-cssjs.php' );
include_once( WP_PLUGIN_DIR . '/twire/bp-twire-filters.php' );

/**************************************************************************
 bp_twire_setup_globals()
 
 Set up and add all global variables for this component, and add them to 
 the $bp global variable array.
 **************************************************************************/

function bp_twire_install() {
	// Tables are installed on a per component basis, where needed.
	global $wpdb, $bp;
	
	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	
	/**
	 * You'll need to write your table definition below, if you want to
	 * install database tables for your component. You can define multiple
	 * tables by adding SQL to the $sql array.
	 *
	 * Creating multiple tables:
	 * $bp->xxx->table_name is defined in bp_example_setup_globals() below.
	 *
	 * You will need to define extra table names in that function to create multiple tables.
	 */
	$sql[] = "CREATE TABLE {$bp->twire->table_name} (
		  		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  		item_id bigint(20) NOT NULL,
		  		username varchar(20) NOT NULL,
		  		user_id bigint(20) NOT NULL,
		  		bp_username varchar(40) NOT NULL,
			        content varchar(180) NOT NULL,
			        twitter_id varchar(20) NOT NULL UNIQUE KEY,
				timestamp timestamp NOT NULL,
				date_posted datetime NOT NULL,
				reply_to_username varchar(20) NOT NULL,
				bp_reply_to_username varchar(40) DEFAULT NULL,
				bp_reply_user_id bigint(20) NOT NULL
		 	   ) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );
	/**
	 * The dbDelta call is commented out so the example table is not installed.
	 * Once you define the SQL for your new table, uncomment this line to install
	 * the table. (Make sure you increment the BP_EXAMPLE_DB_VERSION constant though).
	 */
	dbDelta($sql);
	
	update_site_option( 'bp-twire-db-version', BP_TWIRE_DB_VERSION );
}

function bp_twire_setup_globals() {
	global $bp, $wpdb;

	$bp->twire->table_name = $wpdb->base_prefix . 'bp_twire';	
	$bp->twire->image_base = WP_PLUGIN_URL . '/bp-twire/images';
	$bp->twire->slug = BP_TWIRE_SLUG;
	$bp->twire->format_activity_function = 'bp_twire_format_activity';
    $bp->twire->format_notification_function = 'bp_twire_format_notifications';
    $bp->twire->id = 'twire';

	$bp->version_numbers->twire = BP_TWIRE_VERSION;
}
add_action( 'plugins_loaded', 'bp_twire_setup_globals', 1 ); //Important!!!
add_action( 'wp', 'bp_twire_setup_globals', 1 );	
add_action( 'admin_menu', 'bp_twire_setup_globals', 1 );

function bp_twire_check_installed() {	
	global $wpdb, $bp;

	if ( !is_site_admin() )
		return false;

	
	/***
	 * If you call your admin functionality here, it will only be loaded when the user is in the
	 * wp-admin area, not on every page load.
	 */
	#require ( 'bp-twire/bp-twire-admin.php' );

	/* Need to check db tables exist, activate hook no-worky in mu-plugins folder. */
	if ( get_site_option('bp-twire-db-version') < BP_TWIRE_DB_VERSION )
		bp_twire_install();
}
add_action( 'admin_menu', 'bp_twire_check_installed' );

/***** Screens **********/


	function bp_twire_screen_latest_header() {
		_e( 'Twire Latest Tweets', 'bp-twire' );
	}

	function bp_twire_screen_latest_title() {
		_e( 'Twire Latest Tweets', 'bp-twire' );
	}

    function bp_twire_screen_latest() {
        do_action( 'bp_twire_screen_latest', $_GET['new'] );

		add_action( 'bp_template_content_header', 'bp_twire_screen_latest_header' );
		add_action( 'bp_template_title', 'bp_twire_screen_latest_title' );

		bp_core_load_template( apply_filters( 'bp_twire_template_latest', 'twire/latest' ) );
    }

function bp_twire_screen_latest_old() {
    /* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_twire_screen_latest', $_GET['new'] );
    add_action( 'bp_template_content_header', 'bp_media_view_header' );
	 add_action( 'bp_template_title', 'bp_media_view_title' );
     add_action( 'bp_template_content', 'bp_media_view_content' );
     bp_media_plugin_template();
}

function bp_twire_record_activity( $args = true ) {
    global $bp;
	if ( function_exists('bp_activity_add') ) {

        $defaults = array(
		'id' => false,
		'user_id' => $bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->twire->id,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => false
        );

        $r = wp_parse_args( $args, $defaults );
        extract( $r );

        #echo "<pre>";
        #print_r ($r);
        #echo "</pre>";

        #bp_activity_add( $item_id, $component_name, $component_action, $is_private, $secondary_item_id, $user_id, $secondary_user_id, $recorded_time );
        #bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $component_action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component_name, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => false ) );
        $activity_id = bp_activity_add( array(
            'id' => $item_id,
		    'user_id' => $user_id,
		    'action' => $component_action,
		    'content' => $content,
		    'primary_link' => $primary_link,
		    'component' => $bp->twire->id,
		    'type' => 'activity_update'
	    ) );
	}
}

function bp_twire_delete_activity( $args = true ) {
    global $bp;
	if ( function_exists('bp_activity_delete_by_item_id') ) {
        extract($args);
		bp_activity_delete_by_item_id( array( 'item_id' => $item_id, 'user_id' => $user_id, 'component' => $bp->twire->id ) );
		#bp_activity_delete( $item_id, $component_name, $component_action, $user_id, $secondary_item_id, $recorded_time );
	}
}

function bp_twire_new_post( $item_id, $message, $component_name, $private_post = false, $table_name = null ) {
	global $bp;
	
	if ( empty($message) || !is_user_logged_in() )
		return false;

	if ( !$table_name )
		$table_name = $bp->{$component_name}->table_name;

	$twire_post = new BP_Twire_Post($table_name);
	$twire_post->item_id = $item_id;
	$twire_post->user_id = $bp->loggedin_user->id;
	$twire_post->date_posted = time();

	$allowed_tags = apply_filters( 'bp_twire_post_allowed_tags', '<a>,<b>,<strong>,<i>,<em>,<img>' );
		
	$message = strip_tags( $message, $allowed_tags );
    $twire_post->content = $message;

    $primary_link = bp_core_get_userlink( $bp->loggedin_user->id );

	if ( !$twire_post->save() )
		return false;

	// Record in the activity streams
	bp_twire_record_activity( array( 'item_id' => $twire_post->id, 'content' => $message, 'primary_link' => $primary_link, 'component_name' => $bp->twire->slug, 'component_action' => 'new twire post', 'is_private' => 0, 'user_id' => $bp->displayed_user->id  ) );
	
	do_action( 'bp_twire_post_posted', $twire_post->id, $twire_post->item_id, $twire_post->user_id );
	
	return $twire_post->id;
}

function bp_twire_delete_post( $twire_post_id, $component_name, $table_name = null ) {
	global $bp;

	if ( !is_user_logged_in() )
		return false;

	if ( !$table_name )
		$table_name = $bp->{$component_name}->table_name;
	
	$twire_post = new BP_Twire_Post( $table_name, $twire_post_id );
	$twire_post->item_id = $twire_post_id;
	$twire_post->user_id = $bp->loggedin_user->id;
	
	if ( !is_site_admin() ) {
		if ( !$bp->is_item_admin ) {
			if ( $twire_post->user_id != $bp->loggedin_user->id )
				return false;
		}
	}
	
	if ( !$twire_post->delete() )
		return false;

	// Delete activity stream items
	bp_twire_delete_activity( array( 'user_id' => $twire_post->user_id, 'item_id' => $twire_post->id, 'component_name' => $component_name, 'component_action' => 'delete_twire_post' ) );	

	do_action( 'bp_twire_post_deleted', $twire_post->id, $twire_post->item_id, $twire_post->user_id );
	
	return true;
}

function bp_profile_twire_can_post() {
	global $bp;
	
	if ( bp_is_home() )
		return true;
	
	if ( function_exists('friends_install') ) {
		if ( friends_check_friendship( $bp->loggedin_user->id, $bp->displayed_user->id ) )
			return true;
		else
			return false;
	} 
	
	return true;
}

// List actions to clear super cached pages on, if super cache is installed
add_action( 'bp_twire_post_deleted', 'bp_core_clear_cache' );
add_action( 'bp_twire_post_posted', 'bp_core_clear_cache' );

/**
 * xprofile_action_new_wire_post()
 *
 * Posts a new wire post to the users profile wire. 
 * 
 * @package BuddyPress XProfile
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_wire_new_post() Adds a new wire post to a specific wire using the ID of the item passed and the table name.
 * @uses bp_core_add_message() Adds an error/success message to the session to be displayed on the next page load.
 * @uses bp_core_redirect() Safe redirects to a new page using the wp_redirect() function
 */
function xprofile_action_new_twire_post() {
	global $bp;

	if ( $bp->current_component != $bp->twire->slug )
		return false;
	
	if ( 'post' != $bp->current_action )
		return false;
		
	/* Check the nonce */
	if ( !check_admin_referer( 'bp_twire_post' ) ) 
		return false;
		
	if ( !$twire_post_id = bp_twire_new_post( $bp->displayed_user->id, $_POST['twire-post-textarea'], $bp->twire->slug, false, $bp->twire->table_name ) ) {
		bp_core_add_message( __( 'Twire message could not be posted. Please try again.', 'bp-twire' ), 'error' );
	} else {
		bp_core_add_message( __( 'Twire message successfully posted.', 'bp-twire' ) );

		if ( !bp_is_home() ) {
			/* Record the notification for the user */
			bp_core_add_notification( $bp->loggedin_user->id, $bp->displayed_user->id, $bp->twire->slug, 'new_twire_post' );	
			
			/* We'll use this do_action call to send the email notification. See bp-twire-notifications.php */
			do_action( 'bp_twire_send_notification', $bp->displayed_user->id, $bp->loggedin_user->id );
		}
		
		do_action( 'xprofile_new_twire_post', $twire_post_id );	
	}

	if ( !strpos( $_SERVER['HTTP_REFERER'], $bp->twire->slug ) ) {
		bp_core_redirect( $bp->displayed_user->domain );
	} else {
		bp_core_redirect( $bp->displayed_user->domain . $bp->twire->slug );
	}
}
add_action( 'wp', 'xprofile_action_new_twire_post', 3 );

/**
 * xprofile_action_delete_twire_post()
 *
 * Deletes a wire post from the users profile wire. 
 * 
 * @package BuddyPress XProfile
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_wire_delete_post() Deletes a wire post for a specific wire using the ID of the item passed and the table name.
 * @uses xprofile_delete_activity() Deletes an activity item for the xprofile component and a particular user.
 * @uses bp_core_add_message() Adds an error/success message to the session to be displayed on the next page load.
 * @uses bp_core_redirect() Safe redirects to a new page using the wp_redirect() function
 */
function xprofile_action_delete_twire_post() {
	global $bp;

	if ( $bp->current_component != $bp->twire->slug )
		return false;
	
	if ( $bp->current_action != 'delete' )
		return false;
	
	if ( !check_admin_referer( 'bp_twire_delete_link' ) )
		return false;
			
	$twire_post_id = $bp->action_variables[0];
	
	if ( bp_twire_delete_post( $twire_post_id, $bp->twire->slug, $bp->twire->table_name ) ) {
		bp_core_add_message( __('Twire message successfully deleted.', 'bp-twire') );

		do_action( 'xprofile_delete_twire_post', $twire_post_id );						
	} else {
		bp_core_add_message( __('Twire post could not be deleted, please try again.', 'bp-twire'), 'error' );
	}
	
	if ( !strpos( $_SERVER['HTTP_REFERER'], $bp->twire->slug ) ) {
		bp_core_redirect( $bp->displayed_user->domain );
	} else {
		bp_core_redirect( $bp->displayed_user->domain. $bp->twire->slug );
	}
}
add_action( 'wp', 'xprofile_action_delete_twire_post', 3 );

function xprofile_action_reply_twire_post() {
	global $bp;

	if ( $bp->current_component != $bp->twire->slug )
		return false;
	
	if ( 'reply' != $bp->current_action )
		return false;
		
	/* Check the nonce */
	if ( !check_admin_referer( 'bp_twire_reply_link' ) ) 
		return false;

	$twire_post_id = $bp->action_variables[0];
		
	if ( !$twire_post_id = bp_twire_new_post( $bp->displayed_user->id, $_POST['twire-post-textarea'], $bp->twire->slug, false, $bp->twire->table_name ) ) {
		bp_core_add_message( __( 'Twire message could not be posted. Please try again.', 'bp-twire' ), 'error' );
	} else {
		bp_core_add_message( __( 'Twire message successfully posted.', 'bp-twire' ) );

		if ( !bp_is_home() ) {
			/* Record the notification for the user */
			bp_core_add_notification( $bp->loggedin_user->id, $bp->displayed_user->id, 'profile', 'new_twire_post' );	

			/* We'll use this do_action call to send the email notification. See bp-twire-notifications.php */
			do_action( 'bp_twire_send_notification', $bp->displayed_user->id, $bp->loggedin_user->id );
		}
		
		do_action( 'xprofile_new_twire_post', $twire_post_id );	
	}

	if ( !strpos( $_SERVER['HTTP_REFERER'], $bp->twire->slug ) ) {
		bp_core_redirect( $bp->displayed_user->domain );
	} else {
		bp_core_redirect( $bp->displayed_user->domain . $bp->twire->slug );
	}
}
#add_action( 'wp', 'xprofile_action_reply_twire_post', 3 );

function bp_twire_plugin_template()
{
    get_header();
?>
        <div id="content">
            <div class="padder">

                <?php do_action( 'bp_before_member_home_content' ) ?>

                <div id="item-header">
				    <?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			    </div><!-- #item-header -->

                <div id="item-nav">
				    <div class="item-list-tabs no-ajax" id="object-nav">
                        <ul>
                            <?php bp_get_displayed_user_nav() ?>
					    </ul>
				    </div>
			    </div>

                <div class="item-body">
                    <h2><?php #do_action('bp_template_title') ?></h2>
                    <div class="item-list-tabs no-ajax" id="subnav">
					    <ul>
						    <?php bp_get_options_nav() ?>
						    <?php do_action( 'bp_twire_options_nav' ); ?>
					    </ul>
				    </div>
                    <div >
					    <ul>
                            <?php do_action('bp_template_content_header') ?>
					    </ul>
				    </div>

                    <?php do_action('bp_template_content') ?>
                </div>
            </div>
	</div>

	<?php locate_template( array( 'sidebar.php' ), true ) ?>
<?php 
get_footer(); 
}    

function bp_twire_screen_settings_menu() {
    global $bp, $current_user, $bp_settings_updated, $pass_error;

    if ( $_POST['submit'] ) {
		check_admin_referer('bp_settings_twire');

		/** 
		 * This is when the user has hit the save button on their settings. 
		 * The best place to store these settings is in wp_usermeta. 
		 */
		update_usermeta( (int)$bp->loggedin_user->id, 'twire_fromTwitterPrefix', attribute_escape($_POST['Twire_prefix']) );
		update_usermeta( (int)$bp->loggedin_user->id, 'twire_username', attribute_escape($_POST['Twire_username']) );
		update_usermeta( (int)$bp->loggedin_user->id, 'twire_password', attribute_escape($_POST['Twire_password']) );

		//Now do a little hack in which we add some usermeta of the twitter id to return the buddypress/wordpress id
		//Talk to twitter with credentials to get the user_id
		$twire_post = new BP_Twire_Post( $bp->twire->table_name, 0 );
		update_usermeta( (int)$twire_post->get_twitter_user_id(), 'twire_bpwp_id', attribute_escape((int)$bp->loggedin_user->id) );
	}

	add_action( 'bp_template_content_header', 'bp_twire_screen_settings_menu_header' );
	add_action( 'bp_template_title', 'bp_twire_screen_settings_menu_title' );
	add_action( 'bp_template_content', 'bp_twire_screen_settings_menu_content' );

    #bp_core_load_template('plugin-template');
    bp_twire_plugin_template();
}

	function bp_twire_screen_settings_menu_header() {
		_e( 'Twire Settings', 'bp-twire' );
	}

	function bp_twire_screen_settings_menu_title() {
		_e( 'Twire Settings', 'bp-twire' );
	}

	function bp_twire_screen_settings_menu_content() {
		global $bp, $bp_settings_updated; ?>

		<?php if ( $bp_settings_updated ) { ?>
			<div id="message" class="updated fade">
				<p><?php _e( 'Changes Saved.', 'bp-twire' ) ?></p>
			</div>
		<?php } ?>

<?php
		$user_id = $bp->loggedin_user->id;

		$twitterToWire_prefix = "From GPFU:";
		if ( get_usermeta( $user_id, 'twire_fromTwitterPrefix') != "" )
		{
			$twitterToWire_prefix = get_usermeta( $user_id, 'twire_fromTwitterPrefix');
		}
		if ( get_usermeta( $user_id, 'twire_username') != "" )
		{
			$twitterToWire_username = get_usermeta( $user_id, 'twire_username');
		}
		if ( get_usermeta( $user_id, 'twire_password') != "" )
		{
			$twitterToWire_password = get_usermeta( $user_id, 'twire_password');
		}
?>
        <form action="<?php echo $bp->loggedin_user->domain . BP_SETTINGS_SLUG . '/twire' ?>" name="bp-twire-admin-form" id="bp-twire-admin" class="bp-twire-admin-form" method="post">
				<label for="Twire_prefix">Prefix</label><span> ( leave blank for no prefix ) </span>
				<input name="Twire_prefix" type="text" id="Twire_prefix" value="<?php echo $twitterToWire_prefix; ?>" class="settings-input" />
				<br />

				<label for="Twire_username">Twitter Username</label>
				<input name="Twire_username" type="text" id="Twire_username" value="<?php echo $twitterToWire_username; ?>" class="settings-input" />
				<br />

				<label for="Twire_password">Twitter Password</label>
       				<input name="Twire_password" type="password" id="Twire_password" value="<?php echo $twitterToWire_password; ?>" class="settings-input" />
				<br />


	  		<p class="submit">
				<input type="submit" value="<?php _e( 'Save Settings', 'bp-twire' ) ?> &raquo;" id="submit" name="submit" />
			</p>
			<?php 
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp_settings_twire' );
			?>
		</form>
	<?php
	}

function bp_twire_setup_nav() {
    global $bp;
    
    $twire_link = $bp->loggedin_user->domain . $bp->twire->slug . '/';

    if (function_exists('anygig_orig_check_supporter'))
	if ( false == anygig_orig_check_supporter('Twire', 'bp-twire', $bp->twire->slug, 80, $bp->twire->id, $twire_link))
	    return;

    bp_core_new_nav_item(array(
            'name'=> __( 'Twire', 'bp-twire' ),
            'slug'=> $bp->twire->slug,
            'screen_function'=>'bp_twire_screen_latest',
            'default_subnav_slug'=>'all-posts',
            'position' => 80,
            'item_css_id' => $bp->twire->id
    ));

    /* Add the subnav items to the wire nav */
    bp_core_new_subnav_item( array(
        'name' => __( 'All Posts', 'bp-twire' ),
        'slug' => 'all-posts',
        'parent_url' => $twire_link,
        'parent_slug' => $bp->twire->slug,
        'screen_function' => 'bp_twire_screen_latest',
        'position' => 10 
    ));

    $settings_link = $bp->loggedin_user->domain . BP_SETTINGS_SLUG . '/';

    bp_core_new_subnav_item( array( 
        'name' => __( 'Twire', 'bp-twire' ),
        'slug' => 'twire',
        'parent_url' => $settings_link,
        'parent_slug' => BP_SETTINGS_SLUG,
        'screen_function' => 'bp_twire_screen_settings_menu',
        'position' => 20,
        'user_has_access' => bp_is_home() ) );
	
	/* Only execute the following code if we are actually viewing this component (e.g. http://example.org/example) */
	if ( $bp->current_component == $bp->twire->slug ) {
		if ( bp_is_home() ) {
			/* If the user is viewing their own profile area set the title to "My Example" */
			$bp->bp_options_title = __( 'Twire', 'bp-twire' );
		} else {
			/* If the user is viewing someone elses profile area, set the title to "[user fullname]" */
			//$bp->bp_options_avatar = bp_core_get_avatar( $bp->displayed_user->id, 1 );
			$bp->bp_options_title = $bp->displayed_user->fullname;
		}
    }
}
add_action( 'plugins_loaded', 'bp_twire_setup_nav' );
add_action( 'admin_menu', 'bp_twire_setup_nav', 2 );

#-------------------------------

/**
 * bp_twire_format_activity()
 *
 * This function will format an activity item based on the component action and return it for saving
 * in the activity cache database tables. It can then be selected and displayed with far less load on
 * the server.
 * 
 * @package Twire 
 * @param $item_id The ID of the specific item for which the activity is recorded (could be a wire post id, user id etc)
 * @param $action The component action name e.g. 'new_twire_post' or 'updated_profile'
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @global $current_user WordPress global variable containing current logged in user information
 * @return The readable activity item
 */
function bp_twire_format_activity( $item_id, $user_id, $action, $secondary_item_id = false, $for_secondary_user = false ) {
	global $bp, $current_user;

	switch( $action ) {
		case 'new_twire_post':
			if ( class_exists('BP_Twire_Post') ) {
				$twire_post = new BP_Twire_Post( $bp->twire->table_name, $item_id );
			}

			if ( !$twire_post )
				return false;

			$logged_username = get_usermeta( $bp->loggedin_user->id, 'twire_username');
			$displayed_username = get_usermeta( $bp->displayed_user->id, 'twire_username');

			# if content doesn have an @ in it then they twired themselves
			# if there is a @ in the content then strip out from @ to space and that's who it went to.

			if ( ( $twire_post->username == $displayed_username ) ) {
				
				$from_user_link = bp_core_get_userlink($user_id);
				$to_user_link = false;
								
				$content = sprintf( __('%s tweeted on their own twire', 'bp-twire'), $from_user_link ) . ': <span class="time-since">%s</span>';				
				$return_values['primary_link'] = bp_core_get_userlink( $bp->displayed_user->id, false, true );
			
			} else {
				$from_user_link = bp_core_get_userlink($current_user->id);
				$to_user_link = bp_core_get_userlink($bp->displayed_user->id, false, false, true, true);
				
				$content = sprintf( __('%s tweeted on %s twire', 'bp-twire'), $from_user_link, $to_user_link ) . ': <span class="time-since">%s</span>';			
				$return_values['primary_link'] = bp_core_get_userlink( $twire_post->item_id, false, true );
			
			} 
			
			if ( $content != '' ) {
				$post_excerpt = bp_create_excerpt($twire_post->content);
				
				$content .= '<blockquote>' . $post_excerpt . '</blockquote>';
				$return_values['content'] = $content;
				
				$return_values['content'] = apply_filters( 'xprofile_new_twire_post_activity', $content, $from_user_link, $to_user_link, $post_excerpt );
				
				return $return_values;
			}

			return false;
		break;
	}
	
	do_action( 'bp_twire_format_activity', $action, $item_id, $user_id, $action, $secondary_item_id, $for_secondary_user );
	
	return false;
}

/**
 * bp_twire_format_notifications()
 *
 * Format notifications into something that can be read and displayed
 * 
 * @package Twire 
 * @param $item_id The ID of the specific item for which the activity is recorded (could be a wire post id, user id etc)
 * @param $action The component action name e.g. 'new_wire_post' or 'updated_profile'
 * @param $total_items The total number of identical notification items (used for grouping)
 * @global $bp The global BuddyPress settings variable created in bp_core_setup_globals()
 * @uses bp_core_global_user_fullname() Returns the display name for the user
 * @return The readable notification item
 */
function bp_twire_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	if ( 'new_twire_post' == $action ) {
		if ( (int)$total_items > 1 ) {
			return apply_filters( 'bp_twire_new_post_notification', '<a href="' . $bp->loggedin_user->domain . $bp->twire->slug . '" title="' . __( 'Twire', 'bp-twire' ) . '">' . sprintf( __( 'You have %d new posts on your twire', 'bp-twire' ), (int)$total_items ) . '</a>', $total_items );		
		} else {
			$user_fullname = bp_core_global_user_fullname( $item_id );
			return apply_filters( 'bp_twire_new_post_notification', '<a href="' . $bp->loggedin_user->domain . $bp->twire->slug . '" title="' . __( 'Twire', 'bp-twire' ) . '">' . sprintf( __( '%s posted on your twire', 'bp-twire' ), $user_fullname ) . '</a>', $user_fullname );
		}
	}
	
	do_action( 'bp_twire_format_notifications', $action, $item_id, $secondary_item_id, $total_items );
	
	return false;
}

?>
