<?php

class BP_Twire_Posts_Template {
	var $current_twire_post = -1;
	var $twire_post_count;
	var $twire_posts;
	var $twire_post;
	
	var $in_the_loop;
	
	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_twire_post_count;
	
	var $can_post;
	
	var $table_name;
	
	function bp_twire_posts_template( $item_id, $can_post ) {
		global $bp;
		
		if ( $bp->current_component == $bp->twire->slug ) {
			$this->table_name = $bp->twire->table_name;
			
			// Seeing as we're viewing a users twire, lets remove any new twire
			// post notifications
			if ( 'all-posts' == $bp->current_action )
				bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, 'xprofile', 'new_twire_post' );
			
		} else {
			$this->table_name = $bp->{$bp->current_component}->table_name;
		}
		
		$this->pag_page = isset( $_REQUEST['twpage'] ) ? intval( $_REQUEST['twpage'] ) : 1;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : 5;

		$this->twire_posts = BP_Twire_Post::get_all_for_item( $item_id, $this->table_name, $this->pag_page, $this->pag_num );
		$this->total_twire_post_count = (int)$this->twire_posts['count'];
		
		$this->twire_posts = $this->twire_posts['twire_posts'];
		$this->twire_post_count = count($this->twire_posts);
		
		if ( (int)get_site_option('non-friend-twire-posting') && ( $bp->current_component == $bp->profile->slug || $bp->current_component == $bp->twire->slug ) )
			$this->can_post = 1;
		else
			$this->can_post = $can_post;
		
		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( 'twpage', '%#%', $bp->displayed_user->domain . $bp->twire->slug . '/' ),
			'format' => '',
			'total' => ceil($this->total_twire_post_count / $this->pag_num),
			'current' => $this->pag_page,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'mid_size' => 1
		));
#		print "<pre>";
#		print "item_id = $item_id";
#		print "can = $can_post";
#		print_r ($bp);
#		print "</pre>";
	}
	
	function has_twire_posts() {
		if ( $this->twire_post_count )
			return true;
		
		return false;
	}
	
	function next_twire_post() {
		$this->current_twire_post++;
		$this->twire_post = $this->twire_posts[$this->current_twire_post];
		
		return $this->twire_post;
	}
	
	function rewind_twire_posts() {
		$this->current_twire_post = -1;
		if ( $this->twire_post_count > 0 ) {
			$this->twire_post = $this->twire_posts[0];
		}
	}
	
	function user_twire_posts() { 
		if ( $this->current_twire_post + 1 < $this->twire_post_count ) {
			return true;
		} elseif ( $this->current_twire_post + 1 == $this->twire_post_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_twire_posts();
		}

		$this->in_the_loop = false;
		return false;
	}
	
	function the_twire_post() {
		global $twire_post;

		$this->in_the_loop = true;
		$this->twire_post = $this->next_twire_post();

		if ( 0 == $this->current_twire_post ) // loop has just started
			do_action('loop_start');
	}
}

function bp_has_twire_posts( $item_id = null, $can_post = true ) {
	global $twire_posts_template, $bp;
	
	if ( !$item_id )
		return false;
		
	$twire_posts_template = new BP_Twire_Posts_Template( $item_id, $can_post );
	#print "<pre>";
	#print "item_id = $item_id";
	#print "can = $can_post";
	#	print_r($twire_posts_template);
	#	print "</pre>";
	return $twire_posts_template->has_twire_posts();
}

function bp_twire_posts() {
	global $twire_posts_template;
	return $twire_posts_template->user_twire_posts();
}

function bp_the_twire_post() {
	global $twire_posts_template;
	return $twire_posts_template->the_twire_post();
}

function bp_twire_get_post_list( $item_id = null, $title = null, $empty_message = null, $can_post = true, $show_email_notify = false ) {
	global $bp_item_id, $bp_twire_header, $bp_twire_msg, $bp_twire_can_post, $bp_twire_show_email_notify;

	if ( !$item_id )
		return false;
	
	if ( !$message )
		$empty_message = __("There are currently no twire posts or unable to talk to Twitter!", 'bp-twire');
	
	if ( !$title )
		$title = __('Twire', 'bp-twire');

	/* Pass them as globals, using the same name doesn't work. */
	$bp_item_id = $item_id;
	$bp_twire_header = $title;
	$bp_twire_msg = $empty_message;
	$bp_twire_can_post = $can_post;
	$bp_twire_show_email_notify = $show_email_notify;

	load_template( STYLESHEETPATH . '/twire/post-list.php' );
}

function bp_twire_title() {
	global $bp_twire_header;
	echo apply_filters( 'bp_group_reject_invite_link', $bp_twire_header );
}

