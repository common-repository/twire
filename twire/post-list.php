<div class="info-group">
	<h4><?php bp_twire_title() ?> <a href="<?php bp_twire_see_all_link() ?>"><?php _e( "See All &raquo;", "buddypress" ) ?></a></h4>
    <br />
	<?php if ( bp_has_twire_posts( bp_twire_item_id(), bp_twire_can_post() ) ) : ?>
		<?php bp_twire_get_post_form(); ?>		
		
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
			<?php bp_twire_get_post_form(); ?>		
			<p><?php bp_twire_no_posts_message() ?></p>
		</div>

	<?php endif;?>
	
	<input type="hidden" name="bp_twire_item_id" id="bp_twire_item_id" value="<?php bp_twire_item_id(true) ?>" />
	</form>
</div>
