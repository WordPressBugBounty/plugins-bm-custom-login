/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Compose the gradient palettes
 *
 * @param {Object} gradientPalettes Gradient palettes object.
 *
 * @return {Array} Gradient palettes array.
 */
export const composeGradientPalettes = ( gradientPalettes ) => {
	// Define the result array.
	const result = [];

	if ( null !== gradientPalettes ) {
		/**
		 * Gradient palette defined in theme.json
		 */
		if ( 'object' === typeof gradientPalettes?.theme ) {
			result.push( {
				name: __( 'Theme gradient palette', 'bm-custom-login' ),
				gradients: gradientPalettes.theme,
			} );
		}

		/**
		 * Default gradient palette defined in
		 * WordPress core
		 */
		if ( 'object' === typeof gradientPalettes?.default ) {
			result.push( {
				name: __( 'Default core gradient palette', 'bm-custom-login' ),
				gradients: gradientPalettes.default,
			} );
		}
	}

	return result;
};
