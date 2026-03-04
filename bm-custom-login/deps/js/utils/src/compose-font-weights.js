/**
 * Internal dependencies
 */
import { composeFontWeightName } from './compose-font-weight-name.js';

/**
 * Compose the font weights
 *
 * @param {Object} fontFamilies Font families object.
 *
 * @return {Array} Font weights array.
 */
export const composeFontWeights = ( fontFamilies ) => {
	const results = {};

	Object.values( fontFamilies ).forEach( ( { slug, weights } ) => {
		const result = [];

		weights.forEach( ( fontWeight ) => {
			result.push( {
				label: composeFontWeightName( fontWeight.toString() ),
				value: fontWeight,
			} );
		} );

		results[ slug ] = result;
	} );

	return results;
};
