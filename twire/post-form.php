<div id="twire-post-new">
	<form action="<?php bp_twire_get_action(); ?>" id="twire-post-new-form" method="post">
		<div id="twire-post-new-metadata">
			<?php bp_twire_poster_avatar() ?>
			<?php printf ( __( 'On %1$s %2$s said:', "buddypress" ), bp_twire_poster_date( null, false ), bp_twire_poster_name( false ) ) ?>
			<div id="twire-post-new-metadata-after">
				You have <span id="twireCharsLeft"></span> characters left.
			</div>
		</div>
	
		<div id="twire-post-new-input">
			
			<?php do_action( 'bp_twire_custom_twire_boxes_before' ) ?>
			
			<textarea name="twire-post-textarea" id="twire-post-textarea" wrap="soft"><?php bp_twire_post_prefix() ?></textarea>

			<?php if ( bp_twire_show_email_notify() ) : ?>
				<p><input type="checkbox" name="twire-post-email-notify" id="twire-post-email-notify" value="1" /> <?php _e( 'Notify members via email (will slow down posting)', 'buddypress' ) ?></p>
			<?php endif; ?>
			
			<?php do_action( 'bp_twire_custom_twire_boxes_after' ) ?>
		</div>
			
		<div id="twire-post-new-input-button">
			<input type="submit" name="twire-post-submit" id="twire-post-submit" value="Post &raquo;" />
			
			<?php wp_nonce_field( 'bp_twire_post' ) ?>
			
		</div>
		
		
	</form>
</div>
