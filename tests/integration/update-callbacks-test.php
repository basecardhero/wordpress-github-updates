<?php
/**
 * Update callbacks tests.
 *
 * @since 0.1.0
 */

namespace WordPress_Github_Updates\Tests\Update_Callbacks;

use WordPress_Github_Updates\Tests\Test_Case;
use function WordPress_Github_Updates\Updates\Callbacks\check_plugin_update;

/**
 * Update callbacks tests.
 *
 * @since 0.1.0
 */
class Update_Callbacks_Test extends Test_Case {
	/** @test */
	public function update_plugins_wordpress_github_updates_has_a_callback() {
		$has_action = \has_action(
			'update_plugins_wordpress-github-updates',
			'WordPress_Github_Updates\Updates\Callbacks\check_plugin_update'
		);

		$this->assertEquals( 10, $has_action );
	}

	/** @test */
	public function update_plugins_wordpress_github_updates_will_return_the_update_object() {
		$this->mock_http_request(
			function ( $response, $parsed_args, $url ) {
				return [
					'response' => [
						'code' => 200,
					],
					'body'     => \wp_json_encode(
						[
							'tag_name' => '0.2.0',
							'assets'   => [
								[
									'state'                => 'uploaded',
									'content_type'         => 'application/zip',
									'browser_download_url' => 'https://example.com/the-file.zip',
								],
							],
						]
					),
				];
			}
		);

		$plugin_data = [
			'PluginURI'   => 'wordpress-github-updates',
			'RequiresPHP' => '7.4',
		];
		$plugin_file = 'wordpress-github-updates/wordpress-github-updates.php';

		$update = check_plugin_update( false, $plugin_data, $plugin_file, [] );

		$this->assertEquals( 'wordpress-github-updates', $update->id );
		$this->assertEquals( false, $update->scalar );
		$this->assertEquals( \plugin_basename( WORDPRESS_GITHUB_UPDATES_FILE ), $update->slug );
		$this->assertEquals( '0.2.0', $update->version );
		$this->assertEquals( 'wordpress-github-updates', $update->url );
		$this->assertEquals( 'https://example.com/the-file.zip', $update->package );
	}

	/** @test */
	public function update_plugins_wordpress_github_updates_will_return_false_if_plugin_file_not_match() {
		$plugin_file = 'not-the/path-to-file.php';

		$update = check_plugin_update( false, [], $plugin_file, [] );

		$this->assertFalse( $update );
	}

	/** @test */
	public function update_plugins_wordpress_github_updates_will_return_false_if_plugin_release_is_empty() {
		$this->mock_http_request(
			function ( $response, $parsed_args, $url ) {
				return [
					'response' => [
						'code' => 401,
					],
					'body'     => \wp_json_encode( [] ),
				];
			}
		);

		$plugin_file = 'wordpress-github-updates/wordpress-github-updates.php';

		$update = check_plugin_update( false, [], $plugin_file, [] );

		$this->assertFalse( $update );
	}

	/** @test */
	public function update_plugins_wordpress_github_updates_will_return_false_if_plugin_release_assets_is_invalid() {
		$this->mock_http_request(
			function ( $response, $parsed_args, $url ) {
				return [
					'response' => [
						'code' => 200,
					],
					'body'     => \wp_json_encode(
						[
							'tag_name' => '0.2.0',
							'assets'   => [],
						]
					),
				];
			}
		);

		$plugin_file = 'wordpress-github-updates/wordpress-github-updates.php';

		$update = check_plugin_update( false, [], $plugin_file, [] );

		$this->assertFalse( $update );
	}
}
