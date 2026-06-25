<?php
/**
 * REST API endpoint for login screen preview
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Endpoint_Preview;

use Teydea_Studio\Custom_Login\Dependencies\Utils;
use Teydea_Studio\Custom_Login\Modules\Endpoint_Preview\Internal\Asset_Inliner;
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
		 * Disable script/style concatenation for this request.
		 *
		 * With concatenation on, WordPress emits core assets through the
		 * "load-scripts.php" / "load-styles.php" endpoints, which are not
		 * static files and so cannot be inlined below (and cannot load from
		 * the null-origin iframe). Disabling it makes each asset print as an
		 * individual file URL that the inliner can resolve and embed.
		 */
		global $concatenate_scripts, $compress_scripts, $compress_css;
		$concatenate_scripts = false; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$compress_scripts    = false; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$compress_css        = false; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

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
				$file_path = Asset_Inliner::resolve_url_to_path( $href );

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
				$css = Asset_Inliner::absolutize_css_urls( $css, $href );

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
		 *
		 * For a script with a delayed (defer/async) strategy, WordPress
		 * folds the handle's "before" / "after" inline scripts (added via
		 * wp_add_inline_script(), e.g. a script's localized data) into the
		 * very tag passed to this filter, so replacing the tag wholesale
		 * would otherwise drop them — and with them any data the script
		 * needs to run (which would never execute from the null origin).
		 */
		add_filter(
			'script_loader_tag',
			function ( $tag, $handle, $src ) {
				$file_path = Asset_Inliner::resolve_url_to_path( $src );

				if ( null === $file_path ) {
					return $tag;
				}

				$js = Utils\File::get_contents( $file_path );

				if ( null === $js ) {
					return $tag;
				}

				$scripts = wp_scripts();
				$before  = $scripts->get_inline_script_data( $handle, 'before' );
				$after   = $scripts->get_inline_script_data( $handle, 'after' );

				$output = '';

				/**
				 * Re-emit the script's translation bootstrap (set via
				 * wp_set_script_translations()) ahead of everything else.
				 * WordPress prints it separately from the tag, so rebuilding
				 * the tag here would otherwise drop it and leave the script's
				 * i18n broken inside the null-origin preview. print_translations()
				 * returns the bare JS, so it is wrapped in a script tag here.
				 */
				$translations = $scripts->print_translations( $handle, false );

				if ( is_string( $translations ) && '' !== $translations ) {
					$output .= wp_get_inline_script_tag(
						str_ireplace( '</script>', '<\/script>', $translations ),
						[ 'id' => $handle . '-js-translations' ],
					) . "\n";
				}

				/**
				 * Build the tags via wp_get_inline_script_tag() so the inline
				 * scripts pick up any inline-script attributes (e.g. a
				 * Content-Security-Policy nonce) consistently, the same way
				 * the click-disable script above does.
				 */
				if ( '' !== $before ) {
					$output .= wp_get_inline_script_tag(
						str_ireplace( '</script>', '<\/script>', $before ),
						[ 'id' => $handle . '-js-before' ],
					) . "\n";
				}

				$output .= wp_get_inline_script_tag(
					str_ireplace( '</script>', '<\/script>', $js ),
					[ 'id' => $handle . '-js' ],
				) . "\n";

				if ( '' !== $after ) {
					$output .= wp_get_inline_script_tag(
						str_ireplace( '</script>', '<\/script>', $after ),
						[ 'id' => $handle . '-js-after' ],
					) . "\n";
				}

				return $output;
			},
			PHP_INT_MAX,
			3,
		);

		/**
		 * Render the login form markup, capturing it so that locally
		 * resolvable images can be inlined as data URIs before output.
		 *
		 * The preview iframe uses sandbox="allow-scripts" without
		 * allow-same-origin, giving it a null origin. Images referenced
		 * by URL (logo <img> tags, background-image styles, url() refs in
		 * inlined stylesheets) cannot load from a null origin — most
		 * visibly under WordPress Playground, where requests are served by
		 * a Service Worker that does not control the opaque-origin iframe.
		 * Inlining them as data URIs makes the preview self-contained, the
		 * same way local CSS and JS are already inlined above.
		 */
		ob_start();
		require_once ABSPATH . 'wp-login.php';
		$html = Utils\Type::ensure_string( ob_get_clean() );

		echo Asset_Inliner::inline_local_images( $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output is the wp-login.php markup with locally resolvable images replaced by data URIs; the surrounding markup is already escaped at its source.

		exit;
	}
}
