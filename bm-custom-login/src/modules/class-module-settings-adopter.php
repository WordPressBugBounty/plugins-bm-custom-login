<?php
/**
 * Ensure settings from the old version of the plugin
 * are adopted properly to the new settings structure
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules;

use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Settings_Adopter" class
 */
final class Module_Settings_Adopter extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Ensure seamless settings migration from the old version of the plugin.
		add_filter( 'custom_login__settings_loaded', [ $this, 'maybe_adapt_settings' ] );
	}

	/**
	 * Ensure seamless settings migration from the old version of the plugin
	 *
	 * @param array<string,mixed> $data Loaded settings data.
	 *
	 * @return array<string,mixed> Adapted settings data.
	 */
	public function maybe_adapt_settings( array $data ): array {
		// Only proceed if no settings are loaded yet.
		if ( ! empty( $data ) ) {
			return $data;
		}

		$old_settings = get_option( 'custom_login_options', null );

		// Only proceed if old settings exist.
		if ( ! is_array( $old_settings ) ) {
			return $data;
		}

		// Try to map attachment URLs to attachment IDs.
		$old_settings__background      = Utils\Type::ensure_string( $old_settings['cl_background'] );
		$container_background_image_id = ! empty( $old_settings__background )
			? attachment_url_to_postid( $old_settings__background )
			: 0;

		$old_settings__background_image = Utils\Type::ensure_string( $old_settings['cl_backgroundImage'] );
		$screen_background_image_id     = ! empty( $old_settings__background_image )
			? attachment_url_to_postid( $old_settings__background_image )
			: 0;

		// Try to map the background focal point (position) X.
		$focal_point_x = 0.5; // Default to center.

		if ( isset( $old_settings['cl_backgroundPX'] ) ) {
			switch ( $old_settings['cl_backgroundPX'] ) {
				case 'left':
					$focal_point_x = 0;
					break;
				case 'right':
					$focal_point_x = 1;
					break;
				// No need for center case, as it's the default value.
			}
		}

		// Try to map the background focal point (position) Y.
		$focal_point_y = 0.5; // Default to center.

		if ( isset( $old_settings['cl_backgroundPY'] ) ) {
			switch ( $old_settings['cl_backgroundPY'] ) {
				case 'top':
					$focal_point_y = 0;
					break;
				case 'bottom':
					$focal_point_y = 1;
					break;
				// No need for center case, as it's the default value.
			}
		}

		// Try to map the background size & repeat.
		$size_repeat = 'size-auto--no-repeat';

		if ( isset( $old_settings['cl_backgroundRepeat'] ) ) {
			switch ( $old_settings['cl_backgroundRepeat'] ) {
				case 'no-repeat':
					$size_repeat = 'size-auto--no-repeat';
					break;
				case 'repeat-x':
					$size_repeat = 'size-auto--repeat-x';
					break;
				case 'repeat-y':
					$size_repeat = 'size-auto--repeat-y';
					break;
				case 'repeat':
					$size_repeat = 'size-auto--repeat';
					break;
			}
		}

		// Map old settings to the new settings structure.
		$data = [
			'customCss'                   => [ 'css' => $old_settings['cl_customCSS'] ?: '' ],
			'background'                  => [
				'color'       => $old_settings['cl_backgroundColor'] ?: '#f0f0f1', // Background color.
				'focalPointX' => $focal_point_x, // Background position X.
				'focalPointY' => $focal_point_y, // Background position Y.
				'mediaId'     => $screen_background_image_id, // Background image ID.
				'sizeRepeat'  => $size_repeat, // Background size & repeat.
			],
			'logo'                        => [
				'alignment'    => 'center',
				'asLink'       => true,
				'link'         => 'https://wordpress.org/',
				'mediaId'      => 0,
				'openInNewTab' => false,
				'show'         => true,
				'strictWidth'  => 0,
				'logoSource'   => 'core',
			],
			'loginFormContainer'          => [
				'alignment'                => 'top center', // Top center alignment.
				'backgroundColor'          => '#ffffff',
				'borderBottomColor'        => '#c3c4c7',
				'borderBottomColorOnFocus' => '#c3c4c7',
				'borderBottomColorOnHover' => '#c3c4c7',
				'borderBottomLeftRadius'   => '5px', // Border radius.
				'borderBottomRightRadius'  => '5px', // Border radius.
				'borderBottomStyle'        => 'solid',
				'borderBottomStyleOnFocus' => 'solid',
				'borderBottomStyleOnHover' => 'solid',
				'borderBottomWidth'        => '0px', // No border.
				'borderBottomWidthOnFocus' => '0px', // No border.
				'borderBottomWidthOnHover' => '0px', // No border.
				'borderLeftColor'          => '#c3c4c7',
				'borderLeftColorOnFocus'   => '#c3c4c7',
				'borderLeftColorOnHover'   => '#c3c4c7',
				'borderLeftStyle'          => 'solid',
				'borderLeftStyleOnFocus'   => 'solid',
				'borderLeftStyleOnHover'   => 'solid',
				'borderLeftWidth'          => '0px', // No border.
				'borderLeftWidthOnFocus'   => '0px', // No border.
				'borderLeftWidthOnHover'   => '0px', // No border.
				'borderRightColor'         => '#c3c4c7',
				'borderRightColorOnFocus'  => '#c3c4c7',
				'borderRightColorOnHover'  => '#c3c4c7',
				'borderRightStyle'         => 'solid',
				'borderRightStyleOnFocus'  => 'solid',
				'borderRightStyleOnHover'  => 'solid',
				'borderRightWidth'         => '0px', // No border.
				'borderRightWidthOnFocus'  => '0px', // No border.
				'borderRightWidthOnHover'  => '0px', // No border.
				'borderTopColor'           => '#c3c4c7',
				'borderTopColorOnFocus'    => '#c3c4c7',
				'borderTopColorOnHover'    => '#c3c4c7',
				'borderTopLeftRadius'      => '5px', // Border radius.
				'borderTopRightRadius'     => '5px', // Border radius.
				'borderTopStyle'           => 'solid',
				'borderTopStyleOnFocus'    => 'solid',
				'borderTopStyleOnHover'    => 'solid',
				'borderTopWidth'           => '0px', // No border.
				'borderTopWidthOnFocus'    => '0px', // No border.
				'borderTopWidthOnHover'    => '0px', // No border.
				'focalPointX'              => 0.5,
				'focalPointY'              => 0,
				'marginBottom'             => '0px', // No margin bottom.
				'marginLeft'               => '0px',
				'marginRight'              => '0px',
				'marginTop'                => '7em', // Customized margin top.
				'mediaId'                  => $container_background_image_id, // Background image ID.
				'paddingBottom'            => '26px',
				'paddingLeft'              => '24px',
				'paddingRight'             => '24px',
				'paddingTop'               => '26px',
				'shadow'                   => 'rgba(0,0,0,0.7) 0 4px 10px -1px', // Box shadow.
				'shadowOnFocus'            => 'rgba(0,0,0,0.7) 0 4px 10px -1px', // Box shadow.
				'shadowOnHover'            => 'rgba(0,0,0,0.7) 0 4px 10px -1px', // Box shadow.
				'sizeRepeat'               => 'size-auto--no-repeat',
				'wrapLinksInContainer'     => true, // Wrap footer links in a form container.
				'wrapperPaddingBottom'     => '0px',
				'wrapperPaddingLeft'       => '0px',
				'wrapperPaddingRight'      => '0px',
				'wrapperPaddingTop'        => '0px',
			],
			'loginFormLabels'             => [
				'fontFamily'    => 'default',
				'fontSize'      => '14px',
				'fontWeight'    => '400',
				'letterCase'    => 'none',
				'lineHeight'    => 1.5,
				'marginBottom'  => '3px',
				'marginLeft'    => '0px',
				'marginRight'   => '0px',
				'marginTop'     => '0px',
				'paddingBottom' => '0px',
				'paddingLeft'   => '0px',
				'paddingRight'  => '0px',
				'paddingTop'    => '0px',
				'textColor'     => $old_settings['cl_color'] ?: '#3c434a', // Text color.
			],
			'loginFormInputFields'        => [
				'backgroundColor'          => '#ffffff',
				'backgroundColorOnFocus'   => '#ffffff',
				'backgroundColorOnHover'   => '#ffffff',
				'borderBottomColor'        => '#8c8f94',
				'borderBottomColorOnFocus' => '#2271b1',
				'borderBottomColorOnHover' => '#8c8f94',
				'borderBottomLeftRadius'   => '4px', // Border radius.
				'borderBottomRightRadius'  => '4px', // Border radius.
				'borderBottomStyle'        => 'solid',
				'borderBottomStyleOnFocus' => 'solid',
				'borderBottomStyleOnHover' => 'solid',
				'borderBottomWidth'        => '1px',
				'borderBottomWidthOnFocus' => '1px',
				'borderBottomWidthOnHover' => '1px',
				'borderLeftColor'          => '#8c8f94',
				'borderLeftColorOnFocus'   => '#2271b1',
				'borderLeftColorOnHover'   => '#8c8f94',
				'borderLeftStyle'          => 'solid',
				'borderLeftStyleOnFocus'   => 'solid',
				'borderLeftStyleOnHover'   => 'solid',
				'borderLeftWidth'          => '1px',
				'borderLeftWidthOnFocus'   => '1px',
				'borderLeftWidthOnHover'   => '1px',
				'borderRightColor'         => '#8c8f94',
				'borderRightColorOnFocus'  => '#2271b1',
				'borderRightColorOnHover'  => '#8c8f94',
				'borderRightStyle'         => 'solid',
				'borderRightStyleOnFocus'  => 'solid',
				'borderRightStyleOnHover'  => 'solid',
				'borderRightWidth'         => '1px',
				'borderRightWidthOnFocus'  => '1px',
				'borderRightWidthOnHover'  => '1px',
				'borderTopColor'           => '#8c8f94',
				'borderTopColorOnFocus'    => '#2271b1',
				'borderTopColorOnHover'    => '#8c8f94',
				'borderTopLeftRadius'      => '4px', // Border radius.
				'borderTopRightRadius'     => '4px', // Border radius.
				'borderTopStyle'           => 'solid',
				'borderTopStyleOnFocus'    => 'solid',
				'borderTopStyleOnHover'    => 'solid',
				'borderTopWidth'           => '1px',
				'borderTopWidthOnFocus'    => '1px',
				'borderTopWidthOnHover'    => '1px',
				'eyeIconColor'             => '#2271b1',
				'eyeIconColorOnFocus'      => '#0a4b78',
				'eyeIconColorOnHover'      => '#0a4b78',
				'fontFamily'               => 'default',
				'fontSize'                 => '24px',
				'fontWeight'               => '400',
				'lineHeight'               => 1.33,
				'marginBottom'             => '16px',
				'marginLeft'               => '0px',
				'marginRight'              => '6px',
				'marginTop'                => '0px',
				'paddingBottom'            => '0.1875rem',
				'paddingLeft'              => '0.3125rem',
				'paddingRight'             => '0.3125rem',
				'paddingTop'               => '0.1875rem',
				'placeholderColor'         => '',
				'shadow'                   => '',
				'shadowOnFocus'            => '0 0 0 1px #2271b1',
				'shadowOnHover'            => '',
				'textColor'                => '#2c3338',
				'textColorOnFocus'         => '#2c3338',
				'textColorOnHover'         => '#2c3338',
			],
			'loginFormCheckboxFields'     => [
				'backgroundColor'               => '#ffffff',
				'backgroundColorChecked'        => '#ffffff',
				'backgroundColorOnFocus'        => '#ffffff',
				'backgroundColorOnFocusChecked' => '#ffffff',
				'backgroundColorOnHover'        => '#ffffff',
				'backgroundColorOnHoverChecked' => '#ffffff',
				'borderBottomColor'             => '#8c8f94',
				'borderBottomColorOnFocus'      => '#2271b1',
				'borderBottomColorOnHover'      => '#8c8f94',
				'borderBottomLeftRadius'        => '4px', // Border radius.
				'borderBottomRightRadius'       => '4px', // Border radius.
				'borderBottomStyle'             => 'solid',
				'borderBottomStyleOnFocus'      => 'solid',
				'borderBottomStyleOnHover'      => 'solid',
				'borderBottomWidth'             => '1px',
				'borderBottomWidthOnFocus'      => '1px',
				'borderBottomWidthOnHover'      => '1px',
				'borderLeftColor'               => '#8c8f94',
				'borderLeftColorOnFocus'        => '#2271b1',
				'borderLeftColorOnHover'        => '#8c8f94',
				'borderLeftStyle'               => 'solid',
				'borderLeftStyleOnFocus'        => 'solid',
				'borderLeftStyleOnHover'        => 'solid',
				'borderLeftWidth'               => '1px',
				'borderLeftWidthOnFocus'        => '1px',
				'borderLeftWidthOnHover'        => '1px',
				'borderRightColor'              => '#8c8f94',
				'borderRightColorOnFocus'       => '#2271b1',
				'borderRightColorOnHover'       => '#8c8f94',
				'borderRightStyle'              => 'solid',
				'borderRightStyleOnFocus'       => 'solid',
				'borderRightStyleOnHover'       => 'solid',
				'borderRightWidth'              => '1px',
				'borderRightWidthOnFocus'       => '1px',
				'borderRightWidthOnHover'       => '1px',
				'borderTopColor'                => '#8c8f94',
				'borderTopColorOnFocus'         => '#2271b1',
				'borderTopColorOnHover'         => '#8c8f94',
				'borderTopLeftRadius'           => '4px', // Border radius.
				'borderTopRightRadius'          => '4px', // Border radius.
				'borderTopStyle'                => 'solid',
				'borderTopStyleOnFocus'         => 'solid',
				'borderTopStyleOnHover'         => 'solid',
				'borderTopWidth'                => '1px',
				'borderTopWidthOnFocus'         => '1px',
				'borderTopWidthOnHover'         => '1px',
				'fieldMarginBottom'             => '0rem',
				'fieldMarginLeft'               => '0rem',
				'fieldMarginRight'              => '0.25rem',
				'fieldMarginTop'                => '-0.25rem',
				'fieldSize'                     => 16,
				'icon'                          => 'default',
				'iconColor'                     => '#3582c4',
				'iconColorOnFocus'              => '#3582c4',
				'iconColorOnHover'              => '#3582c4',
				'iconMarginBottom'              => '0px',
				'iconMarginLeft'                => '-4px',
				'iconMarginRight'               => '0px',
				'iconMarginTop'                 => '-3px',
				'iconSize'                      => 21,
				'shadow'                        => 'inset 0 1px 2px rgba(0,0,0,.1)',
				'shadowOnFocus'                 => '0 0 0 1px #2271b1',
				'shadowOnHover'                 => 'inset 0 1px 2px rgba(0,0,0,.1)',
			],
			'loginFormRememberMeCheckbox' => [
				'marginBottom' => '30px', // Adjusted margin bottom.
				'marginLeft'   => '0px',
				'marginRight'  => '0px',
				'marginTop'    => '0px',
				'visibility'   => 'visible',
			],
			'loginFormButtonPrimary'      => [
				'alignment'                => 'default',
				'backgroundColor'          => $old_settings['cl_linkColor'] ?: '#2271b1', // Link color.
				'backgroundColorOnFocus'   => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -30 ) : '#135e96', // Background color on focus.
				'backgroundColorOnHover'   => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -30 ) : '#135e96', // Background color on hover.
				'borderBottomColor'        => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#2271b1', // Border bottom color.
				'borderBottomColorOnFocus' => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border bottom color on focus.
				'borderBottomColorOnHover' => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border bottom color on hover.
				'borderBottomLeftRadius'   => '3px', // Border radius.
				'borderBottomRightRadius'  => '3px', // Border radius.
				'borderBottomStyle'        => 'solid',
				'borderBottomStyleOnFocus' => 'solid',
				'borderBottomStyleOnHover' => 'solid',
				'borderBottomWidth'        => '1px',
				'borderBottomWidthOnFocus' => '1px',
				'borderBottomWidthOnHover' => '1px',
				'borderLeftColor'          => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#2271b1', // Border left color.
				'borderLeftColorOnFocus'   => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border left color on focus.
				'borderLeftColorOnHover'   => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border left color on hover.
				'borderLeftStyle'          => 'solid',
				'borderLeftStyleOnFocus'   => 'solid',
				'borderLeftStyleOnHover'   => 'solid',
				'borderLeftWidth'          => '1px',
				'borderLeftWidthOnFocus'   => '1px',
				'borderLeftWidthOnHover'   => '1px',
				'borderRightColor'         => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#2271b1', // Border right color.
				'borderRightColorOnFocus'  => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border right color on focus.
				'borderRightColorOnHover'  => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border right color on hover.
				'borderRightStyle'         => 'solid',
				'borderRightStyleOnFocus'  => 'solid',
				'borderRightStyleOnHover'  => 'solid',
				'borderRightWidth'         => '1px',
				'borderRightWidthOnFocus'  => '1px',
				'borderRightWidthOnHover'  => '1px',
				'borderTopColor'           => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#2271b1', // Border top color.
				'borderTopColorOnFocus'    => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border top color on focus.
				'borderTopColorOnHover'    => ! empty( $old_settings['cl_linkColor'] ) ? $this->adjust_brightness( $old_settings['cl_linkColor'], -60 ) : '#135e96', // Border top color on hover.
				'borderTopLeftRadius'      => '3px', // Border radius.
				'borderTopRightRadius'     => '3px', // Border radius.
				'borderTopStyle'           => 'solid',
				'borderTopStyleOnFocus'    => 'solid',
				'borderTopStyleOnHover'    => 'solid',
				'borderTopWidth'           => '1px',
				'borderTopWidthOnFocus'    => '1px',
				'borderTopWidthOnHover'    => '1px',
				'fontFamily'               => 'default',
				'fontSize'                 => '13px',
				'fontWeight'               => '400',
				'letterCase'               => 'none',
				'lineHeight'               => 2.31,
				'marginBottom'             => '30px', // Adjusted margin bottom.
				'marginLeft'               => '0px',
				'marginRight'              => '0px',
				'marginTop'                => '0px',
				'paddingBottom'            => '0px',
				'paddingLeft'              => '12px',
				'paddingRight'             => '12px',
				'paddingTop'               => '0px',
				'shadow'                   => 'inset 0 1px 0 rgba(255,255,255,.3),0 1px 0 rgba(0,0,0,.15)',
				'shadowOnFocus'            => 'inset 0 1px 0 rgba(255,255,255,.3),0 1px 0 rgba(0,0,0,.15)',
				'shadowOnHover'            => 'inset 0 1px 0 rgba(255,255,255,.3),0 1px 0 rgba(0,0,0,.15)',
				'textColor'                => '#ffffff',
				'textColorOnFocus'         => '#ffffff',
				'textColorOnHover'         => '#ffffff',
				'width'                    => 'fit-content',
			],
			'loginFormButtonSecondary'    => [
				'backgroundColor'          => '#f6f7f7',
				'backgroundColorOnFocus'   => '#f6f7f7',
				'backgroundColorOnHover'   => '#f0f0f1',
				'borderBottomColor'        => '#3582c4',
				'borderBottomColorOnFocus' => '#3582c4',
				'borderBottomColorOnHover' => '#0a4b78',
				'borderBottomLeftRadius'   => '3px', // Border radius.
				'borderBottomRightRadius'  => '3px', // Border radius.
				'borderBottomStyle'        => 'solid',
				'borderBottomStyleOnFocus' => 'solid',
				'borderBottomStyleOnHover' => 'solid',
				'borderBottomWidth'        => '1px',
				'borderBottomWidthOnFocus' => '1px',
				'borderBottomWidthOnHover' => '1px',
				'borderLeftColor'          => '#3582c4',
				'borderLeftColorOnFocus'   => '#3582c4',
				'borderLeftColorOnHover'   => '#0a4b78',
				'borderLeftStyle'          => 'solid',
				'borderLeftStyleOnFocus'   => 'solid',
				'borderLeftStyleOnHover'   => 'solid',
				'borderLeftWidth'          => '1px',
				'borderLeftWidthOnFocus'   => '1px',
				'borderLeftWidthOnHover'   => '1px',
				'borderRightColor'         => '#3582c4',
				'borderRightColorOnFocus'  => '#3582c4',
				'borderRightColorOnHover'  => '#0a4b78',
				'borderRightStyle'         => 'solid',
				'borderRightStyleOnFocus'  => 'solid',
				'borderRightStyleOnHover'  => 'solid',
				'borderRightWidth'         => '1px',
				'borderRightWidthOnFocus'  => '1px',
				'borderRightWidthOnHover'  => '1px',
				'borderTopColor'           => '#3582c4',
				'borderTopColorOnFocus'    => '#3582c4',
				'borderTopColorOnHover'    => '#0a4b78',
				'borderTopLeftRadius'      => '3px', // Border radius.
				'borderTopRightRadius'     => '3px', // Border radius.
				'borderTopStyle'           => 'solid',
				'borderTopStyleOnFocus'    => 'solid',
				'borderTopStyleOnHover'    => 'solid',
				'borderTopWidth'           => '1px',
				'borderTopWidthOnFocus'    => '1px',
				'borderTopWidthOnHover'    => '1px',
				'paddingBottom'            => '0px',
				'paddingLeft'              => '10px',
				'paddingRight'             => '10px',
				'paddingTop'               => '0px',
				'shadow'                   => '',
				'shadowOnFocus'            => '0 0 0 1px #3582c4',
				'shadowOnHover'            => '',
				'textColor'                => '#2271b1',
				'textColorOnFocus'         => '#0a4b78',
				'textColorOnHover'         => '#0a4b78',
				'fontFamily'               => 'default',
				'fontSize'                 => '13px',
				'fontWeight'               => '400',
				'letterCase'               => 'none',
				'lineHeight'               => 2.15,
			],
			'notices'                     => [
				'borderBottomLeftRadius'   => '0px',
				'borderBottomRightRadius'  => '0px',
				'borderTopLeftRadius'      => '0px',
				'borderTopRightRadius'     => '0px',
				'customNoticeType'         => 'notice',
				'errorBackgroundColor'     => '#ffffff',
				'errorBorderBottomColor'   => '',
				'errorBorderBottomStyle'   => 'none',
				'errorBorderBottomWidth'   => '0px',
				'errorBorderLeftColor'     => '#d63638',
				'errorBorderLeftStyle'     => 'solid',
				'errorBorderLeftWidth'     => '4px',
				'errorBorderRightColor'    => '',
				'errorBorderRightStyle'    => 'none',
				'errorBorderRightWidth'    => '0px',
				'errorBorderTopColor'      => '',
				'errorBorderTopStyle'      => 'none',
				'errorBorderTopWidth'      => '0px',
				'errorShadow'              => 'rgba(0, 0, 0, 0.1) 0px 1px 1px 0px',
				'errorTextColor'           => '#3c434a',
				'fontFamily'               => 'default',
				'fontSize'                 => '13px',
				'fontWeight'               => '400',
				'lineHeight'               => 1.5,
				'marginBottom'             => '20px',
				'marginLeft'               => '0px',
				'marginRight'              => '0px',
				'marginTop'                => '0px',
				'noticeBackgroundColor'    => '#ffffff',
				'noticeBorderBottomColor'  => '',
				'noticeBorderBottomStyle'  => 'none',
				'noticeBorderBottomWidth'  => '0px',
				'noticeBorderLeftColor'    => '#72aee6',
				'noticeBorderLeftStyle'    => 'solid',
				'noticeBorderLeftWidth'    => '4px',
				'noticeBorderRightColor'   => '',
				'noticeBorderRightStyle'   => 'none',
				'noticeBorderRightWidth'   => '0px',
				'noticeBorderTopColor'     => '',
				'noticeBorderTopStyle'     => 'none',
				'noticeBorderTopWidth'     => '0px',
				'noticeShadow'             => 'rgba(0, 0, 0, 0.1) 0px 1px 1px 0px',
				'noticeTextColor'          => '#3c434a',
				'paddingBottom'            => '12px',
				'paddingLeft'              => '12px',
				'paddingRight'             => '12px',
				'paddingTop'               => '12px',
				'showCustomNotice'         => false,
				'successBackgroundColor'   => '#ffffff',
				'successBorderBottomColor' => '',
				'successBorderBottomStyle' => 'none',
				'successBorderBottomWidth' => '0px',
				'successBorderLeftColor'   => '#00a32a',
				'successBorderLeftStyle'   => 'solid',
				'successBorderLeftWidth'   => '4px',
				'successBorderRightColor'  => '',
				'successBorderRightStyle'  => 'none',
				'successBorderRightWidth'  => '0px',
				'successBorderTopColor'    => '',
				'successBorderTopStyle'    => 'none',
				'successBorderTopWidth'    => '0px',
				'successShadow'            => 'rgba(0, 0, 0, 0.1) 0px 1px 1px 0px',
				'successTextColor'         => '#3c434a',
			],
			'underFormLinks'              => [
				'alignment'        => 'left',
				'disableBackLink'  => false,
				'fontFamily'       => 'default',
				'fontSize'         => '13px',
				'fontWeight'       => '400',
				'letterCase'       => 'none',
				'lineHeight'       => 1.5,
				'linkColor'        => $old_settings['cl_linkColor'] ?: '#50575e', // Link color.
				'linkColorOnFocus' => $this->adjust_brightness( $old_settings['cl_linkColor'] ?: '#135e96', -30 ), // Link color.
				'linkColorOnHover' => $this->adjust_brightness( $old_settings['cl_linkColor'] ?: '#135e96', -30 ), // Link color.
				'marginBottom'     => '0px', // Adjusted margin bottom.
				'marginLeft'       => '0px',
				'marginRight'      => '0px',
				'marginTop'        => '0px', // Adjusted margin top.
				'paddingBottom'    => '0px',
				'paddingLeft'      => '12px', // Adjusted padding left.
				'paddingRight'     => '12px', // Adjusted padding right.
				'paddingTop'       => '0px',
				'separator'        => '|',
				'separatorColor'   => $old_settings['cl_color'] ?: '#50575e', // Text color.
				'shadow'           => '',
				'shadowOnFocus'    => '0 0 0 2px #2271b1',
				'shadowOnHover'    => '',
				'textDecoration'   => 'none',
			],
			'underFormLinksList'          => [],
			'socialMediaLinks'            => [
				'alignment'                => 'center',
				'backgroundColor'          => '',
				'backgroundColorOnFocus'   => '',
				'backgroundColorOnHover'   => '',
				'borderBottomColor'        => '',
				'borderBottomColorOnFocus' => '',
				'borderBottomColorOnHover' => '',
				'borderBottomLeftRadius'   => '0px',
				'borderBottomRightRadius'  => '0px',
				'borderBottomStyle'        => 'none',
				'borderBottomStyleOnFocus' => 'none',
				'borderBottomStyleOnHover' => 'none',
				'borderBottomWidth'        => '0px',
				'borderBottomWidthOnFocus' => '0px',
				'borderBottomWidthOnHover' => '0px',
				'borderLeftColor'          => '',
				'borderLeftColorOnFocus'   => '',
				'borderLeftColorOnHover'   => '',
				'borderLeftStyle'          => 'none',
				'borderLeftStyleOnFocus'   => 'none',
				'borderLeftStyleOnHover'   => 'none',
				'borderLeftWidth'          => '0px',
				'borderLeftWidthOnFocus'   => '0px',
				'borderLeftWidthOnHover'   => '0px',
				'borderRightColor'         => '',
				'borderRightColorOnFocus'  => '',
				'borderRightColorOnHover'  => '',
				'borderRightStyle'         => 'none',
				'borderRightStyleOnFocus'  => 'none',
				'borderRightStyleOnHover'  => 'none',
				'borderRightWidth'         => '0px',
				'borderRightWidthOnFocus'  => '0px',
				'borderRightWidthOnHover'  => '0px',
				'borderTopColor'           => '',
				'borderTopColorOnFocus'    => '',
				'borderTopColorOnHover'    => '',
				'borderTopLeftRadius'      => '0px',
				'borderTopRightRadius'     => '0px',
				'borderTopStyle'           => 'none',
				'borderTopStyleOnFocus'    => 'none',
				'borderTopStyleOnHover'    => 'none',
				'borderTopWidth'           => '0px',
				'borderTopWidthOnFocus'    => '0px',
				'borderTopWidthOnHover'    => '0px',
				'gap'                      => '1em',
				'iconColor'                => '#3c434a',
				'iconColorOnFocus'         => '#135e96',
				'iconColorOnHover'         => '#135e96',
				'iconSize'                 => '20px',
				'marginBottom'             => '16px',
				'marginLeft'               => '0px',
				'marginRight'              => '0px',
				'marginTop'                => '16px',
				'paddingBottom'            => '0px',
				'paddingLeft'              => '0px',
				'paddingRight'             => '0px',
				'paddingTop'               => '0px',
				'placement'                => 'at_the_bottom',
				'shadow'                   => '',
				'shadowOnFocus'            => '0 0 0 2px #2271b1',
				'shadowOnHover'            => '',
				'show'                     => false,
			],
			'socialMediaLinksList'        => [],
			'privacyPolicyLink'           => [
				'alignment'        => 'center',
				'fontFamily'       => 'default',
				'fontSize'         => '13px',
				'fontWeight'       => '400',
				'hide'             => false,
				'letterCase'       => 'none',
				'lineHeight'       => 1.4,
				'linkColor'        => $old_settings['cl_linkColor'] ?: '#2271b1', // Link color.
				'linkColorOnFocus' => $this->adjust_brightness( $old_settings['cl_linkColor'] ?: '#135e96', -30 ), // Link color.
				'linkColorOnHover' => $this->adjust_brightness( $old_settings['cl_linkColor'] ?: '#043959', -30 ), // Link color.
				'marginBottom'     => '2em',
				'marginLeft'       => '0em',
				'marginRight'      => '0em',
				'marginTop'        => '3em',
				'paddingBottom'    => '0px',
				'paddingLeft'      => '0px',
				'paddingRight'     => '0px',
				'paddingTop'       => '0px',
				'shadow'           => '',
				'shadowOnFocus'    => '0 0 0 2px #2271b1',
				'shadowOnHover'    => '',
				'textDecoration'   => 'underline',
			],
			'languageSwitcher'            => [
				'iconColor'     => '#3c434a',
				'show'          => true,
				'marginBottom'  => '24px',
				'marginLeft'    => '0px',
				'marginRight'   => '0px',
				'marginTop'     => '24px',
				'paddingBottom' => '24px',
				'paddingLeft'   => '0px',
				'paddingRight'  => '0px',
				'paddingTop'    => '0px',
			],
			'footer'                      => [
				'alignment'      => 'center',
				'fontFamily'     => 'default',
				'fontSize'       => '13px',
				'fontWeight'     => '600',
				'letterCase'     => 'none',
				'lineHeight'     => 1.5, // Adjusted line height.
				'marginBottom'   => '1em',
				'marginLeft'     => '0em',
				'marginRight'    => '0em',
				'marginTop'      => '1em',
				'paddingBottom'  => '0px',
				'paddingLeft'    => '0px',
				'paddingRight'   => '0px',
				'paddingTop'     => '0px',
				'textColor'      => '#50575e',
				'textDecoration' => 'none',
				'text'           => $old_settings['cl_powerby'] ?: '', // "Powered by" text.
			],
			'miscellaneous'               => [
				'disableAutocomplete' => false,
				'disableAutofocus'    => false,
				'disableShakeEffect'  => false,
			],
		];

		return $data;
	}

	/**
	 * Sanitize hexedecimal numbers used for colors
	 *
	 * @param string $color Hex number to sanitize.
	 *
	 * @return string Sanitized hex color or empty string if invalid.
	 */
	protected function sanitize_hex_color( string $color ): string {
		if ( '' === $color ) {
			return '';
		}

		// Make sure the color starts with a hash.
		$color = '#' . ltrim( $color, '#' );

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}

		return '';
	}

	/**
	 * Adjust brightness of a color
	 *
	 * @param string $hex   Hex colour to adjust.
	 * @param int    $steps Amount to adjust colour brightness.
	 *
	 * @return string Hex colour.
	 */
	public function adjust_brightness( string $hex, int $steps ): string {
		$steps = max( -255, min( 255, $steps ) );
		$hex   = str_replace( '#', '', $hex );

		if ( 3 === strlen( $hex ) ) {
			$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
		}

		$color_parts = str_split( $hex, 2 );
		$return      = '#';

		foreach ( $color_parts as $color ) {
			$color   = hexdec( $color );
			$color   = absint( max( 0, min( 255, $color + $steps ) ) );
			$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT );
		}

		return $this->sanitize_hex_color( $return );
	}
}