function bp_twire_item_id( $echo = false ) {
	global $bp_item_id;
	
	if ( $echo )
		echo apply_filters( 'bp_twire_item_id', $bp_item_id );
	else
		return apply_filters( 'bp_twire_item_id', $bp_item_id );
}

function bp_twire_no_posts_message() {
	global $bp_twire_msg;
	echo apply_filters( 'bp_twire_no_posts_message', "Twire was unable to retrieve any tweets" );
}

function bp_twire_can_post() {
	global $bp_twire_can_post;
	return apply_filters( 'bp_twire_can_post', $bp_twire_can_post );
}

function bp_twire_show_email_notify() {
	global $bp_twire_show_email_notify;
	return apply_filters( 'bp_twire_show_email_notify', $bp_twire_show_email_notify );
}

function bp_twire_post_id( $echo = true ) {
	global $twire_posts_template;
	
	if ( $echo )
		echo apply_filters( 'bp_twire_post_id', $twire_posts_template->twire_post->id );
	else
		return apply_filters( 'bp_twire_post_id', $twire_posts_template->twire_post->id );
}

function bp_twire_post_content() {
	global $twire_posts_template;

	echo apply_filters( 'bp_twire_post_content', $twire_posts_template->twire_post->content );
}

function bp_twire_needs_pagination() {
	global $twire_posts_template;

	if ( $twire_posts_template->total_twire_post_count > $twire_posts_template->pag_num )
		return true;
	
	return false;
}

function bp_twire_pagination() {
	global $twire_posts_template;
	echo $twire_posts_template->pag_links;
	wp_nonce_field( 'get_twire_posts' );
}

function bp_twire_pagination_count() {
	echo bp_get_twire_pagination_count();
}
	function bp_get_twire_pagination_count() {
		global $twire_posts_template;

		$from_num = intval( ( $twire_posts_template->pag_page - 1 ) * $twire_posts_template->pag_num ) + 1;
		$to_num = ( $from_num + ( $twire_posts_template->pag_num - 1) > $twire_posts_template->total_twire_post_count ) ? $twire_posts_template->total_twire_post_count : $from_num + ( $twire_posts_template->pag_num - 1); 

		return apply_filters( 'bp_get_twire_pagination_count', sprintf( __( 'Viewing post %d to %d (%d total posts)', 'buddypress' ), $from_num, $to_num, $twire_posts_template->total_twire_post_count ) );  
	}

function bp_twire_ajax_loader_src() {
	global $bp;
	
	echo apply_filters( 'bp_twire_ajax_loader_src', $bp->twire->image_base . '/ajax-loader.gif' );
}

function bp_twire_post_date( $date_format = null, $echo = true ) {
	global $twire_posts_template;

	if ( !$date_format )
		$date_format = get_option('date_format');
		
	if ( $echo )
		echo apply_filters( 'bp_twire_post_date', mysql2date( $date_format, $twire_posts_template->twire_post->date_posted ) );
	else
		return apply_filters( 'bp_twire_post_date', mysql2date( $date_format, $twire_posts_template->twire_post->date_posted ) );
}

function bp_twire_post_author_name( $echo = true ) {
	global $twire_posts_template;
	
	if ( $echo )
		echo apply_filters( 'bp_twire_post_author_name', bp_core_get_userlink( $twire_posts_template->twire_post->user_id ) );
	else
		return apply_filters( 'bp_twire_post_author_name', bp_core_get_userlink( $twire_posts_template->twire_post->user_id ) );
}

function bp_twire_post_author_avatar() {
	global $twire_posts_template;

    $author = new BP_Core_User( $twire_posts_template->twire_post->user_id );  
    echo $author->avatar_thumb;  
	#echo apply_filters( 'bp_twire_post_author_avatar', bp_core_get_avatar( $twire_posts_template->twire_post->user_id, 1 ) );
}

function bp_twire_get_post_form() {
	global $twire_posts_template;

	if ( is_user_logged_in() && $twire_posts_template->can_post )
		load_template( STYLESHEETPATH . '/twire/post-form.php' );		
}

function bp_twire_get_action() {
	echo bp_get_twire_get_action();
}
	function bp_get_twire_get_action() {
		global $bp;

		if ( empty( $bp->current_item ) )
			$uri = $bp->current_action;
		else
			$uri = $bp->current_item;

		if ( $bp->current_component == $bp->twire->slug || $bp->current_component == $bp->profile->slug ) {
			#echo "1component = ".  $bp->current_component;
			#echo "<br />";
			#echo apply_filters( 'bp_get_twire_get_action', $bp->displayed_user->domain . $bp->twire->slug . '/post/' );
			#echo "<br />";
			return apply_filters( 'bp_get_twire_get_action', $bp->displayed_user->domain . $bp->twire->slug . '/post/' );
		} else {
			#echo "2component = ".  $bp->current_component;
			return apply_filters( 'bp_get_twire_get_action', site_url() . '/' . $bp->{$bp->current_component}->slug . '/' . $uri . '/' . $bp->twire->slug . '/post/' );
		}
	}

