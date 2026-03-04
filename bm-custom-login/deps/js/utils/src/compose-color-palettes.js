/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Compose the color palettes
 *
 * @param {Object} colorPalettes Color palettes object.
 * @param {Object} settings      Plugin settings.
 *
 * @return {Array} Color palettes array.
 */
export const composeColorPalettes = ( colorPalettes, settings ) => {
	// Define the result array.
	const result = [];

	if ( null !== colorPalettes ) {
		/**
		 * Color palette defined in theme.json
		 */
		if ( 'object' === typeof colorPalettes?.theme ) {
			result.push( {
				name: __( 'Theme palette', 'bm-custom-login' ),
				colors: colorPalettes.theme,
			} );
		}

		/**
		 * Default color palette defined in
		 * WordPress core
		 */
		if ( 'object' === typeof colorPalettes?.default ) {
			result.push( {
				name: __( 'Default core palette', 'bm-custom-login' ),
				colors: colorPalettes.default,
			} );
		}
	}

	// Add default color palette used in the login screen.
	result.push( {
		name: __( 'WordPress colors', 'bm-custom-login' ),
		colors: [
			{
				name: __( 'Accent', 'bm-custom-login' ),
				slug: 'accent',
				color: '#2271b1',
			},
			{
				name: __( 'Accent (active)', 'bm-custom-login' ),
				slug: 'accent-active',
				color: '#135e96',
			},
			{
				name: __( 'Accent (active dark)', 'bm-custom-login' ),
				slug: 'accent-active-dark',
				color: '#0a4b78',
			},
			{
				name: __( 'Background', 'bm-custom-login' ),
				slug: 'background',
				color: '#f0f0f1',
			},
			{
				name: __( 'Border', 'bm-custom-login' ),
				slug: 'border',
				color: '#c3c4c7',
			},
			{
				name: __( "Field's text", 'bm-custom-login' ),
				slug: 'text',
				color: '#2c3338',
			},
			{
				name: __( 'Label', 'bm-custom-login' ),
				slug: 'label',
				color: '#3c434a',
			},
		],
	} );

	/**
	 * Collect the list of "known" colors so that we will
	 * not include them in a custom colors palette
	 */
	const knownColors = [];

	result.forEach( ( palette ) => {
		palette.colors.forEach( ( color ) => {
			knownColors.push( color.color.toLowerCase() );
		} );
	} );

	/**
	 * Custom colors palette
	 */
	const customColors = [];

	// Color-related keys.
	const colorKeys = [
		'color',
		'backgroundColor',
		'backgroundColorChecked',
		'backgroundColorOnFocus',
		'backgroundColorOnFocusChecked',
		'backgroundColorOnHover',
		'backgroundColorOnHoverChecked',
		'borderBottomColor',
		'borderBottomColorOnFocus',
		'borderBottomColorOnHover',
		'borderLeftColor',
		'borderLeftColorOnFocus',
		'borderLeftColorOnHover',
		'borderRightColor',
		'borderRightColorOnFocus',
		'borderRightColorOnHover',
		'borderTopColor',
		'borderTopColorOnFocus',
		'borderTopColorOnHover',
		'errorBackgroundColor',
		'errorBorderBottomColor',
		'errorBorderLeftColor',
		'errorBorderRightColor',
		'errorBorderTopColor',
		'errorTextColor',
		'eyeIconColor',
		'eyeIconColorOnHover',
		'eyeIconColorOnFocus',
		'iconColor',
		'iconColorOnFocus',
		'iconColorOnHover',
		'linkColor',
		'linkColorOnFocus',
		'linkColorOnHover',
		'noticeBackgroundColor',
		'noticeBorderBottomColor',
		'noticeBorderLeftColor',
		'noticeBorderRightColor',
		'noticeBorderTopColor',
		'noticeTextColor',
		'separatorColor',
		'successBackgroundColor',
		'successBorderBottomColor',
		'successBorderLeftColor',
		'successBorderRightColor',
		'successBorderTopColor',
		'successTextColor',
		'textColor',
		'textColorOnFocus',
		'textColorOnHover',
	];

	// Iterate through settings to find custom colors.
	Object.values( settings?.data ?? {} ).forEach( ( setting ) => {
		if ( setting && 'object' === typeof setting ) {
			for ( const [ key, value ] of Object.entries( setting ) ) {
				if ( 'string' === typeof value && '' !== value && colorKeys.includes( key ) && ! knownColors.includes( value?.toLowerCase() ) ) {
					const color = value.toLowerCase();

					customColors.push( color );
					knownColors.push( color );
				}
			}
		}
	} );

	// If there are custom colors, add them to the result.
	if ( 0 < customColors.length ) {
		result.push( {
			name: __( 'Custom colors', 'bm-custom-login' ),
			colors: customColors.map( ( color ) => ( {
				name: color,
				slug: color,
				color,
			} ) ),
		} );
	}

	return result;
};
