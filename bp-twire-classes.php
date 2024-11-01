<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1); 

include "twitter.php";

define ("TWITTER_QUERY_DELAY_SECONDS", 15);

class BP_Twire_Post {
	var $table_name;
	
	var $id;
	var $tid;
	var $item_id;
	var $user_id;
	var $content;
	var $date_posted;
	var $username;
	var $password;
	var $reply_to_username;
	var $last_id;
	
	function bp_twire_post( $table_name, $id = null, $populate = true ) {
		$this->table_name = $table_name;

		if ( $id ) {
			$this->id = $id;
			
			if ( $populate )
				$this->populate();
		}
	}
	
	function populate() {
		global $wpdb, $bp;

		$sql = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $this->id );

		$twire_post = $wpdb->get_row($sql);

		if ( $twire_post ) {
			$this->item_id = $twire_post->item_id;
			$this->user_id = $twire_post->user_id;
			$this->content = $twire_post->content;
			$this->date_posted = $twire_post->date_posted;
		}
	}
	
	function save() {
		global $wpdb, $bp, $current_user;

		$username = get_usermeta( $current_user->id, 'twire_username');
		$password = get_usermeta( $current_user->id, 'twire_password');
		$prefix = get_usermeta( $current_user->id, 'twire_fromTwitterPrefix');

		//Check for the case where you just installed the plugin, but didn't configure it.
		if ($username === "" or $password === "")
		{
		    return false;
		}

		//Twitter side
		$save_twitter = new twitter();
                $save_twitter->username = $username;
                $save_twitter->password = $password;
		$save_twitter->type = "xml";

                $status = $save_twitter->update(stripslashes($prefix . $this->content));

		$test_id = (string)$status->id[0];
		$twitter_id = ((string)$test_id);
		$this->last_id = $twitter_id;

                $result = $status ? TRUE : FALSE;

                if ($result == true)
                {
                    //Buddypress Side
		
                    $this->item_id = apply_filters( 'bp_twire_post_item_id_before_save', $this->item_id, $this->id ); 
                    $this->user_id = apply_filters( 'bp_twire_post_user_id_before_save', $this->user_id, $this->id ); 
                    $this->content = apply_filters( 'bp_twire_post_content_before_save', $this->content, $this->id ); 
                    $this->date_posted = apply_filters( 'bp_twire_post_date_posted_before_save', $this->date_posted, $this->id );

                    do_action( 'bp_twire_post_before_save', $this );		
		
                    if ( $this->id ) {
			$sql = $wpdb->prepare( 
				"UPDATE {$this->table_name} SET 
					item_id = %d, 
					user_id = %d, 
					content = %s,
				        twitter_id = %s,
				        username = %s,	
					date_posted = FROM_UNIXTIME(%d)
				WHERE
					id = %d
				",
					$this->item_id, 
					$this->user_id, 
					$this->content,
				        $twitter_id,
				        $username,	
					$this->date_posted, 
					$this->id
			);
                    } else {
			$sql = $wpdb->prepare( 
				"INSERT INTO {$this->table_name} ( 
					item_id,
					user_id,
					content,
					twitter_id,
				        username,
					date_posted
				) VALUES (
					%d, %d, %s, %s, %s, FROM_UNIXTIME(%d)
				)",
					$this->item_id, 
					$this->user_id, 
					$this->content,
				        $twitter_id,
				        $username,	
					$this->date_posted 
			);
                    }

                    $result = $wpdb->query($sql);

                    if ( !$this->id )
			$this->id = $wpdb->insert_id;

                    do_action( 'bp_twire_post_after_save', $this );
                }

		return $result;

	}
	
	function delete() {
		global $wpdb, $bp, $current_user;

		$username = get_usermeta( $current_user->id, 'twire_username');
		$password = get_usermeta( $current_user->id, 'twire_password');

		//Check for the case where you just installed the plugin, but didn't configure it.
		if ($username === "" or $password === "")
		{
		    return false;
		}

		$sql = $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE id = %d", $this->id );

		$twire_post = $wpdb->get_row($sql);

		if ( $twire_post ) {
			$this->item_id = $twire_post->item_id;
			$this->user_id = $twire_post->user_id;
			$this->content = $twire_post->content;
			$this->date_posted = $twire_post->date_posted;
		}

		$delete_twitter = new twitter();
                $delete_twitter->username = $username;
                $delete_twitter->password = $password;
		$delete_twitter->type = "xml";

		$status = $delete_twitter->deleteStatus("$twire_post->twitter_id");

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE id = %d", $this->id ) );

		return $status;
	}

	function get_last_id()
	{
		global $wpdb, $bp, $current_user;

		$username = get_usermeta( $current_user->id, 'twire_username');
		$password = get_usermeta( $current_user->id, 'twire_password');

		//Check for the case where you just installed the plugin, but didn't configure it.
		if ($username === "" or $password === "")
		{
		    return false;
		}

		$twitter =  new twitter();
		$twitter->username=$username;
		$twitter->password=$password;
		$twitter->type='xml';
		$status = $twitter->userTimeline(false, 1, false, false, false);

		$twitterid = (string)($status->status[0]->id);

		return $twitterid;
	}

	function get_twitter_user_id()
	{
		global $wpdb, $bp, $current_user;

		$username = get_usermeta( $current_user->id, 'twire_username');
		$password = get_usermeta( $current_user->id, 'twire_password');

		//Check for the case where you just installed the plugin, but didn't configure it.
		if ($username === "" or $password === "")
		{
		    return false;
		}

		$twitter =  new twitter();
		$twitter->type='xml';
		$status = $twitter->showUser(false, false, false, $username);

		$twitter_user_id = (string)($status->id);

		return $twitter_user_id;
	}
	
	/* Static Functions */
	
	function get_all_for_item( $item_id, $table_name, $page = false, $limit = false ) {
		global $wpdb, $bp, $current_user;
		
		/*
		 * Idea is to talk to twitter here and get the last 20.
		 * Then feed it in to the db.  Then let the db grab it and send it back to the plugin.
		 * We don't want to insert if it is already in the DB.
		 */

		$username    = get_usermeta( $bp->displayed_user->id, 'twire_username');
		$password    = get_usermeta( $bp->displayed_user->id, 'twire_password');
                $last_update = get_usermeta( $bp->displayed_user->id, 'twire_cache_time');

		if ($username !== "" and $password !== "" and ($last_update === "" or (($last_update + TWITTER_QUERY_DELAY_SECONDS) < time()) ) )
                {
                    update_usermeta( (int)$bp->displayed_user->id, 'twire_cache_time', attribute_escape(time()) );

		    $twitter =  new twitter();
		    $twitter->username=$username;
		    $twitter->password=$password;
		    $twitter->type='xml';
		    $status = $twitter->userTimeline(false, 20, false, false, $page);
		    if (isset($status->status))
		    {
			#$currentTwit = (string)$status->status[0]->text;
			#$created_at = (string)$status->status[0]->created_at;
			//Twitter: Wed Apr 01 02:31:39 +0000 2009
			//BP     : 2009-03-26 01:16:59

			foreach ($status->status as $status)
			{
				$currentTwit = (string)$status->text;
				$created_at = (string)$status->created_at;
				$timestamp = strtotime($created_at);
				$created_at = date('Y-m-d H:i:s', $timestamp);
				$id = (string)$status->id;
				//Not used now, but are returned to us
				$source = (string)$status->source;
				$truncated = (boolean)$status->truncated;
				$in_reply_to_status_id = (string)$status->in_reply_to_status_id;
				$in_reply_to_user_id = (string)$status->in_reply_to_user_id;
				$favorited = (boolean)$status->favorited;
				$in_reply_to_screen_name = (string)$status->in_reply_to_screen_name;
				$user = $status->user;

				/* Example user stats
				 *
				    [id] => 15379173
				    [name] => dfa327
				    [screen_name] => dfa327
				    [location] => pelham, nh
				    [description] => I started getpaidfrom.us.  A bloging site that splits the profit with you!
				    [profile_image_url] => http://s3.amazonaws.com/twitter_production/profile_images/56451140/head_shot_normal.JPG
				    [url] => http://getpaidfrom.us
				    [protected] => false
				    [followers_count] => 22
				    [profile_background_color] => 9AE4E8
				    [profile_text_color] => 333333
				    [profile_link_color] => 0084B4
				    [profile_sidebar_fill_color] => DDFFCC
				    [profile_sidebar_border_color] => BDDCAD
				    [friends_count] => 10
				    [created_at] => Thu Jul 10 15:57:09 +0000 2008
				    [favourites_count] => 0
				    [utc_offset] => -18000
				    [time_zone] => Eastern Time (US & Canada)
				    [profile_background_image_url] => http://static.twitter.com/images/themes/theme1/bg.gif
				    [profile_background_tile] => false
				    [statuses_count] => 114
				    [notifications] => false
				    [verified] => false
				    [following] => false
				 */

				#$avatar = $user[profile_image_url];

				if ($in_reply_to_user_id == "")
				{
					$user_id =  $bp->displayed_user->id;
				} else {
				    $user_id = get_usermeta( $in_reply_to_user_id, 'twire_bpwp_id');
				    $bp_reply_to_username =  get_usermeta( $user_id, 'twire_username');
				    $bp_reply_to_username = $user_id;
				}
			
				$result = $wpdb->get_results( $wpdb->prepare( "INSERT INTO {$table_name} (item_id, username, user_id, content, twitter_id, timestamp, date_posted, reply_to_username, bp_reply_to_username, bp_reply_user_id) VALUES ( '" . $bp->displayed_user->id . "', '".$username."', '".$user_id."', '".mysql_real_escape_string($currentTwit)."', '".$id."', '".$created_at."', '".$created_at."', '".$in_reply_to_screen_name."', '".$bp_reply_to_username."', '".$bp_reply_to_username."') ON DUPLICATE KEY UPDATE item_id='".$bp->displayed_user->id."'"));
			}
		    }
		}

		if ( $limit && $page )
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
		
		$twire_posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE item_id = %d  ORDER BY date_posted DESC $pag_sql", $item_id ) );
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(id) FROM {$table_name} WHERE item_id = %d", $item_id ) );

		return array( 'twire_posts' => $twire_posts, 'count' => $count );
	}
	
	function delete_all_for_item( $item_id, $table_name ) {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE item_id = %d", $item_id ) );
	}
}

