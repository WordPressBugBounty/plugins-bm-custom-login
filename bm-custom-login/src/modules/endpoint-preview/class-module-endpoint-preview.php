<?php
/**
 * REST API endpoint for login screen preview
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Endpoint_Preview;

use Teydea_Studio\Custom_Login\Dependencies\Utils;
use Teydea_Studio\Custom_Login\Settings;
use WP_REST_Request;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Module_Endpoint_Preview" class
 */
final class Module_Endpoint_Preview extends Utils\Module {
	/**
	 * Shared managing-permissions gate
	 */
	use Utils\With_REST_Helpers;

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
						'required'          => true,
						'type'              => 'object',
						'sanitize_callback' => function ( $value ) {
							return is_array( $value ) ? $value : [];
						},
						'validate_callback' => function ( $value ) {
							return is_array( $value );
						},
					],
				],
				'permission_callback' => $this->get_managing_permissions_callback(
					__( 'You do not have permission to generate a preview.', 'bm-custom-login' ),
				),
			],
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

		/**
		 * Add custom JS script to disable all click events. Printed via core's
		 * helper so the inline script participates in any Content-Security-Policy
		 * nonce (applied through the "wp_inline_script_attributes" filter).
		 */
		add_action(
			'login_head',
			function () {
				wp_print_inline_script_tag(
					'document.addEventListener("click",function(event){event.stopPropagation();event.preventDefault();},true);',
				);
			},
		);

		/**
		 * Inline all local stylesheets into <style> tags.
		 *
		 * The preview iframe uses sandbox="allow-scripts" without
		 * allow-same-origin, giving it a null origin. External
		 * <link rel="stylesheet"> tags cannot load from a null
		 * origin, so we read local CSS files and inline them.
		 */
		add_filter(
			'style_loader_tag',
			function ( $tag, $handle, $href, $media ) {
				$file_path = $this->resolve_url_to_path( $href );

				if ( null === $file_path ) {
					return $tag;
				}

				$css = Utils\File::get_contents( $file_path );

				if ( null === $css ) {
					return $tag;
				}

				/**
				 * Resolve relative url() references to absolute URLs so
				 * that images and fonts can still be loaded by the browser.
				 */
				$base_url = dirname( strtok( $href, '?' ) ?: $href ) . '/';
				$css      = preg_replace_callback(
					'/url\(\s*[\'"]?(?!data:|https?:|\/\/|#)([^\'"\)\s]+)[\'"]?\s*\)/i',
					function ( $matches ) use ( $base_url ) {
						$path = $matches[1];

						// Absolute path: prepend site URL.
						if ( 0 === strpos( $path, '/' ) ) {
							return 'url(' . site_url( $path ) . ')';
						}

						// Relative path: prepend base URL of the original stylesheet.
						return 'url(' . $base_url . $path . ')';
					},
					$css,
				) ?? $css;

				return sprintf(
					'<style id="%s-css" media="%s">%s</style>' . "\n",
					esc_attr( $handle ),
					esc_attr( $media ),
					str_ireplace( '</style>', '<\/style>', $css ),
				);
			},
			PHP_INT_MAX,
			4,
		);

		/**
		 * Inline all local scripts into <script> tags.
		 *
		 * Same sandbox restriction as stylesheets: external
		 * <script src> tags cannot load from a null origin.
		 */
		add_filter(
			'script_loader_tag',
			function ( $tag, $handle, $src ) {
				$file_path = $this->resolve_url_to_path( $src );

				if ( null === $file_path ) {
					return $tag;
				}

				$js = Utils\File::get_contents( $file_path );

				if ( null === $js ) {
					return $tag;
				}

				return sprintf(
					'<script id="%s-js">%s</script>' . "\n",
					esc_attr( $handle ),
					str_ireplace( '</script>', '<\/script>', $js ),
				);
			},
			PHP_INT_MAX,
			3,
		);

		// Render the login form markup.
		require_once ABSPATH . 'wp-login.php';

		exit;
	}

	/**
	 * Resolve a URL to a local filesystem path
	 *
	 * Converts WordPress content, includes, and site URLs to their
	 * corresponding local filesystem paths for stylesheet and script
	 * inlining in the preview endpoint.
	 *
	 * @param string $url The URL to resolve.
	 *
	 * @return ?string The local file path, or null if not resolvable.
	 */
	private function resolve_url_to_path( string $url ): ?string {
		// Strip query string and fragment.
		$clean_url = strtok( $url, '?#' );

		if ( false === $clean_url ) {
			return null;
		}

		/**
		 * Map URL prefixes to filesystem paths, ordered from most
		 * specific to least specific.
		 */
		$mappings = [
			content_url( '/' )  => WP_CONTENT_DIR . '/',
			includes_url( '/' ) => ABSPATH . WPINC . '/',
			site_url( '/' )     => rtrim( ABSPATH, '/' ) . '/',
		];

		foreach ( $mappings as $url_prefix => $fs_prefix ) {
			if ( 0 === strpos( $clean_url, $url_prefix ) ) {
				$file_path = $fs_prefix . substr( $clean_url, strlen( $url_prefix ) );

				/**
				 * Resolve to canonical path and verify it stays within
				 * the allowed directory to prevent path traversal.
				 */
				$real_path   = realpath( $file_path );
				$real_prefix = realpath( $fs_prefix );

				if ( false === $real_path || false === $real_prefix ) {
					return null;
				}

				if ( 0 !== strpos( $real_path, rtrim( $real_prefix, '/' ) . '/' ) ) {
					return null;
				}

				return is_file( $real_path ) ? $real_path : null;
			}
		}

		return null;
	}
}
