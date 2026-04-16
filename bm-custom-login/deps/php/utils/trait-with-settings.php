<?php
/**
 * Set of methods and properties to be inherited by objects that uses Settings class instance
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use Teydea_Studio\Custom_Login\Settings;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "With_Settings" trait
 */
trait With_Settings {
	/**
	 * Hold the Settings instance
	 *
	 * @var ?Settings
	 */
	protected ?Settings $settings = null;

	/**
	 * Get the settings class instance
	 *
	 * @return Settings Settings class instance.
	 */
	public function get_settings(): Settings {
		if ( null === $this->settings ) {
			$this->settings = new Settings( $this->container );
		}

		return $this->settings;
	}

	/**
	 * Reset the settings class instance
	 *
	 * @return void
	 */
	protected function reset_settings(): void {
		$this->settings = null;
	}
}
