<?php
/**
 * Module class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Module" class
 */
abstract class Module {
	/**
	 * Simplify access to the Settings class instance
	 */
	use With_Settings;

	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Construct the module object
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {}

	/**
	 * Run custom actions on container activation
	 *
	 * @return void
	 */
	public function on_container_activation(): void {}

	/**
	 * Run custom actions on container deactivation
	 *
	 * @return void
	 */
	public function on_container_deactivation(): void {}
}
