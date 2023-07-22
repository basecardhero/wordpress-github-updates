<?php
/**
 * PHPUnit bootstrap file
 *
 * @since 0.1.0
 */

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

global $_wp_core_dir, $_plugin_dir;

$_tests_dir   = getenv( 'WP_TESTS_DIR' );
$_wp_core_dir = getenv( 'WP_CORE_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! $_wp_core_dir ) {
	$_wp_core_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress';
}

if ( ! $_plugin_dir ) {
	$_plugin_dir = dirname( dirname( __FILE__ ) );
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	throw new Exception( "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" );
}

// Include composer autoload.
require "{$_plugin_dir}/vendor/autoload.php";

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
/**
 * Callback for WordPress 'muplugins_loaded' action.
 *
 * Manually load the plugin being tested.
 *
 * Loads WooCommerce if plugin files exist.
 *
 * @since 0.1.0
 */
function _manually_load_plugin() {
	global $_wp_core_dir, $_plugin_dir;

	define( 'WC_TAX_ROUNDING_MODE', 'auto' );

	if ( file_exists( "{$_wp_core_dir}/wp-content/plugins/woocommerce/woocommerce.php" ) ) {
		require "{$_wp_core_dir}/wp-content/plugins/woocommerce/woocommerce.php";
	}
	require "{$_plugin_dir}/wordpress-github-updates.php";
}

tests_add_filter( 'setup_theme', 'unit_tests_install_woocommerce' );
/**
 * Callback for WordPress 'setup_theme' action.
 *
 * Run WooCommerce uninstall functionalty.
 *
 * If woocommerce files do not exist, function is skipped.
 *
 * @since 0.1.0
 */
function unit_tests_install_woocommerce() {
	global $_wp_core_dir;

	if ( ! file_exists( "{$_wp_core_dir}/wp-content/plugins/woocommerce/uninstall.php" ) ) {
		return;
	}

	define( 'WP_UNINSTALL_PLUGIN', true );
	define( 'WC_REMOVE_ALL_DATA', true );

	include "{$_wp_core_dir}/wp-content/plugins/woocommerce/uninstall.php";

	WC_Install::install();

	$GLOBALS['wp_roles'] = null;
	wp_roles();
}

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";
require "{$_plugin_dir}/tests/trait-woocommerce-helpers.php";
require "{$_plugin_dir}/tests/class-test-case.php";
require "{$_plugin_dir}/tests/class-rest-test-case.php";
