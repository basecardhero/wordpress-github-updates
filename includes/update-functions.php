<?php
/**
 * Plugin updates functions.
 *
 * @since 0.1.0
 *
 * @package WordPress_Github_Updates
 */

namespace WordPress_Github_Updates\Updates\Functions;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the plugin remote source data
 *
 * @since 0.2.0
 *
 * @return array|false The remote plugin source data or false.
 */
function get_plugin_source_data() {
	$source_url = 'https://api.github.com/repos/basecardhero/wordpress-github-updates/releases/latest';
	$response   = \wp_remote_get( $source_url );

	if ( 200 !== \wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = \wp_remote_retrieve_body( $response );

	return \json_decode( $body, true );
}
