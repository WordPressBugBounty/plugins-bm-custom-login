<?php
/**
 * Plugin Name: WP Custom Login
 * Plugin URI: https://wpcustomlogin.com/?utm_source=WP+Custom+Login
 * Description: Customize the WordPress login screen quickly and easily.
 * Version: 3.0.0
 * Text Domain: bm-custom-login
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.6
 * Tested up to: 6.9
 * Author: Teydea Studio
 * Author URI: https://teydeastudio.com/?utm_source=WP+Custom+Login
 * Network: true
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Require loader
 */
require_once __DIR__ . '/loader.php';

/**
 * Initialize the plugin
 */
add_action(
	'plugins_loaded',
	function (): void {
		// Skip loading the plugin if its PRO version is enabled.
		if ( defined( 'CUSTOM_LOGIN_PRO' ) ) {
			return;
		}

		get_container()->init();
	},
);

// Note whether the plugin is activated network-wide.
add_action(
	sprintf( 'activate_%s', get_container()->get_basename() ),
	/**
	 * Note whether the plugin is being activated network-wide
	 *
	 * @param bool $network_wide Whether the plugin is being activated for all sites in the network or just the current site.
	 */
	function ( bool $network_wide ) {
		// Skip loading the plugin if its PRO version is enabled.
		if ( defined( 'CUSTOM_LOGIN_PRO' ) ) {
			return;
		}

		// Note the network-wide activation status.
		get_container()->note_network_wide_activation_status( $network_wide );
	},
	1,
);

/**
 * Handle the plugin's activation hook
 */
register_activation_hook(
	__FILE__,
	function (): void {
		// Skip loading the plugin if its PRO version is enabled.
		if ( defined( 'CUSTOM_LOGIN_PRO' ) ) {
			return;
		}

		get_container()->on_activation();
	},
);

/**
 * Handle the plugin's deactivation hook
 */
register_deactivation_hook(
	__FILE__,
	function (): void {
		// Skip loading the plugin if its PRO version is enabled.
		if ( defined( 'CUSTOM_LOGIN_PRO' ) ) {
			return;
		}

		get_container()->on_deactivation();
	},
);
