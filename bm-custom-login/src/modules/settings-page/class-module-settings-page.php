<?php
/**
 * Plugin settings page
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules;

use Teydea_Studio\Custom_Login\Dependencies\Universal_Modules;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use Teydea_Studio\Custom_Login\Styles;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Settings_Page" class
 */
final class Module_Settings_Page extends Universal_Modules\Module_Settings_Page {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		parent::register();

		// Filter inline data passed to the settings page script.
		add_filter( 'custom_login__settings_page_script_inline_data', [ $this, 'filter_inline_data' ] );

		// Enqueue additional scripts on the settings page.
		add_action( 'custom_login__enqueue_settings_page_scripts', [ $this, 'enqueue_additional_scripts' ] );
	}

	/**
	 * Setup the values of the class properties
	 *
	 * @return void
	 */
	public function setup_class_properties(): void {
		// Define the page title.
		$this->page_title = __( 'WP Custom Login', 'bm-custom-login' );

		// Define the menu title.
		$this->menu_title = __( 'Custom Login', 'bm-custom-login' );

		// Define the list of help & support links.
		$this->help_links = [
			[
				'url'   => sprintf( 'https://wordpress.org/support/plugin/%s/', $this->container->get_slug() ),
				'title' => __( 'Support forum', 'bm-custom-login' ),
			],
			[
				'url'   => 'https://wpcustomlogin.com/contact/?utm_source=WP+Custom+Login',
				'title' => __( 'Contact plugin author', 'bm-custom-login' ),
			],
			[
				'url'   => sprintf( 'https://wordpress.org/plugins/%s/', $this->container->get_slug() ),
				'title' => __( 'Plugin on WordPress.org directory', 'bm-custom-login' ),
			],
			[
				'url'   => 'https://teydeastudio.com/?utm_source=WP+Custom+Login',
				'title' => __( 'Teydea Studio\'s website', 'bm-custom-login' ),
			],
		];

		parent::setup_class_properties();
	}

	/**
	 * Enqueue additional scripts on the settings page
	 *
	 * @return void
	 */
	public function enqueue_additional_scripts(): void {
		// Enqueue the Media Library.
		wp_enqueue_media();
	}

	/**
	 * Filter inline data passed to the settings page script
	 *
	 * @param array<string,mixed> $data Inline data array.
	 *
	 * @return array<string,mixed> Updated inline data array.
	 */
	public function filter_inline_data( array $data ): array {
		// Get preconfigured styles data.
		$preconfigured_styles = ( new Styles( $this->container ) )->get_preconfigured_styles();

		// Adjust the style keys for JS use.
		$data['styles'] = [
			'colorPalettes'    => $preconfigured_styles['color_palettes'],
			'fontFamilies'     => $preconfigured_styles['font_families'],
			'fontSizes'        => $preconfigured_styles['font_sizes'],
			'gradientPalettes' => $preconfigured_styles['gradient_palettes'],
			'shadowPresets'    => $preconfigured_styles['shadow_presets'],
			'spacingPresets'   => $preconfigured_styles['spacing_presets'],
		];

		/**
		 * Get the list of installed languages and translations
		 * for certain strings
		 */
		$languages = Utils\Languages::get_installed_languages( 'core' );
		$tokens    = [
			'Change',
			'Email',
			'Get New Password',
			'Log In',
			'Password',
			'Please enter your username or email address. You will receive an email message with instructions on how to reset your password.',
			'Powered by WordPress',
			'Register',
			'Register For This Site',
			'Remember Me',
			'Save Password',
			'Username or Email Address',
			'Username',
		];

		$translations = [];

		foreach ( $languages as $lang ) {
			foreach ( $tokens as $token ) {
				$translations[ $token ][ Utils\Strings::to_camel_case( $lang ) ] = Utils\Languages::get_single_translation( $token, $lang );
			}
		}

		/**
		 * Check whether anyone can register
		 */
		$anyone_can_register = get_option( 'users_can_register' );

		if ( ! is_bool( $anyone_can_register ) && ! is_int( $anyone_can_register ) && ! is_string( $anyone_can_register ) ) {
			$anyone_can_register = false;
		} else {
			$anyone_can_register = rest_sanitize_boolean( $anyone_can_register );
		}

		/**
		 * Additional context used in the script
		 */
		$data['context'] = [
			// Whether anyone can register.
			'anyoneCanRegister'         => $anyone_can_register,

			// Whether we're in the network admin context.
			'isNetworkAdmin'            => is_network_admin(),

			// List of supported languages.
			'languages'                 => array_map(
				function ( string $language ): string {
					return Utils\Strings::to_camel_case( $language );
				},
				$languages,
			),

			// Whether multiple languages are supported.
			'supportsMultipleLanguages' => 1 < count( $languages ),

			// Current language.
			'currentLocale'             => Utils\Languages::get_current_locale(),

			// Translations for certain strings.
			'translations'              => $translations,

			// Path to the plugin's directory.
			'mainUrl'                   => $this->container->get_main_url(),
		];

		// Return updated data set.
		return $data;
	}
}
