<?php
/**
 * Plugin updates callbacks.
 *
 * @since 0.1.0
 *
 * @package WordPress_Github_Updates
 */

namespace WordPress_Github_Updates\Updates\Callbacks;

use function WordPress_Github_Updates\Updates\Functions\get_plugin_source_data;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

\add_filter( 'update_plugins_wordpress-github-updates', __NAMESPACE__ . '\check_plugin_update', 10, 4 );
/**
 * Callback for WordPress 'update_plugins_{$hostname}' filter.
 *
 * Allows for 3rd party hosted plugins to hook into the WordPress
 * plugin update api.
 *
 * @since 0.2.0
 *
 * @param array|false $update      The update array of false.
 * @param array       $plugin_data Plugin headers.
 * @param string      $plugin_file Plugin filename.
 * @param string[]    $locales     Installed locales to look up translations for.
 *
 * @return array|false The update array or false.
 */
function check_plugin_update( $update, $plugin_data, $plugin_file, $locales ) {
	if ( 'wordpress-github-updates/wordpress-github-updates.php' !== $plugin_file ) {
		return $update;
	}

	// var_dump( $plugin_data ); exit;

	$source_data = get_plugin_source_data();

	if ( isset( $source_data['zipball_url'] ) ) {
		$update               = (object) $update;
		$update->slug         = \plugin_basename( WORDPRESS_GITHUB_UPDATES_FILE );
		$update->version      = \str_replace( 'v', '', $source_data['tag_name'] );
		$update->url          = $plugin_data['PluginURI'];
		$update->package      = $source_data['zipball_url'];
		// $update->tested       = '6.2.2'; // $plugin_data['Tested up to'];
		$update->requires_php = $plugin_data['RequiresPHP'];
	}

	// var_dump( $update ); exit;

	return $update;
}
