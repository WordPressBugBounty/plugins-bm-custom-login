<?php
/**
 * Implement style and markup adjustments controlled by the "login_form_container" fields group
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
 * The "Adjuster_Login_Form_Container" class
 *
 * @phpstan-type Type_Adjuster_Login_Form_Container_Config ?array{alignment:string,background_color:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,focal_point_x:float,focal_point_y:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,media_id:int,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,size_repeat:string,width:int,wrap_links_in_container:bool,wrap_logo_in_container:bool,wrapper_padding_top:string,wrapper_padding_right:string,wrapper_padding_bottom:string,wrapper_padding_left:string}
 */
final class Adjuster_Login_Form_Container extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'login_form_container';

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Render the containers.
		add_action( 'login_footer', [ $this, 'render_containers' ] );
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
		/** @var Type_Adjuster_Login_Form_Container_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		/**
		 * Markup customizations for login form wrapper
		 */
		$column       = $xpath->query( '//div[contains(@class, "bm-custom-login__column--with-form")]' );
		$form_wrapper = $xpath->query( '//div[@class="bm-custom-login__form-wrapper"]' );

		if ( false !== $form_wrapper && false !== $column && ! empty( $form_wrapper[0] ) && ! empty( $column[0] ) ) {
			$column       = $column[0];
			$form_wrapper = $form_wrapper[0];

			$column->appendChild( $form_wrapper );
		}

		/**
		 * Move logo outside of the #login container
		 * if it should be placed outside the container
		 */
		if ( false === $config['wrap_logo_in_container'] ) {
			$login_container = $xpath->query( '//div[@id="login"]' );
			$logo            = $xpath->query( '//h1[@class="wp-login-logo"]' );

			if ( false !== $login_container && false !== $logo && ! empty( $login_container[0] ) && ! empty( $logo[0] ) ) {
				$login_container = $login_container[0];
				$logo            = $logo[0];

				$login_container->parentNode->insertBefore( $logo, $login_container ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		}

		/**
		 * Move logo inside the form#loginform if it should
		 * be placed inside the container and the footer
		 * links stays outside of it
		 */
		if ( true === $config['wrap_logo_in_container'] && false === $config['wrap_links_in_container'] ) {
			$login_form = $xpath->query( '//form[@id="loginform"]' );
			$logo       = $xpath->query( '//h1[@class="wp-login-logo"]' );

			if ( false !== $login_form && false !== $logo && ! empty( $login_form[0] ) && ! empty( $logo[0] ) ) {
				$login_form = $login_form[0];
				$logo       = $logo[0];

				$login_form->insertBefore( $logo, $login_form->firstChild ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		}

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Login_Form_Container_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{alignment:string,background_color:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,focal_point_x:float,focal_point_y:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,media_id:int,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,size_repeat:string,width:int,wrap_links_in_container:bool,wrap_logo_in_container:bool,wrapper_padding_top:string,wrapper_padding_right:string,wrapper_padding_bottom:string,wrapper_padding_left:string} $results */
		$results = $fields_group->get_all_fields_values();
		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Login_Form_Container_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$selector = true === $config['wrap_links_in_container'] ? '#login' : '.login form';

		$styles = [
			sprintf(
				'#login { width: %spx; max-width: calc(100dvw - 10px); }',
				$config['width'],
			),
			sprintf(
				'%s { background: %s; %s; %s; %s; box-shadow: %s; margin: %s; padding: %s; }',
				$selector,
				$this->styles->compose_color( $config['background_color'], 'initial' ),
				$this->styles->compose_background_image_style(
					[
						'media_id'      => $config['media_id'],
						'focal_point_x' => $config['focal_point_x'],
						'focal_point_y' => $config['focal_point_y'],
						'size_repeat'   => $config['size_repeat'],
					],
				),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['border_top_width'],
						'top_style'    => $config['border_top_style'],
						'top_color'    => $config['border_top_color'],
						'right_width'  => $config['border_right_width'],
						'right_style'  => $config['border_right_style'],
						'right_color'  => $config['border_right_color'],
						'bottom_width' => $config['border_bottom_width'],
						'bottom_style' => $config['border_bottom_style'],
						'bottom_color' => $config['border_bottom_color'],
						'left_width'   => $config['border_left_width'],
						'left_style'   => $config['border_left_style'],
						'left_color'   => $config['border_left_color'],
					],
				),
				$this->styles->compose_border_radius_style( $config['border_bottom_left_radius'], $config['border_bottom_right_radius'], $config['border_top_left_radius'], $config['border_top_right_radius'] ),
				$this->styles->compose_box_shadow_style( $config['shadow'] ),
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
			sprintf(
				'%s:hover { box-shadow: %s; %s; }',
				$selector,
				$this->styles->compose_box_shadow_style( $config['shadow_on_hover'] ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['border_top_width_on_hover'],
						'top_style'    => $config['border_top_style_on_hover'],
						'top_color'    => $config['border_top_color_on_hover'],
						'right_width'  => $config['border_right_width_on_hover'],
						'right_style'  => $config['border_right_style_on_hover'],
						'right_color'  => $config['border_right_color_on_hover'],
						'bottom_width' => $config['border_bottom_width_on_hover'],
						'bottom_style' => $config['border_bottom_style_on_hover'],
						'bottom_color' => $config['border_bottom_color_on_hover'],
						'left_width'   => $config['border_left_width_on_hover'],
						'left_style'   => $config['border_left_style_on_hover'],
						'left_color'   => $config['border_left_color_on_hover'],
					],
				),
			),
			sprintf(
				'%s:focus-within { box-shadow: %s; %s; }',
				$selector,
				$this->styles->compose_box_shadow_style( $config['shadow_on_focus'] ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['border_top_width_on_focus'],
						'top_style'    => $config['border_top_style_on_focus'],
						'top_color'    => $config['border_top_color_on_focus'],
						'right_width'  => $config['border_right_width_on_focus'],
						'right_style'  => $config['border_right_style_on_focus'],
						'right_color'  => $config['border_right_color_on_focus'],
						'bottom_width' => $config['border_bottom_width_on_focus'],
						'bottom_style' => $config['border_bottom_style_on_focus'],
						'bottom_color' => $config['border_bottom_color_on_focus'],
						'left_width'   => $config['border_left_width_on_focus'],
						'left_style'   => $config['border_left_style_on_focus'],
						'left_color'   => $config['border_left_color_on_focus'],
					],
				),
			),
			'.bm-custom-login__form-wrapper { position: relative; }',
		];

		if ( true === $config['wrap_links_in_container'] ) {
			$styles[] = '#login { box-sizing: border-box; }';
			$styles[] = '.login form { background: transparent; padding: 0; margin: 0; border: none; box-shadow: none; overflow: initial; }';
			$styles[] = '.login form::after { content: ""; display: block; clear: both; }';
		}

		if ( 'default' !== $config['alignment'] ) {
			$flex_alignment = 'align-items: center; justify-content: center';

			switch ( $config['alignment'] ) {
				case 'top left':
					$flex_alignment = 'align-items: flex-start; justify-content: flex-start';
					break;
				case 'top center':
					$flex_alignment = 'align-items: center; justify-content: flex-start';
					break;
				case 'top right':
					$flex_alignment = 'align-items: flex-end; justify-content: flex-start';
					break;
				case 'center left':
					$flex_alignment = 'align-items: flex-start; justify-content: center';
					break;
				case 'center center':
					$flex_alignment = 'align-items: center; justify-content: center';
					break;
				case 'center right':
					$flex_alignment = 'align-items: flex-end; justify-content: center';
					break;
				case 'bottom left':
					$flex_alignment = 'align-items: flex-start; justify-content: flex-end';
					break;
				case 'bottom center':
					$flex_alignment = 'align-items: center; justify-content: flex-end';
					break;
				case 'bottom right':
					$flex_alignment = 'align-items: flex-end; justify-content: flex-end';
					break;
			}

			$styles[] = sprintf(
				'.bm-custom-login__column--with-form { display: flex; flex-direction: column; %s; }',
				$flex_alignment,
			);

			$styles[] = sprintf(
				'.bm-custom-login__form-wrapper { padding: %s; }',
				$this->styles->compose_spacing(
					[
						'top'    => $config['wrapper_padding_top'],
						'right'  => $config['wrapper_padding_right'],
						'bottom' => $config['wrapper_padding_bottom'],
						'left'   => $config['wrapper_padding_left'],
					],
				),
			);
		}

		return $styles;
	}

	/**
	 * Render the containers
	 *
	 * @return void
	 */
	public function render_containers(): void {
		?>
		<div class="bm-custom-login__wrapper">
			<?php

			/**
			 * Allow other plugins and modules to render their
			 * own contents before the column that holds
			 * the login form
			 */
			do_action( 'custom_login__before_with_form_column' );

			?>
			<div class="bm-custom-login__column bm-custom-login__column--with-form">
				<?php

				/**
				 * Allow other plugins and modules to render their
				 * own contents inside the column
				 */
				do_action( 'custom_login__inside_column', 'with-form' );

				?>
			</div>
			<?php

			/**
			 * Allow other plugins and modules to render their
			 * own contents after the column that holds
			 * the login form
			 */
			do_action( 'custom_login__after_with_form_column' );

			?>
		</div>
		<?php
	}
}
