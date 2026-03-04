/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalTextDecorationControl as TextDecorationControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalTextTransformControl as TextTransformControl,
} from '@wordpress/block-editor';
import { FontSizePicker, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FieldsGroup } from '../fields-group/index.js';
import { LineHeightControl } from '../line-height-control/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * FontControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 * @param {Object}   properties.options  Preset options for font family and font size selectors.
 * @param {string}   properties.values   Object containing values for all associated subfields.
 *
 * @return {JSX} FontControl component.
 */
export const FontControl = ( { onChange, options, values } ) => {
	// Destructure the objects.
	const { fallbackSize, fontFamilies, fontSizes, fontWeights, label, withDecoration, withLetterCase } = options;
	const { fontFamily, fontSize, fontWeight, letterCase, lineHeight, textDecoration } = values;

	/**
	 * Determine default font weight
	 *
	 * @param {Array} availableFontWeights Available font weights.
	 *
	 * @return {string} Default font weight.
	 */
	const determineDefaultFontWeight = ( availableFontWeights ) => {
		// Try to use "400", if available.
		if ( availableFontWeights.includes( '400' ) ) {
			return '400';
		}

		// Fallback to first available weight.
		return availableFontWeights[ 0 ];
	};

	/**
	 * Render the component
	 */
	return (
		<FieldsGroup className="tsc-font-control">
			<SelectControl
				__nextHasNoMarginBottom
				__next40pxDefaultSize
				label={ label ? label : __( 'Font family', 'bm-custom-login' ) }
				value={ fontFamily }
				options={ fontFamilies }
				/**
				 * Update the value
				 *
				 * @param {string} updatedValue Updated value.
				 *
				 * @return {void}
				 */
				onChange={ ( updatedValue ) => {
					const availableFontWeights = ( fontWeights?.[ updatedValue ] ?? [] ).map( ( { value } ) => value );

					onChange( {
						...values,
						fontFamily: updatedValue,
						fontWeight: availableFontWeights.includes( fontWeight ) ? fontWeight : determineDefaultFontWeight( availableFontWeights ),
					} );
				} }
			/>
			<SelectControl
				__nextHasNoMarginBottom
				__next40pxDefaultSize
				label={ __( 'Font weight', 'bm-custom-login' ) }
				value={ fontWeight }
				options={ fontWeights[ fontFamily ] ?? [] }
				/**
				 * Update the value
				 *
				 * @param {string} updatedValue Updated value.
				 *
				 * @return {void}
				 */
				onChange={ ( updatedValue ) => {
					onChange( {
						...values,
						fontWeight: updatedValue,
					} );
				} }
			/>
			<FontSizePicker
				__next40pxDefaultSize
				fontSizes={ fontSizes }
				value={ fontSize }
				fallbackFontSize={ fallbackSize }
				withReset={ false }
				withSlider={ true }
				/**
				 * Update the value
				 *
				 * @param {string} updatedValue Updated value.
				 *
				 * @return {void}
				 */
				onChange={ ( updatedValue ) => {
					onChange( {
						...values,
						fontSize: updatedValue,
					} );
				} }
			/>
			<LineHeightControl
				value={ lineHeight }
				/**
				 * Update the value
				 *
				 * @param {number} updatedValue Updated value.
				 *
				 * @return {void}
				 */
				onChange={ ( updatedValue ) => {
					onChange( {
						...values,
						lineHeight: updatedValue,
					} );
				} }
			/>
			{ withLetterCase && (
				<TextTransformControl
					value={ letterCase }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( {
							...values,
							letterCase: updatedValue,
						} );
					} }
				/>
			) }
			{ withDecoration && (
				<TextDecorationControl
					value={ textDecoration }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( {
							...values,
							textDecoration: updatedValue,
						} );
					} }
				/>
			) }
		</FieldsGroup>
	);
};

/**
 * Props validation
 */
FontControl.propTypes = {
	onChange: PropTypes.func.isRequired,
	options: PropTypes.shape( {
		fallbackSize: PropTypes.string.isRequired,
		fontFamilies: PropTypes.array.isRequired,
		fontSizes: PropTypes.array.isRequired,
		fontWeights: PropTypes.object.isRequired,
		label: PropTypes.string,
		withDecoration: PropTypes.bool.isRequired,
		withLetterCase: PropTypes.bool.isRequired,
	} ).isRequired,
	values: PropTypes.shape( {
		fontFamily: PropTypes.string.isRequired,
		fontSize: PropTypes.string.isRequired,
		fontWeight: PropTypes.string.isRequired,
		letterCase: PropTypes.string,
		lineHeight: PropTypes.number,
		textDecoration: PropTypes.string,
	} ).isRequired,
};
