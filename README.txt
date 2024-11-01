=== Twire ===
Contributors: dfa3272008
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=gpfu08@gmail.com&item_name=Donation&currency_code=USD
Tags: buddypress, twitter, twire, wire
Requires at least: WP 2.9.1, BuddyPress 1.2
Tested up to : WP 2.9.2, BuddyPress 1.2.3
Stable tag: /trunk/

== Description ==

<p>This plugin will extend Buddypress to have Twitter and BuddyPress combined to get a Twire.</p>
You must have 2 things:
<ul>
<li><a href="http://mu.wordpress.org/">Wordpress MU</a></li>
<li><a href="http://buddypress.org">Buddy Press</a></li>
</ul>
<br />
<p>Version 0.8.7</p>
<p>
* Fixed bug to work with wp single as well as wpmu.
</p>

== Changelog ==

= 0.1 =
* Initial version.

= 0.2 =
* Allow nothing for twire prefix and fixed user/pass entry

= 0.3 =
* Save settings works, Display of icon is correct, 
* cross posting works, user list works after logout, pagination works
* no more need for two classes, may work with php 4 and 5 in this version

= 0.4 = 
* Fixed 3 bugs, one in which didn't allow you to log back in! 

= 0.5 = 
* Bundled up to work with trunk version of BuddyPress

= 0.6 =
* Major functionality added.  
* Works with released version of Buddypress as a true plugin
* Sitewide activity, User activity, notifications, character limit (realtime update), and more

= 0.6.1 =
* Added admin case to readme.  Removed whitespace in twitter.php file.  Don't waste time trying to talk to twitter in get_all_posts if not configured.

= 0.7 =
* Added code to not talk to twitter if twire not configured.
* Added twitter communication logic for up to 4 times a minute updating allowed.* Communication logic is configurable in a define in bp-twire-classes.php at the top.
* Fixed issue, where if twitter is down and you try to save it returns error rather than post to local twire.
* Adjusted post button size.  
* Cleaned up some commented out code.  
* Also, put in future if I update the DB test.

= 0.8 =
* Upgraded to work with Buddypress post 1.0.0
* Upgraded to work in a Buddypress sub folder install
* Moved bp-twire to the same folder as bp-twire

= 0.8.1 = 
* Moved twire files to base for auto install of most of twire with wordpress
* You still need to install the twire directory in to the member theme!!!

= 0.8.2 = 
* Updated css for twire image and Buddypress 1.1.3 to be in sync
* Updated help text to match Buddypress version 1.1.3 install

= 0.8.3 = 
* Fixed javascript error when not logged in, don't test for post field.

= 0.8.4 = 
* Rewrote it to work with Buddypress 1.2.

= 0.8.5 = 
* Fixed install bug.
* Fixed layout to be better.

= 0.8.6 = 
* Added bp loader to check for bp existence.

= 0.8.7 = 
* Fixed bug to work with wp single as well as wpmu.

== Installation ==

1.  Extract the plugin to wp-content/plugins:

    A.	You should have the following directories
    	----------------------------------------------

    	wp-content/plugins/twire
    	wp-content/plugins/twire/css
    	wp-content/plugins/twire/images
    	wp-content/plugins/twire/js
    	wp-content/plugins/twire/twire
    

2.  Copy wp-content/plugins/twire/twire directory to your /wp-content/themes/<current theme>/ directory:

    A.	Example
    	---------------------------------------------

    	wp-content/themes/bp_anygig/twire
    	wp-content/themes/bp_anygig/twire/latest.php
    	wp-content/themes/bp_anygig/twire/post-form.php
    	wp-content/themes/bpanygig/twire/post-list.php

3.  Log in and go to your Buddypress profile.  From the profile settings menu configure your Twire settings (username, password, prefix (if you want one)) and that's it.  

Happy Twiring;)

== Frequently Asked Questions ==

= Can I use this with a CPanel installation? =

No, there is a problem with curl and cpanel installs. 

= Can I use this without curl on my server? =

No, the twitter api uses curl to communicate with twitter

=  Can I use php 4? =

No, I think the twitter api was written for php 5+

= I can't twitter to update. =

Please check that you configured the correct username & password for Twire in the Twire settings.

= What if I have more questions? =

Please visit <a href="http://getpaidfrom.us/groups/twire-plugin-support">http://getpaidfrom.us/groups/twire-plugin-support</a> if you have any additional questions.

= How do I get rid of the ads? = 

Please make a donation and we'll remove the ads on your domain remotely.  Visit <a href="http://dynamicendeavorsllc/no_twire_ads">http://dynamicendeavorsllc/no_twire_ads</a>.  Once we receive the donation via paypal we'll contact you with an email asking for your domain, we'll take care of the rest.

Thanks,
Dave:)

== Screenshots ==

1.  This is the Twire edit screen.
2.  This is an example of a Twire Buddypress user notification display.
3.  This is an example email notification of Twire.
4.  This is the Twire configuration window in Buddypress member Settings
5.  This is the Twire noticiation configuration window in Buddypress member Settings.

== License ==

Copyright (c) 2009 http://dynamicendeavorsllc.com Dynamic Endeavors LLC. All rights reserved.

Released under the GPL license
http://www.opensource.org/licenses/gpl-license.php

This is an add-on for WordPress MU 
http://wordpress.org/

**********************************************************************
This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
**********************************************************************
