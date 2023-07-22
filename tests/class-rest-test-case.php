<?php
/**
 * A test case class with REST integration helpers.
 *
 * @since 0.1.0
 */

namespace WordPress_Github_Updates\Tests;

/**
 * Class for handling REST testing.
 *
 * @since 0.1.0
 */
class Rest_Test_Case extends Test_Case {
	/**
	 * WP Rest Server.
	 *
	 * @since 0.1.0
	 *
	 * @var \WP_REST_Server
	 */
	protected $server;

	/**
	 * Override of parent::set_up().
	 *
	 * @since 0.1.0
	 *
	 * @global $wp_rest_server WP_REST_Server
	 */
	public function set_up() {
		parent::set_up();

		global $wp_rest_server;

		$this->server = $wp_rest_server = new \WP_REST_Server();

		\do_action( 'rest_api_init' );
	}

	/**
	 * Override of parent::tear_down().
	 *
	 * @since 0.1.0
	 *
	 * @global $wp_rest_server WP_REST_Server
	 */
	public function tear_down() {
		parent::tear_down();

		global $wp_rest_server;

		$this->server = $wp_rest_server = null;
	}

	/**
	 * Send a json request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $method  Optional Http method.
	 * @param string $route   Optional Route endpoint.
	 * @param array  $body    Optional attributes.
	 * @param array  $headers Optional headers.
	 *
	 * @return \WP_REST_Response A response object.
	 */
	protected function json( $method, $route, $data = [], $headers = [] ) {
		$data 	 = ! empty( $data ) ? \wp_json_encode( $data ) : null;
		$headers = \array_merge( [ 'Content-Type' => 'application/json' ], $headers );

		return $this->create_and_send_request( $method, $route, $data, $headers );
	}

	/**
	 * Perform a GET http request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $route  Optional Route endpoint.
	 * @param array  $params Optional query string parameters.
	 * @param array  $headers Optional headers.
	 *
	 * @return \WP_REST_Response A response object.
	 */
	protected function get( $route = '', $params = [], $headers = [] ) {
		return $this->create_and_send_request( 'GET', $route, $params, $headers );
	}

	/**
	 * Perform a POST http request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $route Optional Route endpoint.
	 * @param array  $body  Optional attributes.
	 * @param array  $headers Optional headers.
	 *
	 * @return \WP_REST_Response A response object.
	 */
	protected function post( $route = '', $body = [], $headers = [] ) {
		return $this->create_and_send_request( 'POST', $route, $body, $headers );
	}

	/**
	 * Perform a PUT http request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $route Optional Route endpoint.
	 * @param array  $body  Optional attributes.
	 * @param array  $headers Optional headers.
	 *
	 * @return \WP_REST_Response A response object.
	 */
	protected function put( $route = '', $body = [] ) {
		return $this->create_and_send_request( 'PUT', $route, $body );
	}

	/**
	 * Perform a DELETE http request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $route Optional Route endpoint.
	 * @param array  $body  Optional attributes.
	 * @param array  $headers Optional headers.
	 *
	 * @return \WP_REST_Response A response object.
	 */
	protected function delete( $route = '', $body = [], $headers = [] ) {
		return $this->create_and_send_request( 'DELETE', $route, $body, $headers );
	}

	/**
	 * Create a http request and dispatch it to the server.
	 *
	 * @since 0.1.0
	 *
	 * @param string $method Optional Http method.
	 * @param string $route  Optional Route endpoint.
	 * @param array  $body   Optional attributes.
	 * @param array  $headers Optional headers.
	 *
	 * @return \WP_REST_Response A response object.
	 */
	protected function create_and_send_request( $method = '', $route = '', $body = [], $headers = [] ) {
		return $this->send_request(
			$this->create_request( $method, $route, $body, $headers )
		);
	}

	/**
	 * Create a WP_REST_Request.
	 *
	 * @since 0.1.0
	 *
	 * @param string $method Optional Http method.
	 * @param string $route  Optional Route endpoint.
	 * @param array  $body   Optional attributes.
	 * @param array  $headers Optional headers.
	 *
	 * @return \WP_REST_Request
	 */
	protected function create_request( $method = '', $route = '', $body = [], $headers = [] ) {
		$request = new \WP_REST_Request( $method, $route );

		if ( 'GET' !== \strtoupper( $method ) ) {
			$request->set_body( $body );
		} else {
			$request->set_query_params( $body );
		}

		if ( ! empty( $headers ) ) {
			$request->set_headers( $headers );
		}

		return $request;
	}

	/**
	 * Dispatch a request to the server.
	 *
	 * @since 0.1.0
	 *
	 * @param \WP_REST_Request $request A request object.
	 *
	 * @return \WP_REST_Response A response object.
	 */
	protected function send_request( \WP_REST_Request $request ) {
		return $this->server->dispatch( $request );
	}

	/**
	 * Assert as WP_REST_Response status code.
	 *
	 * @since 0.1.0
	 *
	 * @param integer           $code     The response status code.
	 * @param \WP_REST_Response $response A WP_REST_Response object.
	 *
	 * @return \WP_Rest_Test_Case The current instance.
	 */
	protected function assertResponseCode( $code, \WP_REST_Response $response ) {
		$this->assertEquals( $code, $response->get_status() );

		return $this;
	}

	/**
	 * Assert a WP_REST_Response to have a status of 200.
	 *
	 * @since 0.1.0
	 *
	 * @param \WP_REST_Response $response A WP_REST_Response object.
	 *
	 * @return \WP_Rest_Test_Case The current instance.
	 */
	protected function assertResponseSuccess( \WP_REST_Response $response ) {
		$this->assertResponseCode( 200, $response );

		return $this;
	}
}
