<?php
/**
 * Plugin updates callbacks.
 *
 * @since 0.2.0
 *
 * @package WordPress_Github_Updates
 */

namespace WordPress_Github_Updates\Updates\Callbacks;

use function WordPress_Github_Updates\Updates\Functions\get_plugin_release;
use function WordPress_Github_Updates\Updates\Functions\get_plugin_release_asset;

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
 * See https://make.wordpress.org/core/2021/06/29/introducing-update-uri-plugin-header-in-wordpress-5-8/
 * See https://developer.wordpress.org/reference/hooks/update_plugins_hostname/
 *
 * @since 0.2.0
 *
 * @param array|false $update      The update array of false.
 * @param array       $plugin_data Plugin headers.
 * @param string      $plugin_file Plugin filename.
 * @param string[]    $locales     Installed locales to look up translations for.
 *
 * @return object|false The update array or false.
 */
function check_plugin_update( $update, $plugin_data, $plugin_file, $locales ) {
	if ( 'wordpress-github-updates/wordpress-github-updates.php' !== $plugin_file ) {
		return $update;
	}

	$plugin_release = get_plugin_release();

	if ( empty( $plugin_release ) ) {
		return $update;
	}

	$asset = get_plugin_release_asset( $plugin_release );

	if ( $asset ) {
		$update          = (object) $update;
		$update->id      = $plugin_data['PluginURI'];
		$update->slug    = \plugin_basename( WORDPRESS_GITHUB_UPDATES_FILE );
		$update->version = \str_replace( 'v', '', $plugin_release['tag_name'] );
		$update->url     = $plugin_data['PluginURI'];
		$update->package = $asset['browser_download_url'];
	}

	return $update;
}
