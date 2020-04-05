<?php
/**
 * Plugin Name:       Woocommerce Query Report
 * Description:       An admin page to display a filterable table of woocommerce search queries, use the <code>[wqr]</code> shortcode on a front end page 
 * Version:           2.0.0
 * Author:            Jason Lawton
 * Author URI:        https://jasonlawton.com/wqr
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if ( ! defined( 'WQR_VERSION' ) ) {
	define( 'WQR_VERSION', '2.0.0' );
}

if ( ! defined( 'WQR_NAME' ) ) {
	define( 'WQR_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined( 'WQR_DIR' ) ) {
	define( 'WQR_DIR', WP_PLUGIN_DIR . '/' . WQR_NAME );
}

if ( ! defined( 'WQR_URL' ) ) {
	define( 'WQR_URL', WP_PLUGIN_URL . '/' . WQR_NAME );
}

/**
 * Report via Shortcode
 *
 * @since 1.0.0
 */
if ( file_exists( WQR_DIR . '/shortcode/shortcode-report.php' ) ) {
	require_once( WQR_DIR . '/shortcode/shortcode-report.php' );
}

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

/**
 * Report via Admin page
 */
add_action('admin_menu', 'jhl_wqr_admin_page');
function jhl_wqr_admin_page() {
    $page_title = 'Woocommerce Query Report';
    $menu_title = 'Woocommerce Query Report';
    $capability = 'edit_posts';
    $menu_slug  = 'jhl_wqr';
    $function   = 'jhl_wqr_page_display';
    $icon_url   = 'dashicons-list-view';
    $position   = 58;

    add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
}

function jhl_wqr_page_display() {
	require_once( 'pages/options_page.php' );
}