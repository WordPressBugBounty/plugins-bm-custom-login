<?php
/**
 * Apply custom styles defined in plugin settings onto the login page
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Background;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Footer;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Language_Switcher;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Login_Form_Button_Primary;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Login_Form_Button_Secondary;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Login_Form_Checkbox_Fields;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Login_Form_Container;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Login_Form_Input_Fields;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Login_Form_Labels;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Login_Form_Remember_Me_Checkbox;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Logo;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Notices;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Privacy_Policy_Link;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Social_Media_Links;
use Teydea_Studio\Custom_Login\Modules\Adjustments\Internal\Adjuster_Under_Form_Links;
use Teydea_Studio\Custom_Login\Styles;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Module_Adjustments" class
 */
final class Module_Adjustments extends Utils\Module {
	/**
	 * Hold the array of adjusters objects
	 *
	 * @var ?Adjuster[]
	 */
	protected ?array $adjusters = null;

	/**
	 * Hold the Styles instance
	 *
	 * @var ?Styles
	 */
	protected ?object $styles = null;

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Render inline styles in the "head" section of the login screen.
		add_action( 'login_head', [ $this, 'render_inline_styles' ] );

		// Allow adjusters to register their hooks.
		add_action(
			'login_init',
			function (): void {
				if ( $this->should_skip_adjustments() ) {
					return;
				}

				foreach ( $this->get_adjusters() as $adjuster ) {
					$adjuster->register();
				}
			},
		);

