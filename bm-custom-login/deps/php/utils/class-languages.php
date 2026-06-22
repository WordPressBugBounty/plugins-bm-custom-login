<?php
/**
 * Languages utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * Languages utils class
 *
 * Memoizes the resolved locale and loaded translation maps on the
 * instance, so repeated lookups on the same instance reuse a single
 * read. Reuse one instance for the lookups in a given code path (e.g.
 * a settings page that translates many tokens in a loop); separate
 * instances do not share the memo.
 */
final class Languages {
	/**
	 * Hold the current locale
	 *
	 * @var ?string
	 */
	protected ?string $locale = null;

	/**
	 * Loaded translations
	 *
	 * @var array<string,array<string,string>> Translations.
	 */
	protected array $translations = [];

	/**
	 * Get the current locale
	 *
	 * Note: on the login screen we have to read that from $_GET global
	 * variable, as core get_locale() returns site default language
	 * at this point.
	 *
	 * @return string Current locale.
	 */
	public function get_current_locale(): string {
		if ( null === $this->locale ) {
			/**
			 * Check if the locale is set in the $_GET variable
			 */
			if ( isset( $_GET['wp_lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$lang = sanitize_text_field( Type::ensure_string( wp_unslash( $_GET['wp_lang'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( ! empty( $lang ) && in_array( $lang, $this->get_installed_languages( 'core' ), true ) ) {
					$this->locale = $lang;
				}
			}

			/**
			 * If the locale is not set in the $_GET variable,
			 * we can try to read it from the user cookie.
			 */
			if ( null === $this->locale && isset( $_COOKIE['wp_lang'] ) ) {
				$lang = sanitize_text_field( Type::ensure_string( wp_unslash( $_COOKIE['wp_lang'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( ! empty( $lang ) && in_array( $lang, $this->get_installed_languages( 'core' ), true ) ) {
					$this->locale = $lang;
				}
			}

			// Still nothing at this point? Use core function as a fallback.
			if ( null === $this->locale ) {
				$this->locale = get_locale();
			}
		}

		return $this->locale;
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
	public function get_installed_languages( string $type = 'core', string $file = 'default' ): array {
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
	public function get_single_translation( string $token, string $lang ): string {
		// Do not translate native tokens.
		if ( 'en_US' === $lang ) {
			return $token;
		}

		/**
		 * Read the translation file and collect
		 * all the translated strings
		 */
		if ( ! isset( $this->translations[ $lang ] ) ) {
			$messages = [];

			/**
			 * Constrain $lang to an installed language before interpolating
			 * it into a filesystem path; an unvalidated value would allow
			 * including arbitrary *.l10n.php files via path traversal.
			 */
			if ( in_array( $lang, $this->get_installed_languages( 'core' ), true ) ) {
				$path = sprintf( '%1$s/%2$s.l10n.php', WP_LANG_DIR, $lang );

				if ( file_exists( $path ) ) {
					$contents = include $path;

					if ( is_array( $contents ) && isset( $contents['messages'] ) && is_array( $contents['messages'] ) ) {
						/** @var array<string,string> $messages */
						$messages = $contents['messages'];
					}
				}
			}

			$this->translations[ $lang ] = $messages;
			unset( $contents, $messages );
		}

		// Find the translated token.
		return $this->translations[ $lang ][ $token ] ?? $token;
	}
}
