<?php
/**
 * Languages utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Languages utils class
 */
final class Languages {
	/**
	 * Hold the current locale
	 *
	 * @var ?string
	 */
	protected static ?string $locale = null;

	/**
	 * Loaded translations
	 *
	 * @var array<string,array<string,string>> Translations.
	 */
	protected static array $translations = [];

	/**
	 * Get the current locale
	 *
	 * Note: on the login screen we have to read that from $_GET global
	 * variable, as core get_locale() returns site default language
	 * at this point.
	 *
	 * @return string Current locale.
	 */
	public static function get_current_locale(): string {
		if ( null === self::$locale ) {
			/**
			 * Check if the locale is set in the $_GET variable
			 */
			if ( isset( $_GET['wp_lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$lang = sanitize_text_field( Type::ensure_string( wp_unslash( $_GET['wp_lang'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( ! empty( $lang ) && in_array( $lang, self::get_installed_languages( 'core' ), true ) ) {
					self::$locale = $lang;
				}
			}

			/**
			 * If the locale is not set in the $_GET variable,
			 * we can try to read it from the user cookie.
			 */
			if ( null === self::$locale && isset( $_COOKIE['wp_lang'] ) ) {
				$lang = sanitize_text_field( Type::ensure_string( wp_unslash( $_COOKIE['wp_lang'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( ! empty( $lang ) && in_array( $lang, self::get_installed_languages( 'core' ), true ) ) {
					self::$locale = $lang;
				}
			}

			// Still nothing at this point? Use core function as a fallback.
			if ( null === self::$locale ) {
				self::$locale = get_locale();
			}
		}

		return self::$locale;
	}

	/**
	 * Get the list of all languages installed
	 * on a given site
	 *
	 * @param string $type What to search for. Accepts 'plugins', 'themes', 'core'.
	 * @param string $file Specific translation file/area. Either 'admin', 'admin-network', 'continents-cities' or 'default'.
	 *
	 * @return string[] List of installed language keys.
	 */
	public static function get_installed_languages( string $type = 'core', string $file = 'default' ): array {
		$installed_translations = wp_get_installed_translations( $type );
		$languages              = [ 'en_US' ];

		if ( ! isset( $installed_translations[ $file ] ) ) {
			return $languages;
		}

		/** @var string[] $languages */
		$languages = array_values(
			array_unique(
				array_merge(
					$languages,
					array_keys( $installed_translations[ $file ] ),
				),
			),
		);

		return $languages;
	}

	/**
	 * Get single translation string in a specific language
	 *
	 * @param string $token Token to get translation for.
	 * @param string $lang  Requested language.
	 *
	 * @return string Translated string if found, original string otherwise.
	 */
	public static function get_single_translation( string $token, string $lang ): string {
		// Do not translate native tokens.
		if ( 'en_US' === $lang ) {
			return $token;
		}

		/**
		 * Read the translation file and collect
		 * all the translated strings
		 */
		if ( ! isset( self::$translations[ $lang ] ) ) {
			$path     = sprintf( '%1$s/%2$s.l10n.php', WP_LANG_DIR, $lang );
			$messages = [];

			if ( file_exists( $path ) ) {
				$contents = include $path;
				$messages = $contents['messages'];
			}

			self::$translations[ $lang ] = $messages;
			unset( $contents, $messages );
		}

		// Find the translated token.
		return self::$translations[ $lang ][ $token ] ?? $token;
	}
}
