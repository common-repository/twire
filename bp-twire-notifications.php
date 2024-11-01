<?php

/**
 * Notification functions are used to send email notifications to users on specific events
 * They will check to see the users notification settings first, if the user has the notifications
 * turned on, they will be sent a formatted email notification. 
 *
 * You should use your own custom actions to determine when an email notification should be sent.
 */

function bp_twire_notification( $to_user_id, $from_user_id ) {
	global $bp;
	
	/* Let's grab both user's names to use in the email. */
	$sender_name = bp_fetch_user_fullname( $from_user_id, false );
	$receiver_name = bp_fetch_user_fullname( $to_user_id, false );

	/* We need to check to see if the recipient has opted not to receive high-five emails */
	if ( 'no' == get_usermeta( (int)$to_user_id, 'notification_profile_twire_post' ) )
		return false;
	
	/* Get the userdata for the receiver and sender, this will include usernames and emails that we need. */
	$receiver_ud = get_userdata( $to_user_id );
	$sender_ud = get_userdata( $from_user_id );
	
	/* Now we need to construct the URL's that we are going to use in the email */
	$sender_profile_link = site_url( BP_MEMBERS_SLUG . '/' . $sender_ud->user_login . '/' . $bp->profile->slug );
	$sender_twire_link = site_url( BP_MEMBERS_SLUG . '/' . $sender_ud->user_login . '/' . $bp->twire->slug . '/all-posts' );
	$receiver_settings_link = site_url( BP_MEMBERS_SLUG . '/' . $receiver_ud->user_login . '/settings/notifications' );
		
	/* Set up and send the message */
	$to = $receiver_ud->user_email;
	$subject = '[' . get_blog_option( 1, 'blogname' ) . '] ' . sprintf( __( '%s Twired you!', 'bp-twire' ), $sender_name );

	$message = sprintf( __( 
'%s wrote on your Twire! Why not Twit them back?

To see %s\'s profile: %s

To send %s a Tweet: %s

---------------------
', 'bp-twire' ), $sender_name, $sender_name, $sender_profile_link, $sender_name, $sender_twire_link );

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'bp-twire' ), $receiver_settings_link );

	// Send it!
	wp_mail( $to, $subject, $message );
}
add_action( 'bp_twire_send_notification', 'bp_twire_notification', 10, 2 );

?>
