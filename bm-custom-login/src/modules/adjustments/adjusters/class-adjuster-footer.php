<?php
/**
 * Implement style and markup adjustments controlled by the "footer" fields group
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Custom_Login\Adjuster;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster_Footer" class
 *
 * @phpstan-type Type_Adjuster_Footer_Config ?array{alignment:string,font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,text_color:string,text_decoration:string,text:string}
 */
final class Adjuster_Footer extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'footer';

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Maybe render the footer contents.
		add_action( 'login_footer', [ $this, 'maybe_render_footer' ], 10 );
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
		/**
		 * Markup customizations for the login form footer
		 */
		$form_wrapper = $xpath->query( '//div[contains(@class, "bm-custom-login__form-wrapper")]' );
		$footer       = $xpath->query( '//p[@class="bm-custom-login__footer"]' );

		if ( false !== $footer && false !== $form_wrapper && ! empty( $footer[0] ) && ! empty( $form_wrapper[0] ) ) {
			$form_wrapper = $form_wrapper[0];
			$footer       = $footer[0];

			$form_wrapper->appendChild( $footer );
		}

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Footer_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{alignment:string,font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,text_color:string,text_decoration:string,text:string} $results */
		$results = $fields_group->get_all_fields_values();
		unset( $fields_group );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Footer_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'p.bm-custom-login__footer { color: %s; font-family: %s; font-size: %s; font-weight: %s; line-height: %.02fem; text-align: %s; text-decoration: %s; text-transform: %s; margin: %s; padding: %s; }',
				$this->styles->compose_color( $config['text_color'] ),
				$this->styles->get_font_family_name( $config['font_family'] ),
				$config['font_size'],
				$config['font_weight'],
				$config['line_height'],
				$config['alignment'],
				$config['text_decoration'],
				$config['letter_case'],
				$this->styles->compose_spacing(
					[
						'top'    => $config['margin_top'],
						'right'  => $config['margin_right'],
						'bottom' => $config['margin_bottom'],
						'left'   => $config['margin_left'],
					],
				),
				$this->styles->compose_spacing(
					[
						'top'    => $config['padding_top'],
						'right'  => $config['padding_right'],
						'bottom' => $config['padding_bottom'],
						'left'   => $config['padding_left'],
					],
				),
			),
		];

		return $styles;
	}

	/**
	 * Maybe render the footer contents
	 *
	 * @return void
	 */
	public function maybe_render_footer(): void {
		/** @var Type_Adjuster_Footer_Config $config */
		$config = $this->get_config();

		if ( null === $config || empty( $config['text'] ) ) {
			return;
		}

		?>
		<p class="bm-custom-login__footer" aria-hidden="true"><?php echo esc_html( $config['text'] ); ?></p>
		<?php
	}
}