/**
 * xprofile_screen_notification_settings()
 *
 * Loads the notification settings for the xprofile component.
 * Settings are hooked into the function: bp_core_screen_notification_settings_content()
 * in bp-core/bp-core-settings.php
 * 
 * @package BuddyPress Xprofile
 * @global $current_user WordPress global variable containing current logged in user information
 */
function twire_screen_notification_settings() { 
	global $current_user; ?>
	<?php if ( function_exists('bp_twire_install') ) { ?>
		<tr>
			<td></td>
			<td><?php _e( 'A member posts on your twire', 'bp-twire' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_profile_twire_post]" value="yes" <?php if ( !get_usermeta( $current_user->id, 'notification_profile_twire_post' ) || 'yes' == get_usermeta( $current_user->id, 'notification_profile_twire_post' ) ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_profile_twire_post]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id, 'notification_profile_twire_post' ) ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		
		<?php do_action( 'twire_screen_notification_settings' ) ?>
	<?php } ?>
<?php	
}
add_action( 'xprofile_screen_notification_settings', 'twire_screen_notification_settings', 1 );



#--------No longer needed below
function twire_add_settings_nav()
{
	global $bp;

	bp_core_add_subnav_item( 'settings', 'Twire', __('Twire', 'bp-twire'), $bp->loggedin_user->domain . 'settings/', 'twire_screen_general_settings', false, bp_is_home() );
}
#add_action( 'wp', 'twire_add_settings_nav', 2 );
#add_action( 'admin_menu', 'twire_add_settings_nav', 2 );

