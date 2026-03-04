/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { __experimentalBorderBoxControl as BorderBoxControl } from '@wordpress/components'; // eslint-disable-line @wordpress/no-unsafe-wp-apis

/**
 * BorderControl component
 *
 * @param {Object}   properties               Component properties object.
 * @param {Array}    properties.colorPalettes Predefined color palettes.
 * @param {string}   properties.label         Label.
 * @param {Function} properties.onChange      Function callback to trigger on value change.
 * @param {Object}   properties.values        Field's values.
 * @param {boolean}  properties.withAlpha     Whether the alpha choice should be allowed.
 *
 * @return {JSX} BorderControl component.
 */
export const BorderControl = ( { colorPalettes, label, onChange, values, withAlpha } ) => {
	const {
		borderBottomColor,
		borderBottomStyle,
		borderBottomWidth,
		borderLeftColor,
		borderLeftStyle,
		borderLeftWidth,
		borderRightColor,
		borderRightStyle,
		borderRightWidth,
		borderTopColor,
		borderTopStyle,
		borderTopWidth,
	} = values;

	return (
		<BorderBoxControl
			__next40pxDefaultSize
			colors={ colorPalettes }
			enableAlpha={ withAlpha }
			label={ label }
			/**
			 * Update the value
			 *
			 * Ensure the object structure uses expected,
			 * detailed shape.
			 *
			 * @param {Object} updatedValues Updated values.
			 *
			 * @return {void}
			 */
			onChange={ ( updatedValues ) => {
				updatedValues = {
					borderBottomColor: updatedValues?.bottom?.color ?? updatedValues?.color ?? '',
					borderBottomStyle: updatedValues?.bottom?.style ?? updatedValues.style ?? 'none',
					borderBottomWidth: updatedValues?.bottom?.width ?? updatedValues.width ?? '0px',
					borderLeftColor: updatedValues?.left?.color ?? updatedValues.color ?? '',
					borderLeftStyle: updatedValues?.left?.style ?? updatedValues.style ?? 'none',
					borderLeftWidth: updatedValues?.left?.width ?? updatedValues.width ?? '0px',
					borderRightColor: updatedValues?.right?.color ?? updatedValues.color ?? '',
					borderRightStyle: updatedValues?.right?.style ?? updatedValues.style ?? 'none',
					borderRightWidth: updatedValues?.right?.width ?? updatedValues.width ?? '0px',
					borderTopColor: updatedValues?.top?.color ?? updatedValues.color ?? '',
					borderTopStyle: updatedValues?.top?.style ?? updatedValues.style ?? 'none',
					borderTopWidth: updatedValues?.top?.width ?? updatedValues.width ?? '0px',
				};

				[ 'Bottom', 'Left', 'Right', 'Top' ].forEach( ( position ) => {
					if ( '0px' !== updatedValues[ `border${ position }Width` ] && 'none' === updatedValues[ `border${ position }Style` ] ) {
						updatedValues[ `border${ position }Style` ] = 'solid';
					} else if ( '0px' === updatedValues[ `border${ position }Width` ] && '' === updatedValues[ `border${ position }Color` ] ) {
						updatedValues[ `border${ position }Style` ] = 'none';
					}
				} );

				onChange( updatedValues );
			} }
			value={ {
				bottom: {
					color: borderBottomColor,
					style: borderBottomStyle,
					width: borderBottomWidth,
				},
				left: {
					color: borderLeftColor,
					style: borderLeftStyle,
					width: borderLeftWidth,
				},
				right: {
					color: borderRightColor,
					style: borderRightStyle,
					width: borderRightWidth,
				},
				top: {
					color: borderTopColor,
					style: borderTopStyle,
					width: borderTopWidth,
				},
			} }
		/>
	);
};

/**
 * Props validation
 */
BorderControl.propTypes = {
	colorPalettes: PropTypes.array.isRequired,
	label: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
	values: PropTypes.shape( {
		borderBottomColor: PropTypes.string.isRequired,
		borderBottomStyle: PropTypes.string.isRequired,
		borderBottomWidth: PropTypes.string.isRequired,
		borderLeftColor: PropTypes.string.isRequired,
		borderLeftStyle: PropTypes.string.isRequired,
		borderLeftWidth: PropTypes.string.isRequired,
		borderRightColor: PropTypes.string.isRequired,
		borderRightStyle: PropTypes.string.isRequired,
		borderRightWidth: PropTypes.string.isRequired,
		borderTopColor: PropTypes.string.isRequired,
		borderTopStyle: PropTypes.string.isRequired,
		borderTopWidth: PropTypes.string.isRequired,
	} ).isRequired,
	withAlpha: PropTypes.bool.isRequired,
};
