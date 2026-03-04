/**
 * WordPress dependencies
 */
import { sprintf } from '@wordpress/i18n';

/**
 * Compose the font sizes
 *
 * @param {Object} fontSizes Font sizes object.
 *
 * @return {Array} Font sizes array.
 */
export const composeFontSizes = ( fontSizes ) => {
	// Define the result array.
	let result = [];

	if ( null !== fontSizes ) {
		if ( 'object' === typeof fontSizes?.theme ) {
			/**
			 * Font sizes defined in theme.json
			 */
			result = [ ...result, ...fontSizes.theme ];
		} else if ( 'object' === typeof fontSizes?.default ) {
			/**
			 * Default font sizes defined in
			 * WordPress core
			 */
			result = [ ...result, ...fontSizes.default ];
		}
	}

	// Ensure we use desired shape.
	return result.map( ( { size, slug } ) => ( {
		name: sprintf( '%1$s (%2$s)', size, slug ),
		slug,
		size,
	} ) );
};
