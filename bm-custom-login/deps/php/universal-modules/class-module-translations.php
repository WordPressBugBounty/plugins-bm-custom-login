<?php
/**
 * Translations module
 * - load plugin textdomain
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Universal_Modules
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Universal_Modules;

use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Translations" class
 */
class Module_Translations extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Load plugin textdomain.
		add_action( 'init', [ $this, 'load_plugin_textdomain' ], 1 );
	}

	/**
	 * Load plugin textdomain
	 *
	 * @return void
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			$this->container->get_text_domain(),
			false,
			sprintf(
				'%s/languages',
				dirname( $this->container->get_basename() ),
			),
		);
	}
}
