<?php

/**
 * bp_twire_add_js()
 *
 * This function will enqueue the components javascript file, so that you can make
 * use of any javascript you bundle with your component within your interface screens.
 */
function bp_twire_add_js() {
	global $bp;

	if ( $bp->current_component == $bp->twire->slug )
	{
		wp_enqueue_script( 'bp-twire-js-limit', WP_PLUGIN_URL . '/twire/js/jquery.limit-1.2.js' );
		wp_enqueue_script( 'bp-twire-js', WP_PLUGIN_URL . '/twire/js/twire.js' );
	}
}
add_action( 'template_redirect', 'bp_twire_add_js', 1 );

function bp_twire_add_structure_css() {
	/* Enqueue the structure CSS file to give basic positional formatting for components */
    wp_enqueue_style( 'bp-twire-structure', WP_PLUGIN_URL . '/twire/css/structure.css'  );
    $custom_twire_css = get_bloginfo('stylesheet_directory') . "/twire/_inc/css/twire.css";
    $custom_twire_css_file = STYLESHEETPATH . "/twire/_inc/css/twire.css";
    if (file_exists($custom_twire_css_file))
        wp_enqueue_style( 'bp-twire-custom-css', $custom_twire_css );
}
add_action( 'wp_print_styles', 'bp_twire_add_structure_css' );
?>
