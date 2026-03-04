<?php
/**
 * Apply miscellaneous adjustments to the login page
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use Teydea_Studio\Custom_Login\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Miscellaneous" class
 *
 * @phpstan-type Type_Miscellaneous_Config ?array{custom_login_screen_path:string,disable_autocomplete:bool,disable_autofocus:bool,disable_shake_effect:bool,use_custom_login_screen_path:bool}
 */
final class Module_Miscellaneous extends Utils\Module {
	/**
	 * Hold the config during the class lifetime
	 *
	 * @var Type_Miscellaneous_Config Config array, null if couldn't read.
	 */
	protected ?array $config = null;

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Adjust login-specific hooks after the login page is initialized.
		add_action( 'login_init', [ $this, 'on_login_init' ] );

		// Apply markup adjustments.
		add_filter( 'custom_login__markup_adjustments', [ $this, 'apply_markup_adjustments' ], 10, 2 );
	}

	/**
	 * Get the config
	 *
	 * @return Type_Miscellaneous_Config Config array, null if couldn't read.
	 */
	protected function get_config(): ?array {
		if ( null === $this->config ) {
			$settings = new Settings( $this->container );

			// Get the fields group.
			$fields_group = $settings->get_fields_group( 'miscellaneous' );

			if ( null === $fields_group ) {
				return null;
			}

			/** @var array{custom_login_screen_path:string,disable_autocomplete:bool,disable_autofocus:bool,disable_shake_effect:bool,use_custom_login_screen_path:bool} $results */
			$results      = $fields_group->get_all_fields_values();
			$this->config = $results;
		}

		return $this->config;
	}

	/**
	 * Adjust login-specific hooks after the login page is initialized
	 *
	 * @return void
	 */
	public function on_login_init(): void {
		$config = $this->get_config();

		if ( null === $config ) {
			return;
		}

		/**
		 * Maybe disable the autofocus on the login form
		 */
		if ( true === $config['disable_autofocus'] ) {
			add_filter( 'enable_login_autofocus', '__return_false' );
		}

		/**
		 * Maybe disable the shake effect on the login form
		 */
		if ( true === $config['disable_shake_effect'] ) {
			add_filter( 'shake_error_codes', '__return_empty_array' );
		}
	}

	/**
	 * Allow specific adjusters to apply their markup adjustments
	 *
	 * @param DOMDocument $doc   The DOMDocument object.
	 * @param DOMXPath    $xpath The DOMXPath object.
	 *
	 * @return DOMDocument Updated DOMDocument object after applying adjustments.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		$config = $this->get_config();

		/**
		 * Maybe disable the autocomplete on the
		 * login form fields
		 */
		if ( null !== $config && true === $config['disable_autocomplete'] ) {
			$fields = $xpath->query( '//input' );

			if ( false !== $fields && $fields->length > 0 ) {
				/** @var DOMElement $field */
				foreach ( $fields as $field ) {
					if ( $field->hasAttribute( 'autocomplete' ) ) {
						$field->setAttribute( 'autocomplete', 'off' );
					}
				}
			}
		}

		return $doc;
	}
}