function twire_screen_general_settings() {
	global $current_user, $bp_settings_updated;
	
	$bp_settings_updated = false;
	
	if ( $_POST['submit']  && check_admin_referer('bp_settings_twire') ) {
		update_usermeta( (int)$current_user->id, 'twire_prefix', $_POST['twire_prefix'] );
		update_usermeta( (int)$current_user->id, 'twire_username', $_POST['twire_username'] );
		update_usermeta( (int)$current_user->id, 'twire_password', $_POST['twire_password'] );
		
		$bp_settings_updated = true;
		do_action('profile_update', $current_user->id);
	}
	
	add_action( 'bp_template_title', 'twire_screen_general_settings_title' );
	add_action( 'bp_template_content', 'twire_screen_general_settings_content' );
	
	bp_core_load_template('plugin-template');
}

function twire_screen_general_settings_title() {
	_e( 'Twire Settings', 'bp-twire' );
}

function twire_screen_general_settings_content() {
	global $bp, $current_user, $bp_settings_updated, $pass_error; ?>

	
<?php
	$twire_prefix = "Tweet from GetPaidFrom.Us:";
	if ( get_usermeta( $current_user->id, 'twire_prefix') != "" )
	{
		$twire_prefix = get_usermeta( $current_user->id, 'twire_prefix');
	}
	if ( get_usermeta( $current_user->id, 'twire_username') != "" )
	{
		$twire_username = get_usermeta( $current_user->id, 'twire_username');
	}
	if ( get_usermeta( $current_user->id, 'twire_password') != "" )
	{
		$twire_password = get_usermeta( $current_user->id, 'twire_password');
	}
?>

	<?php if ( $bp_settings_updated && !$pass_error ) { ?>
		<div id="message" class="updated fade">
			<p><?php _e( 'Changes Saved.', 'bp-twire' ) ?></p>
		</div>
	<?php } ?>
	
	<form action="<?php echo $bp->loggedin_user->domain . 'settings/Twire' ?>" method="post" id="settings-form">
			<fieldset class="options">
				<table border=0>
					<tr>
						<td>Prefix:</td>
						<td><input name="twire_prefix" type="text" id="twire_prefix" value="<?php echo $twire_prefix; ?>" size="45" /></td>
					</tr>
					<tr>
						<td>Twitter Username:</td>
        					<td><input name="twire_username" type="text" id="twire_username" value="<?php echo $twire_username; ?>" size="45" /></td>
					</tr>
					<tr>
						<td>Twitter Password:</td>
        					<td><input name="twire_password" type="password" id="twire_password" value="<?php echo $twire_password; ?>" size="45" /></td>
					</tr>
				</table>
			</fieldset>

		<p><input type="submit" name="submit" value="<?php _e( 'Save Changes', 'bp-twire' ) ?>" id="submit" class="auto"/></p>
		<?php wp_nonce_field('bp_settings_twire') ?>
	</form>
<?php
}
?>
