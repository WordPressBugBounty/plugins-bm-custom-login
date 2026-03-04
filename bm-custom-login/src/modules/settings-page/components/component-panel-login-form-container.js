/**
 * External dependencies
 */
import { AlignmentMatrixControl } from '@teydeastudio/components/src/alignment-matrix-control/index.js';
import { BorderControl } from '@teydeastudio/components/src/border-control/index.js';
import { BorderRadiusControl } from '@teydeastudio/components/src/border-radius-control/index.js';
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { IntegerControl } from '@teydeastudio/components/src/integer-control/index.js';
import { MediaControl } from '@teydeastudio/components/src/media-control/index.js';
import { ShadowControl } from '@teydeastudio/components/src/shadow-control/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalBoxControl as BoxControl,
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * PanelLoginFormContainer component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelLoginFormContainer component.
 */
export const PanelLoginFormContainer = ( { presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { loginFormContainer } = settings.data;
	const {
		alignment,
		backgroundColor,
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
		focalPointX,
		focalPointY,
		marginBottom,
		marginLeft,
		marginRight,
		marginTop,
		mediaId,
		paddingBottom,
		paddingLeft,
		paddingRight,
		paddingTop,
		shadow,
		shadowOnFocus,
		shadowOnHover,
		sizeRepeat,
		width,
		wrapLinksInContainer,
		wrapLogoInContainer,
		wrapperPaddingBottom,
		wrapperPaddingLeft,
		wrapperPaddingRight,
		wrapperPaddingTop,
	} = loginFormContainer;

	// Destructure the presets object.
	const { colorPalettes, gradientPalettes, shadowPresets } = presets;

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
				loginFormContainer: {
					...loginFormContainer,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelLoginFormContainer component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Login form container', 'bm-custom-login' ) }>
			<FieldsGroup>
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Place logo inside the form container', 'bm-custom-login' ) }
					checked={ wrapLogoInContainer }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							wrapLogoInContainer: ! wrapLogoInContainer,
						} );
					} }
				/>
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Place footer links inside the form container', 'bm-custom-login' ) }
					checked={ wrapLinksInContainer }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							wrapLinksInContainer: ! wrapLinksInContainer,
						} );
					} }
				/>
				<AlignmentMatrixControl
					label={ __( 'Position / alignment', 'bm-custom-login' ) }
					value={ alignment }
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
				{ 'default' !== alignment && (
					<BoxControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( "Wrapper's padding", 'bm-custom-login' ) }
						values={ {
							top: wrapperPaddingTop,
							right: wrapperPaddingRight,
							bottom: wrapperPaddingBottom,
							left: wrapperPaddingLeft,
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
								wrapperPaddingTop: 'undefined' === typeof top ? '0px' : top,
								wrapperPaddingRight: 'undefined' === typeof right ? '0px' : right,
								wrapperPaddingBottom: 'undefined' === typeof bottom ? '0px' : bottom,
								wrapperPaddingLeft: 'undefined' === typeof left ? '0px' : left,
							} );
						} }
						resetValues={ {
							top: '0px',
							right: '0px',
							bottom: '0px',
							left: '0px',
						} }
					/>
				) }
				<hr />
				<FieldsGroup label={ __( 'Background', 'bm-custom-login' ) } withBoxBorder withBaseControl>
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
				</FieldsGroup>
				<MediaControl
					allowedTypes={ [ 'image' ] }
					label={ __( 'background image', 'bm-custom-login' ) } // Lower case intentional.
					values={ {
						focalPointX,
						focalPointY,
						mediaId,
						sizeRepeat,
					} }
					withFocalPointPicker
					withSizeRepeatSelector
					/**
					 * Update the value
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
				<IntegerControl
					label={ __( 'Container width (px)', 'bm-custom-login' ) }
					help={ __( 'Default is 320px.', 'bm-custom-login' ) }
					min={ 0 }
					max={ 1000 }
					value={ width }
					defaultValue={ 320 }
					/**
					 * Update the value
					 *
					 * @param {number} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							width: updatedValue,
						} );
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
						setSetting( updatedValues );
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
						top: '26px',
						right: '24px',
						bottom: '26px',
						left: '24px',
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
						top: '24px',
						right: '0px',
						bottom: '24px',
						left: '0px',
					} }
				/>
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelLoginFormContainer.propTypes = {
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
