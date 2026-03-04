/**
 * External dependencies
 */
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { FontControl } from '@teydeastudio/components/src/font-control/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalBoxControl as BoxControl,
	PanelBody,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * PanelFooter component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelFooter component.
 */
export const PanelFooter = ( { presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { footer } = settings.data;
	const {
		alignment,
		fontFamily,
		fontSize,
		fontWeight,
		letterCase,
		lineHeight,
		marginBottom,
		marginLeft,
		marginRight,
		marginTop,
		paddingBottom,
		paddingLeft,
		paddingRight,
		paddingTop,
		textColor,
		textDecoration,
		text,
	} = footer;

	// Destructure the presets object.
	const { colorPalettes, fontFamilies, fontSizes, fontWeights, gradientPalettes } = presets;

	/**
	 * Helper setter
	 *
	 * @param {Object} setting Setting to update.
	 *
	 * @return {void}
	 */
	const setSetting = ( setting ) => {
		setSettings( {
			...settings,
			data: {
				...settings.data,
				footer: {
					...footer,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelFooter component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Footer', 'bm-custom-login' ) }>
			<FieldsGroup>
				<TextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Footer text', 'bm-custom-login' ) }
					value={ text }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							text: updatedValue,
						} );
					} }
				/>
				<hr />
				<SelectControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Text alignment', 'bm-custom-login' ) }
					value={ alignment }
					options={ [
						{
							value: 'left',
							label: __( 'Left', 'bm-custom-login' ),
						},
						{
							value: 'center',
							label: __( 'Center', 'bm-custom-login' ),
						},
						{
							value: 'right',
							label: __( 'Right', 'bm-custom-login' ),
						},
					] }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							alignment: updatedValue,
						} );
					} }
				/>
				<hr />
				<FieldsGroup label={ __( 'Colors', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ textColor }
						label={ __( 'Text', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								textColor: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
				</FieldsGroup>
				<hr />
				<FontControl
					values={ {
						fontFamily,
						fontSize,
						fontWeight,
						letterCase,
						lineHeight,
						textDecoration,
					} }
					options={ {
						fallbackSize: '13px',
						fontFamilies,
						fontSizes,
						fontWeights,
						withDecoration: true,
						withLetterCase: true,
					} }
					/**
					 * Update the values
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting( updatedValues );
					} }
				/>
				<hr />
				<BoxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Padding', 'bm-custom-login' ) }
					values={ {
						top: paddingTop,
						right: paddingRight,
						bottom: paddingBottom,
						left: paddingLeft,
					} }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues        Updated values.
					 * @param {string} updatedValues.top    Value for "top" side.
					 * @param {string} updatedValues.right  Value for "right" side.
					 * @param {string} updatedValues.bottom Value for "bottom" side.
					 * @param {string} updatedValues.left   Value for "left" side.
					 *
					 * @return {void}
					 */
					onChange={ ( { top, right, bottom, left } ) => {
						setSetting( {
							paddingTop: 'undefined' === typeof top ? '0px' : top,
							paddingRight: 'undefined' === typeof right ? '0px' : right,
							paddingBottom: 'undefined' === typeof bottom ? '0px' : bottom,
							paddingLeft: 'undefined' === typeof left ? '0px' : left,
						} );
					} }
					resetValues={ {
						top: '0px',
						right: '0px',
						bottom: '0px',
						left: '0px',
					} }
				/>
				<hr />
				<BoxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Margin', 'bm-custom-login' ) }
					values={ {
						top: marginTop,
						right: marginRight,
						bottom: marginBottom,
						left: marginLeft,
					} }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues        Updated values.
					 * @param {string} updatedValues.top    Value for "top" side.
					 * @param {string} updatedValues.right  Value for "right" side.
					 * @param {string} updatedValues.bottom Value for "bottom" side.
					 * @param {string} updatedValues.left   Value for "left" side.
					 *
					 * @return {void}
					 */
					onChange={ ( { top, right, bottom, left } ) => {
						setSetting( {
							marginTop: 'undefined' === typeof top ? '0px' : top,
							marginRight: 'undefined' === typeof right ? '0px' : right,
							marginBottom: 'undefined' === typeof bottom ? '0px' : bottom,
							marginLeft: 'undefined' === typeof left ? '0px' : left,
						} );
					} }
					resetValues={ {
						top: '1em',
						right: '0em',
						bottom: '1em',
						left: '0em',
					} }
				/>
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelFooter.propTypes = {
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
