<?php
/**
 * @package   DD_Activate
 * @author    Cindy Leschaud <cindy.leschaud@gmail.com>
 * @license   GPL-2.0+
 * @link      http://designpnod.ch
 * @copyright 2014 DesignPond
 *
 * @wordpress-plugin
 * Plugin Name:       DD_Activate
 * Plugin URI:        DD_Activate
 * Description:       Activate user account with code
 * Version:           1.0.0
 * Author:            Cindy Leschaud
 * Author URI:        https://github.com/DesignPond
 * Text Domain:       dd_activate-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/DesignPond
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/


require_once( plugin_dir_path( __FILE__ ) . 'public/class-dd_activate.php' );

/**
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'DD_Activate', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'DD_Activate', 'deactivate' ) );

/**
 * plugin is loaded
 */
add_action( 'plugins_loaded', array( 'DD_Activate', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/**
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-dd_activate-admin.php' );
	add_action( 'plugins_loaded', array( 'DD_Activate_Admin', 'get_instance' ) );

}
