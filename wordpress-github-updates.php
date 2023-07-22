<?php
/**
 * Plugin Name: WordPress Github Updates
 * Description: A plugin to test hosting plugins on Github.
 * Plugin URI: https://basecardhero.com
 * Author: BaseCardHero
 * Author URI: https://basecardhero.com
 * Version: 0.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: wordpress-github-updates
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: wordpress-github-updates
 *
 * @package WordPress_Github_Updates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WORDPRESS_GITHUB_UPDATES_FILE', __FILE__ );
define( 'WORDPRESS_GITHUB_UPDATES_DIRECTORY', dirname( WORDPRESS_GITHUB_UPDATES_FILE ) );

require_once WORDPRESS_GITHUB_UPDATES_DIRECTORY . '/includes/functions.php';
require_once WORDPRESS_GITHUB_UPDATES_DIRECTORY . '/includes/update-functions.php';
require_once WORDPRESS_GITHUB_UPDATES_DIRECTORY . '/includes/callbacks.php';
require_once WORDPRESS_GITHUB_UPDATES_DIRECTORY . '/includes/update-callbacks.php';
