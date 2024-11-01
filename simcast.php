<?php
/*
   Plugin Name: Simcast
   Plugin URI: 
   Version: 1.0.0
   Author: <a href="https://erickar.be">Erick Arbe</a>
   Description: A plugin that connects your WordPress website to your Simplecast podcast hosting account. Displays your most recent podcast episodes and their show notes. Optionally embeds the Simplecast player into your pages as well. This plugin has no affiliation with Simplecast. **NOTICE: The lastest version of this plugin (1.0.0) has breaking changes if you are still using V1 of the SimpleCast API.**
   Text Domain: simcast
   License: GPLv3
*/
/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$Simcast_minimalRequiredPhpVersion = '6.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function Simcast_noticePhpVersionWrong() {
    global $Simcast_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Simcast" requires a newer version of PHP to be running.',  'simcast').
            '<br/>' . __('Minimal version of PHP required: ', 'simcast') . '<strong>' . $Simcast_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'simcast') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function Simcast_PhpVersionCheck() {
    global $Simcast_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $Simcast_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'Simcast_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function Simcast_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('simcast', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','Simcast_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (Simcast_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('simcast_init.php');
    Simcast_init(__FILE__);
}
