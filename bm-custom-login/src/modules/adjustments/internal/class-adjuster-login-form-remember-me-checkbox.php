<?php
/**
 * Implement style and markup adjustments controlled by the "login_form_remember_me_checkbox" fields group
 *
 * @package Teydea_Studio\Login_Customizer
 */

namespace Teydea_Studio\Login_Customizer\Modules\Adjustments\Internal;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Login_Customizer\Dependencies\Utils;
use Teydea_Studio\Login_Customizer\Modules\Adjustments\Adjuster;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Adjuster_Login_Form_Remember_Me_Checkbox" class
 *
 * @phpstan-type Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config ?array{help_bubble_background_color:string,help_bubble_border_color:string,help_bubble_text_color:string,help_icon_color:string,help_icon_color_on_hover:string,help_text:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,show_help:bool,visibility:string,label_remember_me:string}
 */
final class Adjuster_Login_Form_Remember_Me_Checkbox extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'login_form_remember_me_checkbox';

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Resolve the help text of the "Remember Me" toggletip.
		add_filter( 'login_remember_me_help_text', [ $this, 'filter_help_text' ] );
	}

	/**
	 * Resolve the help text of the "Remember Me" toggletip
	 *
	 * WordPress 7.1 renders a help toggletip next to the "Remember Me" label and
	 * drops the whole element when this filter yields an empty string, which is
	 * what both hide paths below rely on. On earlier versions the filter never
	 * runs, so nothing here needs a version guard.
	 *
	 * The stored override is escaped on its way out, because the field's own
	 * sanitizer leaves quotes and ampersands intact. Core escapes the filtered
	 * string again in wp_get_tooltip_helper(), which costs nothing: esc_html()
	 * does not re-encode an entity that is already valid, so a second pass over
	 * an escaped string returns it unchanged. Core's own default needs no pass
	 * at all and is handed back as it came.
	 *
	 * @param string $help_text Help text as resolved by core.
	 *
	 * @return string Help text to render, empty string to drop the toggletip.
	 */
	public function filter_help_text( string $help_text ): string {
		/** @var Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $help_text;
		}

		// A hidden checkbox row leaves nothing for the help text to explain.
		if ( 'visible' !== $config['visibility'] || false === $config['show_help'] ) {
			return '';
		}

		return '' === $config['help_text'] ? $help_text : esc_html( $config['help_text'] );
	}

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Markup customizations for the checkbox field value
		 */
		if ( 'visible' !== $config['visibility'] ) {
			$wrapper = $xpath->query( '//input[@id="rememberme"]' );

			if ( false !== $wrapper && ! empty( $wrapper[0] ) ) {
				$wrapper = $wrapper[0];
				$wrapper->setAttribute( 'disabled', 'disabled' );

				if ( 'hidden-checked' === $config['visibility'] ) {
					$wrapper->setAttribute( 'checked', 'checked' );
				}
			}

			return $doc;
		}

		/**
		 * Markup customizations for the checkbox label
		 */
		$label = $xpath->query( '//label[@for="rememberme"]' );

		if ( false !== $label && ! empty( $label[0] ) ) {
			$label            = $label[0];
			$label->nodeValue = esc_html( $config['label_remember_me'] );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{help_bubble_background_color:string,help_bubble_border_color:string,help_bubble_text_color:string,help_icon_color:string,help_icon_color_on_hover:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,show_help:bool,visibility:string} $results */
		$results                      = $fields_group->get_all_fields_values();
		$results['help_text']         = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'help_text.%s', $this->locale ) ) ?? '' );
		$results['label_remember_me'] = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_remember_me.%s', $this->locale ) ) ?? '' );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'#login form p.forgetmenot { margin: %1$s; }',
				$this->styles->compose_spacing(
					[
						'top'    => $config['margin_top'],
						'right'  => $config['margin_right'],
						'bottom' => $config['margin_bottom'],
						'left'   => $config['margin_left'],
					],
				),
			),
		];

		if ( 'visible' !== $config['visibility'] ) {
			$styles[] = '#login form p.forgetmenot { display: none; }';
			return $styles;
		}

		if ( false === $config['show_help'] ) {
			return $styles;
		}

		return array_merge( $styles, $this->generate_toggletip_styles( $config ) );
	}

	/**
	 * Generate styles for the "Remember Me" help toggletip (WordPress 7.1+)
	 *
	 * The popover bubble renders in the top layer, which detaches it from the
	 * ancestor stacking and clipping context but not from the document tree, so
	 * it is still reached by the login-form-scoped selectors used everywhere
	 * else here. Both arrow pseudo-elements are recolored alongside the bubble
	 * because core paints them from its own border and background literals.
	 *
	 * Each focus ring is drawn in its control's resting color rather than its
	 * hover color: the resting color is the one already known to read against
	 * the surface the control sits on, and it keeps the ring distinguishable
	 * from the glyph it surrounds, which turns the hover color while focused.
	 * The transparent outline is what remains of the indicator under forced
	 * colors, where box shadows are dropped.
	 *
	 * @param array{help_bubble_background_color:string,help_bubble_border_color:string,help_bubble_text_color:string,help_icon_color:string,help_icon_color_on_hover:string} $config Adjuster config.
	 *
	 * @return string[] Array of CSS styles.
	 */
	private function generate_toggletip_styles( array $config ): array {
		$background_color = $this->styles->compose_color( $config['help_bubble_background_color'] );
		$border_color     = $this->styles->compose_color( $config['help_bubble_border_color'] );
		$icon_color       = $this->styles->compose_color( $config['help_icon_color'] );
		$icon_color_hover = $this->styles->compose_color( $config['help_icon_color_on_hover'] );
		$text_color       = $this->styles->compose_color( $config['help_bubble_text_color'] );

		return [
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__toggle { color: %1$s; }',
				$icon_color,
			),
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__close { color: %1$s; }',
				$text_color,
			),
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__toggle:hover, #login form p.forgetmenot .wp-tooltip__toggle:focus, #login form p.forgetmenot .wp-tooltip__close:hover, #login form p.forgetmenot .wp-tooltip__close:focus { color: %1$s; }',
				$icon_color_hover,
			),
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__toggle:focus { box-shadow: 0 0 0 1.5px %1$s; outline: 2px solid transparent; }',
				$icon_color,
			),
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__close:focus { box-shadow: 0 0 0 1.5px %1$s; outline: 2px solid transparent; }',
				$text_color,
			),
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__bubble { background: %1$s; border-color: %2$s; color: %3$s; }',
				$background_color,
				$border_color,
				$text_color,
			),
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__bubble::before { border-top-color: %1$s; }',
				$border_color,
			),
			sprintf(
				'#login form p.forgetmenot .wp-tooltip__bubble::after { border-top-color: %1$s; }',
				$background_color,
			),
		];
	}
}
