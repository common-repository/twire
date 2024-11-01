<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1); 
function bp_twire_ajax_get_twire_posts() {
	global $bp;
	?>

	<?php if ( bp_has_twire_posts( $_POST['bp_twire_item_id'], true ) ) : ?>
		<form name="twire-post-list-form" id="twire-post-list-form" action="" method="post">
		
		<?php if ( bp_twire_needs_pagination() ) : ?>
			<div id="twire-count" class="pag-count">
				<?php bp_twire_pagination_count() ?> &nbsp;
				<img id="ajax-loader" src="<?php bp_twire_ajax_loader_src() ?>" height="7" alt="<?php _e( "Loading", "buddypress" ) ?>" style="display: none;" />
			</div>
		
			<div id="twire-pagination" class="pagination-links">
				<?php bp_twire_pagination() ?>
			</div>
		<?php endif; ?>
		
		<ul id="twire-post-list" class="item-list">
		<?php while ( bp_twire_posts() ) : bp_the_twire_post(); ?>
			<li>
				<div class="twire-post-metadata">
					<?php bp_twire_post_author_avatar() ?>
					<?php printf ( __( 'On %1$s %2$s said:', "buddypress" ), bp_twire_post_date( null, false ), bp_twire_post_author_name( false ) ) ?>
					<?php bp_twire_delete_link() ?>
					<?php bp_twire_reply_link() ?>
				</div>
				
				<div class="twire-post-content">
					<?php bp_twire_post_content() ?>
				</div>
			</li>
		<?php endwhile; ?>
		</ul>
	
	<?php else: ?>

		<div id="message" class="info">
			<p><?php bp_twire_no_posts_message() ?></p>
		</div>

	<?php endif;?>
	
	<input type="hidden" name="bp_twire_item_id" id="bp_twire_item_id" value="<?php print $_POST['bp_twire_item_id'] ?>" />
	</form>
	<?php
}
add_action( 'wp_ajax_get_twire_posts', 'bp_twire_ajax_get_twire_posts' );

function bp_twire_ajax_twire_post_count() {
	global $bp;
?>
		<div id="twire-post-new-metadata">
			<?php bp_twire_poster_avatar() ?>
			<?php printf ( __( 'On %1$s %2$s said:', "buddypress" ), bp_twire_poster_date( null, false ), bp_twire_poster_name( false ) ) ?>
			<div id="twire-post-new-metadata-after">
				You have <span id="twireCharsLeft"></span> characters left.
			</div>
		</div>
	
		<div id="twire-post-new-input">
			
			<?php do_action( 'bp_twire_custom_twire_boxes_before' ) ?>

			Total letter Count (Max 140) : <span id="display_count">0</span>			
			<textarea name="twire-post-textarea" id="twire-post-textarea"><?php bp_twire_post_prefix() ?></textarea>

			<?php if ( bp_twire_show_email_notify() ) : ?>
				<p><input type="checkbox" name="twire-post-email-notify" id="twire-post-email-notify" value="1" /> <?php _e( 'Notify members via email (will slow down posting)', 'buddypress' ) ?></p>
			<?php endif; ?>
			
			<?php do_action( 'bp_twire_custom_twire_boxes_after' ) ?>
			
			<input type="submit" name="twire-post-submit" id="twire-post-submit" value="Post &raquo;" />
			
			<?php wp_nonce_field( 'bp_twire_post' ) ?>
			
		</div>
<?php
}

add_action( 'wp_ajax_twire_post_count', 'bp_twire_ajax_twire_post_count');
?>
