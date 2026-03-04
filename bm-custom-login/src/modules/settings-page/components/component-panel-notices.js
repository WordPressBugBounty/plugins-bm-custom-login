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
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * PanelNotices component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.context     Additional context object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelNotices component.
 */
export const PanelNotices = ( { context, presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { notices } = settings.data;
	const {
		borderBottomLeftRadius,
		borderBottomRightRadius,
		borderTopLeftRadius,
		borderTopRightRadius,
		customNoticeType,
		errorBackgroundColor,
		errorBorderBottomColor,
		errorBorderBottomStyle,
		errorBorderBottomWidth,
		errorBorderLeftColor,
		errorBorderLeftStyle,
		errorBorderLeftWidth,
		errorBorderRightColor,
		errorBorderRightStyle,
		errorBorderRightWidth,
		errorBorderTopColor,
		errorBorderTopStyle,
		errorBorderTopWidth,
		errorShadow,
		errorTextColor,
		fontFamily,
		fontSize,
		fontWeight,
		lineHeight,
		marginBottom,
		marginLeft,
		marginRight,
		marginTop,
		noticeBackgroundColor,
		noticeBorderBottomColor,
		noticeBorderBottomStyle,
		noticeBorderBottomWidth,
		noticeBorderLeftColor,
		noticeBorderLeftStyle,
		noticeBorderLeftWidth,
		noticeBorderRightColor,
		noticeBorderRightStyle,
		noticeBorderRightWidth,
		noticeBorderTopColor,
		noticeBorderTopStyle,
		noticeBorderTopWidth,
		noticeShadow,
		noticeTextColor,
		paddingBottom,
		paddingLeft,
		paddingRight,
		paddingTop,
		showCustomNotice,
		successBackgroundColor,
		successBorderBottomColor,
		successBorderBottomStyle,
		successBorderBottomWidth,
		successBorderLeftColor,
		successBorderLeftStyle,
		successBorderLeftWidth,
		successBorderRightColor,
		successBorderRightStyle,
		successBorderRightWidth,
		successBorderTopColor,
		successBorderTopStyle,
		successBorderTopWidth,
		successShadow,
		successTextColor,
	} = notices;

	// Destructure the presets object.
	const { colorPalettes, fontFamilies, fontSizes, fontWeights, gradientPalettes, shadowPresets } = presets;

	// Destructure the context object.
	const { anyoneCanRegister, languages, translations } = context;

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
				notices: {
					...notices,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelNotices component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Notices', 'bm-custom-login' ) }>
			<FieldsGroup>
				<FontControl
					values={ {
						fontFamily,
						fontSize,
						fontWeight,
						lineHeight,
					} }
					options={ {
						fallbackSize: '13px',
						fontFamilies,
						fontSizes,
						fontWeights,
						withDecoration: false,
						withLetterCase: false,
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
				<FieldsGroup label={ __( 'Colors (error)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ errorBackgroundColor }
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
								errorBackgroundColor: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ errorTextColor }
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
								errorTextColor: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
					/>
				</FieldsGroup>
				<FieldsGroup label={ __( 'Colors (notice)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ noticeBackgroundColor }
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
								noticeBackgroundColor: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ noticeTextColor }
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
								noticeTextColor: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
					/>
				</FieldsGroup>
				<FieldsGroup label={ __( 'Colors (success)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ successBackgroundColor }
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
								successBackgroundColor: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ successTextColor }
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
								successTextColor: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
					/>
				</FieldsGroup>
				<hr />
				<BorderControl
					values={ {
						borderBottomColor: errorBorderBottomColor,
						borderBottomStyle: errorBorderBottomStyle,
						borderBottomWidth: errorBorderBottomWidth,
						borderLeftColor: errorBorderLeftColor,
						borderLeftStyle: errorBorderLeftStyle,
						borderLeftWidth: errorBorderLeftWidth,
						borderRightColor: errorBorderRightColor,
						borderRightStyle: errorBorderRightStyle,
						borderRightWidth: errorBorderRightWidth,
						borderTopColor: errorBorderTopColor,
						borderTopStyle: errorBorderTopStyle,
						borderTopWidth: errorBorderTopWidth,
					} }
					label={ __( 'Border style (error)', 'bm-custom-login' ) }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting( {
							errorBorderBottomColor: updatedValues.borderBottomColor,
							errorBorderBottomStyle: updatedValues.borderBottomStyle,
							errorBorderBottomWidth: updatedValues.borderBottomWidth,
							errorBorderLeftColor: updatedValues.borderLeftColor,
							errorBorderLeftStyle: updatedValues.borderLeftStyle,
							errorBorderLeftWidth: updatedValues.borderLeftWidth,
							errorBorderRightColor: updatedValues.borderRightColor,
							errorBorderRightStyle: updatedValues.borderRightStyle,
							errorBorderRightWidth: updatedValues.borderRightWidth,
							errorBorderTopColor: updatedValues.borderTopColor,
							errorBorderTopStyle: updatedValues.borderTopStyle,
							errorBorderTopWidth: updatedValues.borderTopWidth,
						} );
					} }
					colorPalettes={ colorPalettes }
					withAlpha
				/>
				<BorderControl
					values={ {
						borderBottomColor: noticeBorderBottomColor,
						borderBottomStyle: noticeBorderBottomStyle,
						borderBottomWidth: noticeBorderBottomWidth,
						borderLeftColor: noticeBorderLeftColor,
						borderLeftStyle: noticeBorderLeftStyle,
						borderLeftWidth: noticeBorderLeftWidth,
						borderRightColor: noticeBorderRightColor,
						borderRightStyle: noticeBorderRightStyle,
						borderRightWidth: noticeBorderRightWidth,
						borderTopColor: noticeBorderTopColor,
						borderTopStyle: noticeBorderTopStyle,
						borderTopWidth: noticeBorderTopWidth,
					} }
					label={ __( 'Border style (notice)', 'bm-custom-login' ) }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting( {
							noticeBorderBottomColor: updatedValues.borderBottomColor,
							noticeBorderBottomStyle: updatedValues.borderBottomStyle,
							noticeBorderBottomWidth: updatedValues.borderBottomWidth,
							noticeBorderLeftColor: updatedValues.borderLeftColor,
							noticeBorderLeftStyle: updatedValues.borderLeftStyle,
							noticeBorderLeftWidth: updatedValues.borderLeftWidth,
							noticeBorderRightColor: updatedValues.borderRightColor,
							noticeBorderRightStyle: updatedValues.borderRightStyle,
							noticeBorderRightWidth: updatedValues.borderRightWidth,
							noticeBorderTopColor: updatedValues.borderTopColor,
							noticeBorderTopStyle: updatedValues.borderTopStyle,
							noticeBorderTopWidth: updatedValues.borderTopWidth,
						} );
					} }
					colorPalettes={ colorPalettes }
					withAlpha
				/>
				<BorderControl
					values={ {
						borderBottomColor: successBorderBottomColor,
						borderBottomStyle: successBorderBottomStyle,
						borderBottomWidth: successBorderBottomWidth,
						borderLeftColor: successBorderLeftColor,
						borderLeftStyle: successBorderLeftStyle,
						borderLeftWidth: successBorderLeftWidth,
						borderRightColor: successBorderRightColor,
						borderRightStyle: successBorderRightStyle,
						borderRightWidth: successBorderRightWidth,
						borderTopColor: successBorderTopColor,
						borderTopStyle: successBorderTopStyle,
						borderTopWidth: successBorderTopWidth,
					} }
					label={ __( 'Border style (success)', 'bm-custom-login' ) }
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValues Updated values.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValues ) => {
						setSetting( {
							successBorderBottomColor: updatedValues.borderBottomColor,
							successBorderBottomStyle: updatedValues.borderBottomStyle,
							successBorderBottomWidth: updatedValues.borderBottomWidth,
							successBorderLeftColor: updatedValues.borderLeftColor,
							successBorderLeftStyle: updatedValues.borderLeftStyle,
							successBorderLeftWidth: updatedValues.borderLeftWidth,
							successBorderRightColor: updatedValues.borderRightColor,
							successBorderRightStyle: updatedValues.borderRightStyle,
							successBorderRightWidth: updatedValues.borderRightWidth,
							successBorderTopColor: updatedValues.borderTopColor,
							successBorderTopStyle: updatedValues.borderTopStyle,
							successBorderTopWidth: updatedValues.borderTopWidth,
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
					label={ __( 'Shadow (error)', 'bm-custom-login' ) }
					value={ errorShadow }
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
							errorShadow: updatedValue,
						} );
					} }
				/>
				<ShadowControl
					label={ __( 'Shadow (notice)', 'bm-custom-login' ) }
					value={ noticeShadow }
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
							noticeShadow: updatedValue,
						} );
					} }
				/>
				<ShadowControl
					label={ __( 'Shadow (success)', 'bm-custom-login' ) }
					value={ successShadow }
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
							successShadow: updatedValue,
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
						top: '12px',
						right: '12px',
						bottom: '12px',
						left: '12px',
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
						top: '0px',
						right: '0px',
						bottom: '20px',
						left: '0px',
					} }
				/>
				<hr />
				<MultilingualTextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Notice content for the password reset screen', 'bm-custom-login' ) }
					original={
						translations?.[
							'Please enter your username or email address. You will receive an email message with instructions on how to reset your password.'
						]
					}
					values={ Object.fromEntries(
						languages.map( ( language ) => [ language, notices?.[ sprintf( 'noticePasswordReset.%s', language ) ] ?? '' ] )
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
							Object.fromEntries( languages.map( ( language ) => [ sprintf( 'noticePasswordReset.%s', language ), updatedValues[ language ] ] ) )
						);
					} }
				/>
				{ anyoneCanRegister && (
					<Fragment>
						<hr />
						<MultilingualTextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( '"Register For This Site" notice content', 'bm-custom-login' ) }
							original={ translations?.[ 'Register For This Site.' ] }
							values={ Object.fromEntries(
								languages.map( ( language ) => [ language, notices?.[ sprintf( 'noticeRegister.%s', language ) ] ?? '' ] )
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
										languages.map( ( language ) => [ sprintf( 'noticeRegister.%s', language ), updatedValues[ language ] ] )
									)
								);
							} }
						/>
					</Fragment>
				) }
				<hr />
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Show custom notice above the login form', 'bm-custom-login' ) }
					checked={ showCustomNotice }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							showCustomNotice: ! showCustomNotice,
						} );
					} }
				/>
				{ showCustomNotice && (
					<Fragment>
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Custom notice type', 'bm-custom-login' ) }
							value={ customNoticeType }
							options={ [
								{
									value: 'error',
									label: __( 'Error', 'bm-custom-login' ),
								},
								{
									value: 'notice',
									label: __( 'Notice', 'bm-custom-login' ),
								},
								{
									value: 'success',
									label: __( 'Success', 'bm-custom-login' ),
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
									customNoticeType: updatedValue,
								} );
							} }
						/>
						<MultilingualTextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Content of the custom notice', 'bm-custom-login' ) }
							values={ Object.fromEntries(
								languages.map( ( language ) => [ language, notices?.[ sprintf( 'noticeCustom.%s', language ) ] ?? '' ] )
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
									Object.fromEntries( languages.map( ( language ) => [ sprintf( 'noticeCustom.%s', language ), updatedValues[ language ] ] ) )
								);
							} }
						/>
					</Fragment>
				) }
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelNotices.propTypes = {
	context: PropTypes.object.isRequired,
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
