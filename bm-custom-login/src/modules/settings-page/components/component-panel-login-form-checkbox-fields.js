/**
 * External dependencies
 */
import { BorderControl } from '@teydeastudio/components/src/border-control/index.js';
import { BorderRadiusControl } from '@teydeastudio/components/src/border-radius-control/index.js';
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { IconControl } from '@teydeastudio/components/src/icon-control/index.js';
import { IntegerControl } from '@teydeastudio/components/src/integer-control/index.js';
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
import { __ } from '@wordpress/i18n';

/**
 * PanelLoginFormCheckboxFields component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelLoginFormCheckboxFields component.
 */
export const PanelLoginFormCheckboxFields = ( { presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { loginFormCheckboxFields } = settings.data;
	const {
		backgroundColor,
		backgroundColorChecked,
		backgroundColorOnFocus,
		backgroundColorOnFocusChecked,
		backgroundColorOnHover,
		backgroundColorOnHoverChecked,
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
		fieldMarginBottom,
		fieldMarginLeft,
		fieldMarginRight,
		fieldMarginTop,
		fieldSize,
		icon,
		iconColor,
		iconColorOnFocus,
		iconColorOnHover,
		iconMarginBottom,
		iconMarginLeft,
		iconMarginRight,
		iconMarginTop,
		iconSize,
		shadow,
		shadowOnFocus,
		shadowOnHover,
	} = loginFormCheckboxFields;

	// Destructure the presets object.
	const { colorPalettes, shadowPresets } = presets;

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
				loginFormCheckboxFields: {
					...loginFormCheckboxFields,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelLoginFormCheckboxFields component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Login form checkbox field(s)', 'bm-custom-login' ) }>
			<FieldsGroup>
				<IconControl
					label={ __( '"Checked" icon', 'bm-custom-login' ) }
					value={ icon }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							icon: updatedValue,
						} );
					} }
					preset="check"
				/>
				<hr />
				<FieldsGroup label={ __( 'Colors', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ iconColor }
						label={ __( 'Icon', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								iconColor: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
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
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ backgroundColorChecked }
						label={ __( 'Background (when checked)', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								backgroundColorChecked: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
				</FieldsGroup>
				<FieldsGroup label={ __( 'Colors (on hover)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ iconColorOnHover }
						label={ __( 'Icon', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								iconColorOnHover: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
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
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ backgroundColorOnHoverChecked }
						label={ __( 'Background (when checked)', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								backgroundColorOnHoverChecked: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
				</FieldsGroup>
				<FieldsGroup label={ __( 'Colors (on focus)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ iconColorOnFocus }
						label={ __( 'Icon', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								iconColorOnFocus: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
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
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ backgroundColorOnFocusChecked }
						label={ __( 'Background (when checked)', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								backgroundColorOnFocusChecked: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
				</FieldsGroup>
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
				<IntegerControl
					label={ __( 'Field size (width & height, in pixels)', 'bm-custom-login' ) }
					min={ 0 }
					max={ 50 }
					value={ fieldSize }
					defaultValue={ 16 }
					/**
					 * Update the value
					 *
					 * @param {number} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							fieldSize: updatedValue,
						} );
					} }
				/>
				<BoxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( "Field's margin", 'bm-custom-login' ) }
					values={ {
						top: fieldMarginTop,
						right: fieldMarginRight,
						bottom: fieldMarginBottom,
						left: fieldMarginLeft,
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
							fieldMarginTop: 'undefined' === typeof top ? '0px' : top,
							fieldMarginRight: 'undefined' === typeof right ? '0px' : right,
							fieldMarginBottom: 'undefined' === typeof bottom ? '0px' : bottom,
							fieldMarginLeft: 'undefined' === typeof left ? '0px' : left,
						} );
					} }
					resetValues={ {
						top: '-.25rem',
						right: '.25rem',
						bottom: '0rem',
						left: '0rem',
					} }
				/>
				<hr />
				<IntegerControl
					label={ __( 'Icon size (width & height, in pixels)', 'bm-custom-login' ) }
					min={ 0 }
					max={ 50 }
					value={ iconSize }
					defaultValue={ 21 }
					/**
					 * Update the value
					 *
					 * @param {number} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							iconSize: updatedValue,
						} );
					} }
				/>
				<BoxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( "Icon's margin", 'bm-custom-login' ) }
					values={ {
						top: iconMarginTop,
						right: iconMarginRight,
						bottom: iconMarginBottom,
						left: iconMarginLeft,
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
							iconMarginTop: 'undefined' === typeof top ? '0px' : top,
							iconMarginRight: 'undefined' === typeof right ? '0px' : right,
							iconMarginBottom: 'undefined' === typeof bottom ? '0px' : bottom,
							iconMarginLeft: 'undefined' === typeof left ? '0px' : left,
						} );
					} }
					resetValues={ {
						top: '-3px',
						right: '0px',
						bottom: '0px',
						left: '-4px',
					} }
				/>
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelLoginFormCheckboxFields.propTypes = {
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
