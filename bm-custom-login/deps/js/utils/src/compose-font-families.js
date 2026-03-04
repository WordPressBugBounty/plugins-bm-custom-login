/**
 * Compose the font families
 *
 * @param {Object} fontFamilies Font families object.
 *
 * @return {Array} Font families array.
 */
export const composeFontFamilies = ( fontFamilies ) =>
	Object.values( fontFamilies ).map( ( { name, slug } ) => ( {
		label: name,
		value: slug,
	} ) );
