<?php
/**
 * Base test case class.
 *
 * @since 0.1.0
 */

namespace WordPress_Github_Updates\Tests;

/**
 * Base class for tests.
 *
 * @since 0.1.0
 */
class Test_Case extends \WP_UnitTestCase {
	/**
	 * The faker instance.
	 *
	 * @link https://fakerphp.github.io/
	 *
	 * @since 0.1.0
	 *
	 * @var \Faker\Generator
	 */
	protected $faker;

	/**
	 * Override of parent::set_up().
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->faker = \Faker\Factory::create();
	}

	/**
	 * Override of parent::tear_down().
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function tear_down() {
		parent::tear_down();

		$this->faker = null;
	}

	/**
	 * Create a WP_User with 'administrator' role.
	 *
	 * @since 0.1.0
	 *
	 * @param string $role_or_capability Optional desired WordPress role or capability.
	 * @param array  $args 				 Optional WP_User args.
	 *
	 * @return WP_User A WP_User.
	 */
	protected function create_admin_user( $args = [] ) {
		return $this->create_user( 'administrator', $args );
	}

	/**
	 * Create a WP_User.
	 *
	 * @since 0.1.0
	 *
	 * @param string $role_or_capability Optional desired WordPress role or capability.
	 * @param array  $args 				 Optional WP_User args.
	 *
	 * @return WP_User A WP_User.
	 */
	protected function create_user( $role_or_capability = '', $args = [] ) {
		$wp_user = $this->factory->user->create_and_get( $args );

		if ( $role_or_capability ) {
			$wp_user->set_role( $role_or_capability );
		}

		return $wp_user;
	}

	/**
	 * Log out a user within the test scope.
	 *
	 * @uses wp_set_current_user()
	 *
	 * @since 0.1.0
	 *
	 * @return $this The current instance.
	 */
	protected function log_out() {
		return $this->as_user( 0 );
	}

	/**
	 * Set the current WordPress user.
	 *
	 * Alias for Test_Case::as_user().
	 *
	 * @since 0.1.0
	 *
	 * @param WP_User|int $wp_user A WP_User object or user ID.
	 *
	 * @return $this The current instance.
	 */
	protected function log_in( $wp_user ) {
		return $this->as_user( $wp_user );
	}

	/**
	 * Set the current WordPress admin user.
	 *
	 * @uses wp_set_current_user()
	 *
	 * @since 0.1.0
	 *
	 * @param WP_User|int $wp_user A WP_User object or user ID.
	 *
	 * @return $this The current instance.
	 */
	protected function as_admin( $wp_user = null ) {
		if ( \is_null( $wp_user ) ) {
			$wp_user = $this->create_admin_user();
		}

		return $this->as_user( $wp_user );
	}

	/**
	 * Set the current WordPress user.
	 *
	 * @uses wp_set_current_user()
	 *
	 * @since 0.1.0
	 *
	 * @param WP_User|int $wp_user A WP_User object or user ID.
	 *
	 * @return $this The current instance.
	 */
	protected function as_user( $wp_user = null ) {
		if ( \is_null( $wp_user ) ) {
			$wp_user = $this->create_user();
		}

		$user_id = isset( $wp_user->ID )
			? $wp_user->ID
			: $wp_user;

		\wp_set_current_user( $user_id );

		return $this;
	}

	/**
	 * Create a WP_Post with the post factory.
	 *
	 * @since 0.1.0
	 *
	 * @param array $data Optional post arguments.
	 *
	 * @return \WP_Post A WP_Post object.
	 */
	protected function create_post( $data = [] ) {
		return $this->factory->post->create_and_get( $data );
	}

	/**
	 * Mock a http request.
	 *
	 * Hooks into 'pre_http_request' filter.
	 *
	 * https://developer.wordpress.org/reference/hooks/pre_http_request/
	 *
	 * @since 0.2.0
	 *
	 * @param callback $callback The callback function.
	 */
	protected function mock_http_request( $callback ) {
		\add_filter( 'pre_http_request', $callback, 10, 3 );
	}
}
