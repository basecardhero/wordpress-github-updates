<?php
/**
 * Update functions tests.
 *
 * @since 0.1.0
 */

namespace WordPress_Github_Updates\Tests\Update_Functions;

use WordPress_Github_Updates\Tests\Test_Case;
use function WordPress_Github_Updates\Updates\Functions\get_remote_release_url;
use function WordPress_Github_Updates\Updates\Functions\get_plugin_release_asset;
use function WordPress_Github_Updates\Updates\Functions\get_plugin_release;

/**
 * Update functions tests.
 *
 * @since 0.1.0
 */
class Update_Functions_Test extends Test_Case {
	/** @test */
	public function get_remote_release_url_will_return_the_url() {
		$this->assertEquals(
			get_remote_release_url(),
			'https://api.github.com/repos/basecardhero/wordpress-github-updates/releases/latest'
		);
	}

	/** @test */
	public function get_remote_release_url_will_apply_the_filter() {
		get_remote_release_url();

		$this->assertEquals( 1, \did_filter( 'wordpress_github_updates_remote_release_url' ) );
	}

	/** @test */
	public function get_plugin_release_asset_will_return_the_release_asset() {
		$release = [
			'assets' => [
				[
					'id'           => 'test_asset',
					'state'        => 'uploaded',
					'content_type' => 'application/zip',
				],
			],
		];

		$asset = get_plugin_release_asset( $release );

		$this->assertEquals( 'test_asset', $asset['id'] );
	}

	/** @test */
	public function get_plugin_release_asset_will_return_false_if_no_assets() {
		$release = [];

		$this->assertFalse( get_plugin_release_asset( $release ) );
	}

	/** @test */
	public function get_plugin_release_asset_will_return_false_if_state_is_not_uploaded() {
		$release = [
			'assets' => [
				[
					'id'           => 'test_asset',
					'state'        => 'not_uploaded',
					'content_type' => 'application/zip',
				],
			],
		];

		$this->assertFalse( get_plugin_release_asset( $release ) );
	}

	/** @test */
	public function get_plugin_release_asset_will_return_false_if_content_type_is_not_application_zip() {
		$release = [
			'assets' => [
				[
					'id'           => 'test_asset',
					'state'        => 'uploaded',
					'content_type' => 'application/text',
				],
			],
		];

		$this->assertFalse( get_plugin_release_asset( $release ) );
	}

	/** @test */
	public function get_plugin_release_will_return_the_release_content() {
		$this->mock_http_request(
			function ( $response, $parsed_args, $url ) {
				$this->assertEquals( 'GET', $parsed_args['method'] );
				$this->assertEquals(
					[
						'Accept'               => 'application/vnd.github+json',
						'X-GitHub-Api-Version' => '2022-11-28',
					],
					$parsed_args['headers']
				);

				$this->assertEquals(
					'https://api.github.com/repos/basecardhero/wordpress-github-updates/releases/latest',
					$url
				);

				return [
					'response' => [
						'code' => 200,
					],
					'body'     => \wp_json_encode( [ 'id' => 'test_id' ] ),
				];
			}
		);

		$release = get_plugin_release();

		$this->assertEquals( 'test_id', $release['id'] );
	}

	/** @test */
	public function get_plugin_release_will_return_the_false_if_release_url_is_empty() {
		\add_filter(
			'wordpress_github_updates_remote_release_url',
			static function( $url ) {
				return '';
			}
		);

		$this->assertFalse( get_plugin_release() );
	}

	/** @test */
	public function get_plugin_release_will_return_the_false_if_response_code_is_not_200() {
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

		$this->assertFalse( get_plugin_release() );
	}
}
