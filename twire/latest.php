<?php get_header(); ?>
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
                    <div class="item-list-tabs no-ajax" id="subnav">
					    <ul>
						    <?php bp_get_options_nav() ?>
						    <?php do_action( 'bp_twire_options_nav' ); ?>
					    </ul>
				    </div>
                    <div id="buddypress_twire_ad">
                        <iframe src="http://dynamicendeavorsllc.com/buddypress_twire_ad/" width='664px' height='75px' frameborder='0' scrolling='no'></iframe>
                    </div>
                    <?php if ( function_exists('bp_twire_get_post_list') ) : ?>
                    <?php bp_twire_get_post_list( bp_current_user_id(), bp_word_or_name( __( "Your Twire", 'buddypress' ), __( "%s's Twire", 'buddypress' ), true, false ), bp_word_or_name( __( "No one has posted to your twire yet.", 'buddypress' ), __( "No one has posted to %s's twire yet.", 'buddypress' ), true, false ), bp_profile_twire_can_post() ); ?>
		            <?php endif; ?>

                    <?php do_action('bp_template_content') ?>
            </div>
        </div>
	</div>

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
