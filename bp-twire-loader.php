<?php
/*
Plugin Name: Twire
Plugin URI: http://dynamicendeavorsllc.com
Description: This plugin will bring Twitter functionality to your buddypress installation. You must have 2 things:  1.  <a href="http://mu.wordpress.org/">Wordpress MU</a>, 2.  <a href="http://buddypress.org">Buddy Press</a>
Version: 0.8.7
Author: David Aubin
Author URI: http://codewarrior.getpaidfrom.us
Revision Date: 04/21/2010
Requires at least: WPMU 2.9.1, BuddyPress 1.2.0
Tested up to: WPMU 2.9.2, BuddyPress 1.2.3
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=gpfu08@gmail.com&item_name=Donation&currency_code=USD
 */

// Copyright (c) 2008-2010 www.getpaidfrom.us Dynamic Endeavors LLC. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress MU 
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

define ( 'BP_TWIRE_VERSION', '0.8.7' );

function bp_twire_plugin_init() {

    require_once( dirname( __FILE__ ) . '/bp-twire.php' );
}

$buddypress_installed = false;

$active_plugins = get_site_option( 'active_sitewide_plugins' );
if ( isset( $active_plugins['buddypress/bp-loader.php'] ) )
{
    $buddypress_installed = true;
}
if ( false == $buddypress_installed )
{
    $active_plugins = get_option( 'active_plugins' );
    foreach ($active_plugins as $key => $value)
	if ( $value == 'buddypress/bp-loader.php' )
	{
	    $buddypress_installed = true;
	    break;
	}
}
if ( $buddypress_installed )
    bp_twire_plugin_init();
    
?>
