<?php
/**
 * Settings-page URL helpers
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Settings_Page_URLs" class
 *
 * Stateless URL builders for the admin-area settings page that the
 * `composer-universal-modules` `Module_Settings_Page` registers.
 * Lives at the shared-utilities level (not inside the
 * `Universal_Modules\Settings_Page` namespace) so consumers in
 * unrelated modules — admin notices, dashboard widgets, REST handlers —
 * can build settings-page deep links without reaching into another
 * module's namespace, per the monorepo's module-architecture rule
 * that forbids direct cross-module calls.
 *
 * Assumes the universal `Module_Settings_Page`'s default parent slugs
 * (`options-general.php` for single-site, `settings.php` for network)
 * and its `{slug}-settings-page` page-key convention. Plugins that
 * override either should call the module's own URL builder instead.
 *
 * URLs returned are raw — callers escape per output context
 * (`esc_url()` for HTML attributes, `esc_url_raw()` for storage or
 * for embedding inside a pre-escaped `wp_kses()` payload).
 */
class Settings_Page_URLs {
	/**
	 * Build the settings page's admin page key (`?page=` value / `WP_Screen`
	 * slug) from the plugin slug
	 *
	 * Single source of truth for the `{slug}-settings-page` convention that
	 * the universal `Module_Settings_Page` registers under. The module's own
	 * `get_page_slug()` / `is_settings_page()` delegate here, and cross-module
	 * consumers (admin notices, dashboard widgets) reach the same string
	 * without calling into the settings-page module directly.
	 *
	 * @param Container $container Plugin container.
	 *
	 * @return string Settings-page admin page key.
	 */
	public static function page_slug( Container $container ): string {
		return sprintf( '%s-settings-page', $container->get_slug() );
	}

	/**
	 * Build the absolute URL of the plugin's settings page
	 *
	 * @param Container $container Plugin container.
	 *
	 * @return string Absolute, unescaped settings-page URL.
	 */
	public static function settings_page( Container $container ): string {
		$page_slug = self::page_slug( $container );

		return $container->is_network_enabled()
			? network_admin_url( add_query_arg( [ 'page' => $page_slug ], 'settings.php' ) )
			: admin_url( add_query_arg( [ 'page' => $page_slug ], 'options-general.php' ) );
	}

	/**
	 * Check whether the current admin screen is the plugin's settings page
	 *
	 * Mirrors {@see self::settings_page()} on the predicate side: derives the
	 * same `{slug}-settings-page` key and matches it against the active
	 * `WP_Screen` (network-aware via {@see Screen::is()}). Lets cross-module
	 * consumers gate behaviour on the settings page without reconstructing the
	 * page-key convention or reaching into the settings-page module.
	 *
	 * @param Container $container Plugin container.
	 *
	 * @return bool Whether the current screen is the plugin's settings page.
	 */
	public static function is_current_screen( Container $container ): bool {
		$screen = new Screen( $container );
		return $screen->is( self::page_slug( $container ), 'settings_page' );
	}

	/**
	 * Build the deep-linked URL of a specific settings-page tab
	 *
	 * The `SettingsTabs` React machinery consumes the URL fragment
	 * (`#vendor-defaults`, `#general`, …) to route to the requested
	 * tab on initial render.
	 *
	 * @param Container $container Plugin container.
	 * @param string    $tab_slug  Tab slug (no leading `#`).
	 *
	 * @return string Absolute, unescaped tab-deep-link URL.
	 */
	public static function settings_tab( Container $container, string $tab_slug ): string {
		return sprintf( '%1$s#%2$s', self::settings_page( $container ), $tab_slug );
	}
}
