<?php
/**
 * A test case class with REST integration helpers.
 *
 * @since 0.1.0
 */

namespace WordPress_Github_Updates\Tests;

trait WooCommerce_Helpers {
	/**
	 * Create a WooCommerce Simple Product.
	 *
	 * See https://woocommerce.github.io/code-reference/classes/WC-Product-Simple.html
	 *
	 * @param string $name  The product name.
	 * @param float  $price The product price.
	 *
	 * @return \WC_Product_Simple The product instance.
	 */
	protected function create_simple_product( string $name, ?float $price = null ): \WC_Product_Simple {
		$product = new \WC_Product_Simple();
		$product->set_name( $name );

		if ( $price ) {
			$product->set_regular_price( $price );
		}

		$product->save();

		return $product;
	}

	/**
	 * Create a WooCommerce Variable Product.
	 *
	 * See https://woocommerce.github.io/code-reference/classes/WC-Product-Variable.html
	 *
	 * @param string $name The product name.
	 * @param array  $attribute_name_options Array of product attributes.
	 *
	 * @return \WC_Product_Variable The product instance.
	 */
	protected function create_variable_product( string $name, array $attribute_name_options = [] ): \WC_Product_Variable {
		$product = new \WC_Product_Variable();
		$product->set_name( $name );

		foreach ( $attribute_name_options as $name => $options ) {
			$attribute = new \WC_Product_Attribute();
			$attribute->set_name( $name );
			$attribute->set_options( $options );
			$attribute->set_position( 0 );
			$attribute->set_visible( true );
			$attribute->set_variation( true );

			$product->set_attributes( [ $attribute ] );
		}

		$product->save();

		return $product;
	}

	/**
	 * Create a WooCommerce Product Variation.
	 *
	 * See https://woocommerce.github.io/code-reference/classes/WC-Product-Variation.html
	 *
	 * @param int 	$parent_id The parent product id.
	 * @param array $attributes The product attributes.
	 * @param float $price Optional product price. Default 0.
	 *
	 * @return \WC_Product_Variation The product instance.
	 */
	protected function create_product_variation( int $parent_id, array $attributes, ?float $price = 0 ): \WC_Product_Variation {
		$product_variation = new \WC_Product_Variation();
		$product_variation->set_parent_id( $parent_id );
		$product_variation->set_attributes( $attributes );
		$product_variation->set_regular_price( $price );
		$product_variation->save();

		return $product_variation;
	}

	/**
	 * Create an order.
	 *
	 * @param array             $products Array of \WC_Product
	 * @param \WC_Customer|null $customer Optional customer object. Default null.
	 * @param string            $status   Optional order status. Default 'wc-completed'.
	 *
	 * @return \WC_Order
	 */
	protected function create_order( ?\WC_Customer $customer = null, string $status = 'wc-completed' ): \WC_Order {
		$order = \wc_create_order();

		if ( $customer ) {
			$order->set_customer_id( $customer->get_id() );
			$order->set_address( $customer->get_billing(), 'billing' );
			$order->set_address( $customer->get_shipping(), 'shipping' );
		}

		if ( $status ) {
			$order->set_status( $status, 'Order created programmatically' );
		}

		$order->calculate_totals();
		$order->save();

		return $order;
	}

	/**
	 * Create a customer.
	 *
	 * @since 0.1.0
	 *
	 * @param array $data 		   Optional customer data. Default empty array.
	 * @param array $billing_data  Optional billing data. Default empty array.
	 * @param array $shipping_data Optional shipping data. Default empty array.
	 *
	 * @return \WC_Customer A WC_Customer object.
	 */
	protected function create_customer( $data = [], $billing_data = [], $shipping_data = [] ): \WC_Customer {
		$data = $this->generate_customer_data( $data, $billing_data, $shipping_data );

		$id = \wc_create_new_customer( $data['email'], $data['username'] );

		$wc_customer = new \WC_Customer( $id );
		$wc_customer->set_first_name( $data['first_name'] );
		$wc_customer->set_last_name( $data['last_name'] );
		$wc_customer->set_display_name( $data['display_name'] );
		$wc_customer->set_role( $data['role'] );
		$wc_customer->set_date_created( $data['date_created'] );
		$wc_customer->set_date_modified( $data['date_modified'] );
		$wc_customer->set_billing_location( $data['billing']['country'], $data['billing']['state'], $data['billing']['postcode'], $data['billing']['city'] );
		$wc_customer->set_shipping_location( $data['shipping']['country'], $data['shipping']['state'], $data['shipping']['postcode'], $data['shipping']['city'] );
		$wc_customer->set_billing_first_name( $data['billing']['first_name'] );
		$wc_customer->set_billing_last_name( $data['billing']['last_name'] );
		$wc_customer->set_billing_company( $data['billing']['company'] );
		$wc_customer->set_billing_address_1( $data['billing']['address_1'] );
		$wc_customer->set_billing_address_2( $data['billing']['address_2'] );
		$wc_customer->set_billing_email( $data['billing']['email'] );
		$wc_customer->set_billing_phone( $data['billing']['phone'] );
		$wc_customer->set_shipping_first_name( $data['shipping']['first_name'] );
		$wc_customer->set_shipping_last_name( $data['shipping']['last_name'] );
		$wc_customer->set_shipping_company( $data['shipping']['company'] );
		$wc_customer->set_shipping_address_1( $data['shipping']['address_1'] );
		$wc_customer->set_shipping_address_2( $data['shipping']['address_2'] );
		$wc_customer->set_is_paying_customer( $data['is_paying_customer'] );
		$wc_customer->save();

		return $wc_customer;
	}

	/**
	 * Generate data for a WC_Customer.
	 *
	 * Meh. This is messy.
	 *
	 * @since 0.1.0
	 *
	 * @param array $data 		   Optional customer data.
	 * @param array $billing_data  Optional billing data.
	 * @param array $shipping_data Optional shipping data.
	 *
	 * @return array WC_Customer data.
	 */
	protected function generate_customer_data( $data = [], $billing_data = [], $shipping_data = [] ): array {
		$first_name = $this->faker->firstName();
		$last_name  = $this->faker->lastName();
		$email      = $this->faker->email();
		$address_1  = $this->faker->streetAddress();
		$city       = $this->faker->city();
		$post_code  = $this->faker->postcode();
		$state      = $this->faker->stateAbbr();

		$data = \wp_parse_args(
			$data,
			[
				'date_created'       => null,
				'date_modified'      => null,
				'first_name'         => $first_name,
				'last_name'          => $last_name,
				'email'              => $this->faker->unique()->email(),
				'display_name'       => $first_name,
				'role'               => 'customer',
				'username'           => '',
				'is_paying_customer' => false,
			]
		);

		$data['billing'] = \wp_parse_args(
			$billing_data,
			[
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'company'    => '',
				'address_1'  => $address_1,
				'address_2'  => '',
				'city'       => $city,
				'postcode'   => $post_code,
				'country'    => 'United States',
				'state'      => $state,
				'email'      => $email,
				'phone'      => $this->faker->phoneNumber(),
			]
		);

		$data['shipping'] = \wp_parse_args(
			$shipping_data,
			[
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'company'    => '',
				'address_1'  => $address_1,
				'address_2'  => '',
				'city'       => $city,
				'postcode'   => $post_code,
				'country'    => 'United States',
				'state'      => $state,
			]
		);

		return $data;
	}
}
