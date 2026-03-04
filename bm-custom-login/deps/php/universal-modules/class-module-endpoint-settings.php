<?php
/**
 * REST API endpoint for getting and updating settings
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Universal_Modules
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Universal_Modules;

use Teydea_Studio\Custom_Login\Settings;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Endpoint_Settings" class
 */
class Module_Endpoint_Settings extends Utils\Module {
	/**
	 * Hold the Settings instance
	 *
	 * @var ?Settings
	 */
	protected ?Settings $settings = null;

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Register endpoints.
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
	}

	/**
	 * Register endpoints
	 *
	 * @return void
	 */
	public function register_endpoints(): void {
		register_rest_route(
			sprintf( '%s/v1', $this->container->get_slug() ),
			'/settings',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_settings' ],

				/**
				 * Ensure that user is logged in and has the required
				 * capability
				 *
				 * @return bool|WP_Error Boolean "true" if user has the permission to process this request, WP_Error otherwise.
				 */
				'permission_callback' => function () {
					$user = new Utils\User( $this->container );

					if ( ! $user->has_managing_permissions() ) {
						return new WP_Error(
							'rest_forbidden',
							__( 'You do not have permission to read settings.', 'bm-custom-login' ),
							[ 'status' => rest_authorization_required_code() ],
						);
					}

					return true;
				},
			],
		);

		register_rest_route(
			sprintf( '%s/v1', $this->container->get_slug() ),
			'/settings',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'save_settings' ],
				'args'                => [
					'nonce'    => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',

						/**
						 * Nonce value validation
						 *
						 * @param string $value Nonce value.
						 *
						 * @return bool Whether the nonce value is valid or not.
						 */
						'validate_callback' => function ( string $value ): bool {
							$nonce = new Utils\Nonce( $this->container, 'save_settings' );
							return false !== wp_verify_nonce( $value, $nonce->get_action() );
						},
					],
					'settings' => [
						'required'          => true,
						'type'              => 'array',

						/**
						 * Settings data sanitization
						 *
						 * @return Settings Instance of a Settings class.
						 */
						'sanitize_callback' => function (): Settings {
							if ( null === $this->settings ) {
								$this->settings = new Settings( $this->container );
							}

							return $this->settings;
						},

						/**
						 * Settings data validation
						 *
						 * @param array<string,array<string,mixed>> $settings Settings array.
						 *
						 * @return true|WP_Error Boolean "true" if a given settings data are valid, instance of WP_Error otherwise.
						 */
						'validate_callback' => function ( array $settings ) {
							if ( ! isset( $settings['data'] ) || ! is_array( $settings['data'] ) ) {
								return new WP_Error(
									'invalid_settings_data',
									__( 'Settings data is missing or invalid.', 'bm-custom-login' ),
								);
							}

							$this->settings = new Settings( $this->container, $settings['data'] ); // @phpstan-ignore-line argument.type

							return $this->settings->has_validation_errors()
								? $this->settings->get_first_validation_error()
								: true;
						},
					],
				],

				/**
				 * Ensure that user is logged in and has the required
				 * capability
				 *
				 * @return bool|WP_Error Boolean "true" if user has the permission to process this request, WP_Error otherwise.
				 */
				'permission_callback' => function () {
					$user = new Utils\User( $this->container );

					if ( ! $user->has_managing_permissions() ) {
						return new WP_Error(
							'rest_forbidden',
							__( 'You do not have permission to save settings.', 'bm-custom-login' ),
							[ 'status' => rest_authorization_required_code() ],
						);
					}

					return true;
				},
			],
		);
	}

	/**
	 * Get plugin settings
	 *
	 * @return WP_Error|WP_REST_Response Instance of WP_REST_Response on success, instance of WP_Error on failure.
	 */
	public function get_settings() {
		$settings = new Settings( $this->container );

		if ( $settings->has_validation_errors() ) {
			return $settings->get_first_validation_error();
		}

		$data = $settings->get_normalized_data();

		if ( null === $data ) {
			return new WP_Error(
				'validation_errors_found',
				__( 'Can\'t get settings data; resolve validation errors first.', 'bm-custom-login' ),
			);
		}

		return new WP_REST_Response(
			[
				'data'      => $data,
				'defaults'  => $settings->get_defaults(),
				'templates' => $settings->get_templates(),
			],
			200,
		);
	}

	/**
	 * Save plugin settings
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return WP_Error|WP_REST_Response Instance of WP_REST_Response on success, instance of WP_Error on failure.
	 */
	public function save_settings( WP_REST_Request $request ) {
		/** @var Settings $settings */
		$settings = $request->get_param( 'settings' );
		$saved    = $settings->save();

		return $saved instanceof WP_Error
			? $saved
			: new WP_REST_Response( [], 200 );
	}
}
