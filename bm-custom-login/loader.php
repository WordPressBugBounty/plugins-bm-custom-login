<?php
/**
 * Load plugin tokens and dependencies
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login;

use Teydea_Studio\Custom_Login\Dependencies\Universal_Modules;
use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class autoloader
 */
spl_autoload_register(
	/**
	 * Autoload plugin classes
	 *
	 * @param string $class_name Class name.
	 *
	 * @return void
	 */
	function ( string $class_name ): void {
		$class_map = include __DIR__ . '/classmap.php';

		if ( isset( $class_map[ $class_name ] ) ) {
			require_once __DIR__ . $class_map[ $class_name ];
		}
	},
);

/**
 * Get the plugin container object
 *
 * @return Utils\Plugin Plugin container object.
 */
function get_container(): Utils\Plugin {
	static $plugin = null;

	if ( null === $plugin ) {
		// Construct the plugin object.
		$plugin = new Utils\Plugin();

		$plugin->set_data_prefix( 'custom_login' );
		$plugin->set_main_dir( __DIR__ );
		$plugin->set_name( 'Custom Login' );
		$plugin->set_slug( 'bm-custom-login' );
		$plugin->set_supports_network( true );
		$plugin->set_text_domain( 'bm-custom-login' );
		$plugin->set_version( '3.0.0' );

		$plugin->register_modules(
			[
				Modules\Module_Adjustments::class,
				Modules\Module_Endpoint_Preview::class,
				Modules\Module_Settings_Adopter::class,
				Modules\Module_Settings_Page::class,
				Modules\Module_Markup_Adjustments::class,
				Modules\Module_Miscellaneous::class,
				Modules\Module_Plugin_Upgrade_Action_Link::class,
				Universal_Modules\Module_Endpoint_Settings::class,
				Universal_Modules\Module_Translations::class,
			],
		);
	}

	return $plugin;
}