function bp_twire_post_prefix() {
	global $wpdb, $bp, $current_user;

	if ( $current_user->id != $bp->displayed_user->id )
	{
		$username = get_usermeta( $bp->displayed_user->id, 'twire_username');
		echo "@$username ";
	}
}

function bp_twire_poster_avatar() {
	global $bp;
	
    $author = new BP_Core_User( $bp->loggedin_user->id );  
    echo $author->avatar_thumb;  
	#echo apply_filters( 'bp_twire_poster_avatar', bp_core_get_avatar( $bp->loggedin_user->id, 1 ) );
}

function bp_twire_poster_name( $echo = true ) {
	global $bp;
	
	if ( $echo )
		echo apply_filters( 'bp_twire_poster_name', '<a href="' . $bp->loggedin_user->domain . $bp->profile->slug . '">' . __('You', 'bp-twire') . '</a>' );
	else
		return apply_filters( 'bp_twire_poster_name', '<a href="' . $bp->loggedin_user->domain . $bp->profile->slug . '">' . __('You', 'bp-twire') . '</a>' );
}

function bp_twire_poster_date( $date_format = null, $echo = true ) {
	if ( !$date_format )
		$date_format = get_option('date_format');

	if ( $echo )
		echo apply_filters( 'bp_twire_poster_date', mysql2date( $date_format, date("Y-m-d H:i:s") ) );
	else
		return apply_filters( 'bp_twire_poster_date', mysql2date( $date_format, date("Y-m-d H:i:s") ) );	
}

function bp_twire_delete_link() {
	global $twire_posts_template, $bp;

	if ( empty( $bp->current_item ) )
		$uri = $bp->current_action;
	else
		$uri = $bp->current_item;

	if ( ( $twire_posts_template->twire_post->user_id == $bp->loggedin_user->id ) ) {
		if ( $bp->twire->slug == $bp->current_component || $bp->profile->slug == $bp->current_component ) {
			echo apply_filters( 'bp_twire_delete_link', '<a href="' . wp_nonce_url( $bp->displayed_user->domain . $bp->twire->slug . '/delete/' . $twire_posts_template->twire_post->id, 'bp_twire_delete_link' ) . '">[' . __('Delete', 'bp-twire') . ']</a>' );
		} else {
			echo apply_filters( 'bp_twire_delete_link', '<a href="' . wp_nonce_url( site_url( $bp->{$bp->current_component}->slug . '/' . $uri . '/twire/delete/' . $twire_posts_template->twire_post->id ), 'bp_twire_delete_link' ) . '">[' . __('Delete', 'bp-twire') . ']</a>' );
		}
	}
}

function bp_twire_reply_link() {
	global $twire_posts_template, $bp;

	#dfa 5/27/2009 removed until I figure out how I want this to work
	return;

	if ( empty( $bp->current_item ) )
		$uri = $bp->current_action;
	else
		$uri = $bp->current_item;

	if ( ( $twire_posts_template->twire_post->user_id == $bp->loggedin_user->id ) ) {
		if ( $bp->twire->slug == $bp->current_component || $bp->profile->slug == $bp->current_component ) {
			echo apply_filters( 'bp_twire_reply_link', '<a href="' . wp_nonce_url( $bp->displayed_user->domain . $bp->twire->slug . '/reply/' . $twire_posts_template->twire_post->id, 'bp_twire_reply_link' ) . '">[' . __('Reply', 'bp-twire') . ']</a>' );
		} else {
			echo apply_filters( 'bp_twire_reply_link', '<a href="' . wp_nonce_url( site_url( $bp->{$bp->current_component}->slug . '/' . $uri . '/twire/reply/' . $twire_posts_template->twire_post->id ), 'bp_twire_reply_link' ) . '">[' . __('Reply', 'bp-twire') . ']</a>' );
		}
	}
}

function bp_twire_see_all_link() {
	global $bp;
	
	if ( empty( $bp->current_item ) )
		$uri = $bp->current_action;
	else
		$uri = $bp->current_item;
	
	if ( $bp->current_component == $bp->twire->slug || $bp->current_component == $bp->profile->slug ) {
		echo apply_filters( 'bp_twire_see_all_link', $bp->displayed_user->domain . $bp->twire->slug );
	} else {
		echo apply_filters( 'bp_twire_see_all_link', $bp->root_domain . '/' . $bp->groups->slug . '/' . $uri . '/twire' );
	}
}

function bp_custom_twire_boxes_before() {
	do_action( 'bp_twire_custom_twire_boxes_before' );
}

function bp_custom_twire_boxes_after() {
	do_action( 'bp_twire_custom_twire_boxes_after' );
}


?>
