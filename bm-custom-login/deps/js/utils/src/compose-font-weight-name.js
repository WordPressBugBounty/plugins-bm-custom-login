/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Compose the font weight name
 *
 * @param {string} fontWeight Font weight.
 *
 * @return {string} Font weight name.
 */
export const composeFontWeightName = ( fontWeight ) => {
	const mapping = {
		100: __( 'Thin 100', 'bm-custom-login' ),
		'100italic': __( 'Thin 100 Italic', 'bm-custom-login' ),
		200: __( 'ExtraLight 200', 'bm-custom-login' ),
		'200italic': __( 'ExtraLight 200 Italic', 'bm-custom-login' ),
		300: __( 'Light 300', 'bm-custom-login' ),
		'300italic': __( 'Light 300 Italic', 'bm-custom-login' ),
		400: __( 'Regular 400', 'bm-custom-login' ),
		'400italic': __( 'Regular 400 Italic', 'bm-custom-login' ),
		500: __( 'Medium 500', 'bm-custom-login' ),
		'500italic': __( 'Medium 500 Italic', 'bm-custom-login' ),
		600: __( 'SemiBold 600', 'bm-custom-login' ),
		'600italic': __( 'SemiBold 600 Italic', 'bm-custom-login' ),
		700: __( 'Bold 700', 'bm-custom-login' ),
		'700italic': __( 'Bold 700 Italic', 'bm-custom-login' ),
		800: __( 'ExtraBold 800', 'bm-custom-login' ),
		'800italic': __( 'ExtraBold 800 Italic', 'bm-custom-login' ),
		900: __( 'Black 900', 'bm-custom-login' ),
		'900italic': __( 'Black 900 Italic', 'bm-custom-login' ),
	};

	return mapping[ fontWeight ] ?? fontWeight;
};
