/**
 * External dependencies
 */
import { BorderControl } from '@teydeastudio/components/src/border-control/index.js';
import { BorderRadiusControl } from '@teydeastudio/components/src/border-radius-control/index.js';
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { FontControl } from '@teydeastudio/components/src/font-control/index.js';
import { MultilingualTextControl } from '@teydeastudio/components/src/multilingual-text-control/index.js';
import { ShadowControl } from '@teydeastudio/components/src/shadow-control/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalBoxControl as BoxControl,
	PanelBody,
} from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * PanelLoginFormButtonSecondary component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.context     Additional context object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelLoginFormButtonSecondary component.
 */
export const PanelLoginFormButtonSecondary = ( { context, presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { loginFormButtonSecondary } = settings.data;
	const {
		backgroundColor,
		backgroundColorOnFocus,
		backgroundColorOnHover,
		borderBottomColor,
		borderBottomColorOnFocus,
		borderBottomColorOnHover,
		borderBottomLeftRadius,
		borderBottomRightRadius,
		borderBottomStyle,
		borderBottomStyleOnFocus,
		borderBottomStyleOnHover,
		borderBottomWidth,
		borderBottomWidthOnFocus,
		borderBottomWidthOnHover,
		borderLeftColor,
		borderLeftColorOnFocus,
		borderLeftColorOnHover,
		borderLeftStyle,
		borderLeftStyleOnFocus,
		borderLeftStyleOnHover,
		borderLeftWidth,
		borderLeftWidthOnFocus,
		borderLeftWidthOnHover,
		borderRightColor,
		borderRightColorOnFocus,
		borderRightColorOnHover,
		borderRightStyle,
		borderRightStyleOnFocus,
		borderRightStyleOnHover,
		borderRightWidth,
		borderRightWidthOnFocus,
		borderRightWidthOnHover,
		borderTopColor,
		borderTopColorOnFocus,
		borderTopColorOnHover,
		borderTopLeftRadius,
		borderTopRightRadius,
		borderTopStyle,
		borderTopStyleOnFocus,
		borderTopStyleOnHover,
		borderTopWidth,
		borderTopWidthOnFocus,
		borderTopWidthOnHover,
		paddingBottom,
		paddingLeft,
		paddingRight,
		paddingTop,
		shadow,
		shadowOnFocus,
		shadowOnHover,
		textColor,
		textColorOnFocus,
		textColorOnHover,
		fontFamily,
		fontSize,
		fontWeight,
		letterCase,
		lineHeight,
	} = loginFormButtonSecondary;

	// Destructure the presets object.
	const { colorPalettes, fontFamilies, fontSizes, fontWeights, gradientPalettes, shadowPresets } = presets;

	// Destructure the context object.
	const { languages, translations } = context;

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
				loginFormButtonSecondary: {
					...loginFormButtonSecondary,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelLoginFormButtonSecondary component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Login form button (secondary)', 'bm-custom-login' ) }>
			<FieldsGroup>
				<FieldsGroup label={ __( 'Colors', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ backgroundColor }
						label={ __( 'Background', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								backgroundColor: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
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
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
					/>
				</FieldsGroup>
				<FieldsGroup label={ __( 'Colors (on hover)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ backgroundColorOnHover }
						label={ __( 'Background', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								backgroundColorOnHover: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ textColorOnHover }
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
								textColorOnHover: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
					/>
				</FieldsGroup>
				<FieldsGroup label={ __( 'Colors (on focus)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ backgroundColorOnFocus }
						label={ __( 'Background', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								backgroundColorOnFocus: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ textColorOnFocus }
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
								textColorOnFocus: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
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
					} }
					options={ {
						fallbackSize: '13px',
						fontFamilies,
						fontSizes,
						fontWeights,
						label: __( 'Font family', 'bm-custom-login' ),
						withDecoration: false,
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
				<BorderControl
					values={ {
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
					} }
					label={ __( 'Border style', 'bm-custom-login' ) }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting( {
							borderBottomColor: updatedValues.borderBottomColor,
							borderBottomStyle: updatedValues.borderBottomStyle,
							borderBottomWidth: updatedValues.borderBottomWidth,
							borderLeftColor: updatedValues.borderLeftColor,
							borderLeftStyle: updatedValues.borderLeftStyle,
							borderLeftWidth: updatedValues.borderLeftWidth,
							borderRightColor: updatedValues.borderRightColor,
							borderRightStyle: updatedValues.borderRightStyle,
							borderRightWidth: updatedValues.borderRightWidth,
							borderTopColor: updatedValues.borderTopColor,
							borderTopStyle: updatedValues.borderTopStyle,
							borderTopWidth: updatedValues.borderTopWidth,
						} );
					} }
					colorPalettes={ colorPalettes }
					withAlpha
				/>
				<BorderControl
					values={ {
						borderBottomColor: borderBottomColorOnHover,
						borderBottomStyle: borderBottomStyleOnHover,
						borderBottomWidth: borderBottomWidthOnHover,
						borderLeftColor: borderLeftColorOnHover,
						borderLeftStyle: borderLeftStyleOnHover,
						borderLeftWidth: borderLeftWidthOnHover,
						borderRightColor: borderRightColorOnHover,
						borderRightStyle: borderRightStyleOnHover,
						borderRightWidth: borderRightWidthOnHover,
						borderTopColor: borderTopColorOnHover,
						borderTopStyle: borderTopStyleOnHover,
						borderTopWidth: borderTopWidthOnHover,
					} }
					label={ __( 'Border style (on hover)', 'bm-custom-login' ) }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting( {
							borderBottomColorOnHover: updatedValues.borderBottomColor,
							borderBottomStyleOnHover: updatedValues.borderBottomStyle,
							borderBottomWidthOnHover: updatedValues.borderBottomWidth,
							borderLeftColorOnHover: updatedValues.borderLeftColor,
							borderLeftStyleOnHover: updatedValues.borderLeftStyle,
							borderLeftWidthOnHover: updatedValues.borderLeftWidth,
							borderRightColorOnHover: updatedValues.borderRightColor,
							borderRightStyleOnHover: updatedValues.borderRightStyle,
							borderRightWidthOnHover: updatedValues.borderRightWidth,
							borderTopColorOnHover: updatedValues.borderTopColor,
							borderTopStyleOnHover: updatedValues.borderTopStyle,
							borderTopWidthOnHover: updatedValues.borderTopWidth,
						} );
					} }
					colorPalettes={ colorPalettes }
					withAlpha
				/>
				<BorderControl
					values={ {
						borderBottomColor: borderBottomColorOnFocus,
						borderBottomStyle: borderBottomStyleOnFocus,
						borderBottomWidth: borderBottomWidthOnFocus,
						borderLeftColor: borderLeftColorOnFocus,
						borderLeftStyle: borderLeftStyleOnFocus,
						borderLeftWidth: borderLeftWidthOnFocus,
						borderRightColor: borderRightColorOnFocus,
						borderRightStyle: borderRightStyleOnFocus,
						borderRightWidth: borderRightWidthOnFocus,
						borderTopColor: borderTopColorOnFocus,
						borderTopStyle: borderTopStyleOnFocus,
						borderTopWidth: borderTopWidthOnFocus,
					} }
					label={ __( 'Border style (on focus)', 'bm-custom-login' ) }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting( {
							borderBottomColorOnFocus: updatedValues.borderBottomColor,
							borderBottomStyleOnFocus: updatedValues.borderBottomStyle,
							borderBottomWidthOnFocus: updatedValues.borderBottomWidth,
							borderLeftColorOnFocus: updatedValues.borderLeftColor,
							borderLeftStyleOnFocus: updatedValues.borderLeftStyle,
							borderLeftWidthOnFocus: updatedValues.borderLeftWidth,
							borderRightColorOnFocus: updatedValues.borderRightColor,
							borderRightStyleOnFocus: updatedValues.borderRightStyle,
							borderRightWidthOnFocus: updatedValues.borderRightWidth,
							borderTopColorOnFocus: updatedValues.borderTopColor,
							borderTopStyleOnFocus: updatedValues.borderTopStyle,
							borderTopWidthOnFocus: updatedValues.borderTopWidth,
						} );
					} }
					colorPalettes={ colorPalettes }
					withAlpha
				/>
				<BorderRadiusControl
					value={ {
						bottomLeft: borderBottomLeftRadius,
						bottomRight: borderBottomRightRadius,
						topLeft: borderTopLeftRadius,
						topRight: borderTopRightRadius,
					} }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValue             Updated value.
					 * @param {string} updatedValue.bottomLeft  Value for bottom left corner.
					 * @param {string} updatedValue.bottomRight Value for bottom right corner.
					 * @param {string} updatedValue.topLeft     Value for top left corner.
					 * @param {string} updatedValue.topRight    Value for top right corner.
					 *
					 * @return {void}
					 */
					onChange={ ( { bottomLeft, bottomRight, topLeft, topRight } ) => {
						setSetting( {
							borderBottomLeftRadius: bottomLeft,
							borderBottomRightRadius: bottomRight,
							borderTopLeftRadius: topLeft,
							borderTopRightRadius: topRight,
						} );
					} }
				/>
				<hr />
				<ShadowControl
					label={ __( 'Shadow', 'bm-custom-login' ) }
					value={ shadow }
					presets={ shadowPresets }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							shadow: updatedValue,
						} );
					} }
				/>
				<ShadowControl
					label={ __( 'Shadow (on hover)', 'bm-custom-login' ) }
					value={ shadowOnHover }
					presets={ shadowPresets }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							shadowOnHover: updatedValue,
						} );
					} }
				/>
				<ShadowControl
					label={ __( 'Shadow (on focus)', 'bm-custom-login' ) }
					value={ shadowOnFocus }
					presets={ shadowPresets }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							shadowOnFocus: updatedValue,
						} );
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
						right: '10px',
						bottom: '0px',
						left: '10px',
					} }
				/>
				<hr />
				<MultilingualTextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( '"Change" button text', 'bm-custom-login' ) }
					original={ translations?.Change }
					values={ Object.fromEntries(
						languages.map( ( language ) => [ language, loginFormButtonSecondary?.[ sprintf( 'labelChange.%s', language ) ] ?? '' ] )
					) }
					/**
					 * Update the values
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting(
							Object.fromEntries( languages.map( ( language ) => [ sprintf( 'labelChange.%s', language ), updatedValues[ language ] ] ) )
						);
					} }
				/>
				<hr />
				<MultilingualTextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( '"Generate Password" button text', 'bm-custom-login' ) }
					original={ translations?.GeneratePassword }
					values={ Object.fromEntries(
						languages.map( ( language ) => [ language, loginFormButtonSecondary?.[ sprintf( 'labelGeneratePassword.%s', language ) ] ?? '' ] )
					) }
					/**
					 * Update the values
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting(
							Object.fromEntries(
								languages.map( ( language ) => [ sprintf( 'labelGeneratePassword.%s', language ), updatedValues[ language ] ] )
							)
						);
					} }
				/>
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelLoginFormButtonSecondary.propTypes = {
	context: PropTypes.object.isRequired,
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
