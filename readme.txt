=== Simcast ===
Contributors: openchamp
Donate link: https://erickar.be/contact
Tags: simplecast, podcasting, podcast, podcast player
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 4.0
Tested up to: 5.9.0
Stable tag: 1.0.1
Requires PHP: 7.0

A plugin that connects your WordPress website to your Simplecast podcast hosting account. Displays your most recent podcast episodes and their show notes. Optionally embeds the Simplecast player into your pages as well. This plugin has no affiliation with Simplecast.

== Description ==

A plugin that connects your WordPress website to your Simplecast podcast hosting account. Displays your most recent podcast episodes and their show notes. Optionally embeds the Simplecast player into your pages as well. This plugin has no affiliation with Simplecast. **NOTICE: The lastest version of this plugin has breaking changes if you are still using V1 of the SimpleCast API.**

== Installation ==

=== From within WordPress ===

1. Visit 'Plugins > Add New'
1. Search for 'Simplecast'
1. Activate Simcast from your Plugins page.
1. Go to "after activation" below.

=== Manually ===

1. Upload the `simcast` folder to the `/wp-content/plugins/` directory
1. Activate the Simcast plugin through the 'Plugins' menu in WordPress
1. Go to "after activation" below.

=== After activation ===

1. You will see a new tab under plugins called Simcast
1. Enter your API Key and Show ID from your Simplecast account.
1. Use the [simcast] shortcode to display your podcast episodes on any page or post.

== Frequently Asked Questions ==

= Do you support Simplecast V2? =

Yes, that is the only version of the API we support.

= Do I need a Simplecast account in order to use this plugin? =
 
Yes, you sure do.

= What if I have hundreds of episodes? =
 
You can add this to your shortcode: limit="10" to just show 10 episodes. Currently the plugin only imports your 30 most recent episodes. If you need more than that please contact me.

== Screenshots ==


== Changelog ==

= 1.0.1 =
- Updated a fatal PHP error when activating the plugin. 
- Modified the settings page helper text.


= 1.0.0 =
Breaking changes! We no longer support Simplecast V1 API. If you haven't upgraded your Simplecast account, then this plugin will not work. Made various other improvements and accommodations for the new Simplecast API. Also - clear your cache to reflect the new updates.

= 0.2.2 =
Removed podcasts in draft mode from being visible.

= 0.2.1 =
Re-added the ability to add links directly to the episode.

= 0.2.0 =
Support for API V2 is here! Couple of notes: because the new API change, the link to view the full episode is disabled in this version (support for that is coming soon). Control over the amount of episodes shown and pagination is coming soon too. This had to be reconfigured due to the new API as well.

= 0.1.4 =
Small bug fixes.

= 0.1.3 =
Two new shortcode attributes: hide_player and link_text. hide_player optionally hides the embedded player (use "true" or "false") while link_text changes the text that links to your Simplecast page for that podcast.


= 0.1.2 =
Added the ability to limit the amount of podcast episodes shown through the use of the limit attribute.

= 0.1.1 =
Fixed compatible WordPress version.

= 0.1 =
Initial Revision
