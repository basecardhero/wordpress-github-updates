<?php
/**
 * Plugin updates functions.
 *
 * @since 0.2.0
 *
 * @package WordPress_Github_Updates
 */

namespace WordPress_Github_Updates\Updates\Functions;

if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the latest plugin release from Github.
 *
 * Retrieve the latest release object from Githib REST API.
 *
 * https://docs.github.com/en/rest/releases/releases?apiVersion=2022-11-28#get-the-latest-release
 *
 * @since 0.2.0
 *
 * @return array|false The remote plugin source data or false.
 */
function get_plugin_release() {
	$release_url = get_remote_release_url();

	if ( empty( $release_url ) ) {
		return false;
	}

	$response = \wp_remote_get(
		$release_url,
		[
			'headers' => [
				'Accept'               => 'application/vnd.github+json',
				'X-GitHub-Api-Version' => '2022-11-28',
			],
		]
	);

	if ( 200 !== \wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = \wp_remote_retrieve_body( $response );

	return \json_decode( $body, true );
}

/**
 * Get the first asset from the release.
 *
 * Retrieve the first item within the 'assets' array if exists. The
 * asset item will contain the release zip for the latest plugin release.
 *
 * The asset state must be 'uploaded' and a zip file.
 *
 * https://docs.github.com/en/rest/releases/releases?apiVersion=2022-11-28#get-the-latest-release
 *
 * @since 0.2.0
 *
 * @param array $release The current Github release.
 *
 * @return array|bool The asset array or false if not exists.
 */
function get_plugin_release_asset( array $release ) {
	if ( empty( $release['assets'][0] ) ) {
		return false;
	}

	if ( 'uploaded' !== $release['assets'][0]['state'] ) {
		return false;
	}

	if ( 'application/zip' !== $release['assets'][0]['content_type'] ) {
		return false;
	}

	return $release['assets'][0];
}

/**
 * Get the remote release url.
 *
 * @since 0.2.0
 *
 * @return string The remote release url.
 */
function get_remote_release_url() {
	/**
	 * Filter 'wordpress_github_updates_remote_release_url'.
	 *
	 * @since 0.2.0
	 *
	 * @param string $url The remote release url.
	 */
	return \apply_filters(
		'wordpress_github_updates_remote_release_url',
		'https://api.github.com/repos/basecardhero/wordpress-github-updates/releases/latest'
	);
}