		// Allow specific adjusters to apply their markup adjustments.
		add_filter( 'custom_login__markup_adjustments', [ $this, 'apply_markup_adjustments' ], 10, 2 );
	}

	/**
	 * Determine whether the login-page adjustments should be skipped
	 *
	 * Adjustments are not applied to interim login requests or the
	 * "confirm_admin_email" login action.
	 *
	 * @return bool Whether to skip the adjustments for the current request.
	 */
	private function should_skip_adjustments(): bool {
		/**
		 * Read GET/POST explicitly rather than $_REQUEST, which can include cookies
		 * (a stray "interim-login" / "action" cookie must not disable the styling).
		 *
		 * This only inspects the request to decide whether to skip cosmetic login
		 * styling -- it changes no state, and the action value is sanitized with
		 * sanitize_key(), so the nonce / sanitization sniffs are suppressed here.
		 */
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Do not apply adjustments for interim login requests.
		if ( isset( $_GET['interim-login'] ) || isset( $_POST['interim-login'] ) ) {
			return true;
		}

		$action = '';

		if ( isset( $_POST['action'] ) ) {
			$action = sanitize_key( Utils\Type::ensure_string( wp_unslash( $_POST['action'] ) ) );
		} elseif ( isset( $_GET['action'] ) ) {
			$action = sanitize_key( Utils\Type::ensure_string( wp_unslash( $_GET['action'] ) ) );
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		// Do not apply adjustments on the "confirm_admin_email" login action.
		if ( 'confirm_admin_email' === $action ) {
			return true;
		}

		return false;
	}

	/**
	 * Get array of adjusters objects
	 *
	 * @return Adjuster[]
	 */
	protected function get_adjusters(): array {
		if ( null === $this->adjusters ) {
			/**
			 * Allow other plugins and modules to filter the
			 * list of adjuster classes
			 *
			 * @param array<string,string> $adjuster_classes List of adjuster classes.
			 */
			$adjuster_classes = apply_filters(
				'custom_login__adjuster_classes',
				[
					'background'                      => Adjuster_Background::class,
					'language_switcher'               => Adjuster_Language_Switcher::class,
					'footer'                          => Adjuster_Footer::class,
					'login_form_button_primary'       => Adjuster_Login_Form_Button_Primary::class,
					'login_form_button_secondary'     => Adjuster_Login_Form_Button_Secondary::class,
					'login_form_checkbox_fields'      => Adjuster_Login_Form_Checkbox_Fields::class,
					'login_form_container'            => Adjuster_Login_Form_Container::class,
					'login_form_input_fields'         => Adjuster_Login_Form_Input_Fields::class,
					'login_form_labels'               => Adjuster_Login_Form_Labels::class,
					'login_form_remember_me_checkbox' => Adjuster_Login_Form_Remember_Me_Checkbox::class,
					'logo'                            => Adjuster_Logo::class,
					'notices'                         => Adjuster_Notices::class,
					'privacy_policy_link'             => Adjuster_Privacy_Policy_Link::class,
					'social_media_links'              => Adjuster_Social_Media_Links::class,
					'under_form_links'                => Adjuster_Under_Form_Links::class,
				],
			);

			$adjusters      = [];
			$current_locale = ( new Utils\Languages() )->get_current_locale();

			/** @var Adjuster $adjuster */
			foreach ( $adjuster_classes as $adjuster ) {
				$adjusters[] = new $adjuster(
					$this->container,
					$this->get_settings(),
					$this->get_styles(),
					$current_locale,
				);
			}

			$this->adjusters = $adjusters;
		}

		return $this->adjusters;
	}

	/**
	 * Get the custom CSS styles, defined by the user
	 *
	 * @return string Custom CSS styles.
	 */
	protected function get_custom_css(): string {
		// Get the fields group.
		$fields_group = $this->get_settings()->get_fields_group( 'custom_css' );

		if ( null === $fields_group ) {
			return '';
		}

		/** @var string $css */
		$css = $fields_group->get_field_value( 'css' ) ?? '';

		return $css;
	}

	/**
	 * Get the styles class instance
	 *
	 * @return Styles Styles class instance.
	 */
	protected function get_styles(): Styles {
		if ( null === $this->styles ) {
			$this->styles = new Styles( $this->container );
		}

		return $this->styles;
	}

	/**
	 * Render inline styles in the "head" section of the login screen
	 *
	 * @return void
	 */
	public function render_inline_styles(): void {
		if ( $this->should_skip_adjustments() ) {
			return;
		}

		// Start with core styles.
		$styles = [
			'#login { padding: 0; }',
			'.bm-custom-login__wrapper { align-items: center; display: flex; flex-direction: column; height: 100dvh; justify-content: center; width: 100%; }',
			'@media (min-width: 768px) { .bm-custom-login__wrapper { flex-direction: row; height: 100%; } }',
			'.bm-custom-login__column { align-items: center; display: flex; flex: 1; width: 100%; justify-content: center; height: 100%; position: relative; }',
			'@media (min-width: 768px) { .bm-custom-login__column { min-height: 0; } }',
			'.bm-custom-login__at-the-bottom-placeholder { display: none; }',
		];

		/**
		 * Allow adjusters to generate their styles
		 */
		foreach ( $this->get_adjusters() as $adjuster ) {
			$styles = array_merge(
				$styles,
				$adjuster->generate_styles(),
			);
		}

		// Add the custom CSS defined in plugin settings.
		$styles[] = $this->get_custom_css();

		// Print the font faces.
		wp_print_font_faces();

		?>
		<style id="<?php echo esc_attr( $this->container->get_slug() ); ?>-styles"><?php echo Utils\Strings::sanitize_css( implode( PHP_EOL, $styles ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></style>
		<?php
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
		if ( $this->should_skip_adjustments() ) {
			return $doc;
		}

		// Create the wrapper container.
		$wrapper = $doc->createElement( 'div' );
		$wrapper->setAttribute( 'class', 'bm-custom-login__form-wrapper' );

		/**
		 * Markup customizations for login form container
		 */
		$container = $xpath->query( '//div[@id="login"]' );

		if ( false !== $container && ! empty( $container[0] ) ) {
			$container = $container[0];

			$container->parentNode->replaceChild( $wrapper, $container ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$wrapper->appendChild( $container );
		}

		/**
		 * Markup customizations for the language switcher
		 */
		$language_switcher = $xpath->query( '//div[@class="language-switcher"]' );

		if ( false !== $language_switcher && ! empty( $language_switcher[0] ) ) {
			$language_switcher = $language_switcher[0];
			$wrapper->appendChild( $language_switcher );
		}

		/**
		 * Append the "at the bottom" placeholder
		 */
		$at_the_bottom_placeholder = $xpath->query( '//div[contains(@class, "bm-custom-login__at-the-bottom-placeholder")]' );

		if ( false !== $at_the_bottom_placeholder && ! empty( $at_the_bottom_placeholder[0] ) ) {
			$at_the_bottom_placeholder = $at_the_bottom_placeholder[0];
			$wrapper->appendChild( $at_the_bottom_placeholder );
		}

		/**
		 * Allow other adjusters to apply their markup adjustments
		 */
		foreach ( $this->get_adjusters() as $adjuster ) {
			$doc = $adjuster->apply_markup_adjustments( $doc, $xpath );
		}

		// Return the updated DOMDocument object.
		return $doc;
	}
}
