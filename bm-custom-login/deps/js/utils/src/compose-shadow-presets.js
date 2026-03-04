/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Compose the shadow presets
 *
 * @param {Object} shadows Theme shadows object.
 *
 * @return {Array} Shadows array.
 */
export const composeShadowPresets = ( shadows ) => {
	// Define the result array.
	let result = [
		{
			name: __( 'None', 'bm-custom-login' ),
			shadow: '',
		},
	];

	if ( null !== shadows ) {
		/**
		 * Shadows defined in theme.json
		 */
		if ( 'object' === typeof shadows?.theme ) {
			result = [ ...result, ...shadows.theme ];
		}

		/**
		 * Default shadows defined in
		 * WordPress core
		 */
		if ( 'object' === typeof shadows?.default ) {
			result = [ ...result, ...shadows.default ];
		}
	}

	// Ensure we use desired shape.
	return result.map( ( { name, shadow } ) => ( {
		label: name,
		value: shadow,
	} ) );
};
