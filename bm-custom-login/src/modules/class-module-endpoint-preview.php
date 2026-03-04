<?php
/**
 * REST API endpoint for login screen preview
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules;

use Teydea_Studio\Custom_Login\Dependencies\Utils;
use Teydea_Studio\Custom_Login\Settings;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Endpoint_Preview" class
 */
final class Module_Endpoint_Preview extends Utils\Module {
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
		// Register endpoint.
		add_action( 'rest_api_init', [ $this, 'register_endpoint' ] );
	}

	/**
	 * Register endpoint
	 *
	 * @return void
	 */
	public function register_endpoint(): void {
		register_rest_route(
			sprintf( '%s/v1', $this->container->get_slug() ),
			'/preview',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_preview' ],
				'args'                => [
					'settings' => [
						'required' => true,
						'type'     => 'object',
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
							__( 'You do not have permission to generate a preview.', 'bm-custom-login' ),
							[ 'status' => rest_authorization_required_code() ],
						);
					}

					return true;
				},
			]
		);
	}

	/**
	 * Generate the login screen preview
	 *
	 * @param WP_REST_Request $request REST request.
	 *
	 * @return void
	 */
	public function get_preview( WP_REST_Request $request ): void {
		global $user_login, $error; // Required by the wp-login.php file.

		// Get the settings from the request.
		$settings = $request->get_param( 'settings' );

		// Force disable autofocus on the username field to improve the user experience in the preview.
		$settings['miscellaneous']['disableAutofocus'] = true; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible

		// Apply the settings filter to load custom settings.
		add_filter( 'custom_login__settings_loaded', fn () => $settings );

		// Clear authentication context to render as non-logged-in user.
		wp_set_current_user( 0 );
		$user_login = ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		/**
		 * Force the current user to zero when wp-login.php is included.
		 *
		 * PHP_INT_MAX priority ensures this runs last in the filter chain,
		 * unconditionally returning 0 regardless of what any earlier filter
		 * (core cookie auth, REST nonce auth, third-party plugins) resolved.
		 */
		add_filter( 'determine_current_user', '__return_zero', PHP_INT_MAX );

		/**
		 * Force re-authentication mode in wp-login.php.
		 *
		 * When wp-login.php loads, it calls wp_signon() which authenticates
		 * from cookies. If successful, it redirects and exits before the
		 * login form is rendered. Setting 'reauth' makes wp-login.php skip
		 * the redirect for authenticated users. It also clears any stale
		 * error messages, so the preview renders cleanly.
		 */
		$_REQUEST['reauth'] = '1'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		/**
		 * Prevent wp-login.php from sending Set-Cookie headers that would
		 * clear the real user's auth cookies. The reauth mode in wp-login.php
		 * calls wp_clear_auth_cookie(), which would log the user out because
		 * the preview response is delivered via an iframe sharing the same
		 * origin. This filter short-circuits both wp_clear_auth_cookie()
		 * and wp_set_auth_cookie().
		 */
		add_filter( 'send_auth_cookies', '__return_false', PHP_INT_MAX );

		/**
		 * Prevent any redirects during preview rendering.
		 *
		 * This serves as a safety net against wp-login.php redirects
		 * and against the PRO addon's custom login URL module.
		 */
		add_filter( 'wp_redirect', '__return_false', PHP_INT_MAX );

		// Add the Content-Type header.
		header( 'Content-Type: text/html; charset=UTF-8' );

		// Add custom JS script to disable all click events.
		add_action(
			'login_head',
			function () {
				?>
				<script>
					document.addEventListener( 'click', function ( event ) {
						event.stopPropagation();
						event.preventDefault();
					}, true );
				</script>
				<?php
			},
		);

		// Render the login form markup.
		require_once ABSPATH . 'wp-login.php';
		exit;
	}
}
