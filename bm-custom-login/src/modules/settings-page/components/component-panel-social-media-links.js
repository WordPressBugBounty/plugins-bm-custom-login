/**
 * External dependencies
 */
import { BorderControl } from '@teydeastudio/components/src/border-control/index.js';
import { BorderRadiusControl } from '@teydeastudio/components/src/border-radius-control/index.js';
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { SortableContainer } from '@teydeastudio/components/src/sortable-container/index.js';
import { ShadowControl } from '@teydeastudio/components/src/shadow-control/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalBoxControl as BoxControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
	PanelBody,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { SocialMediaLink } from './component-social-media-link.js';

/**
 * PanelSocialMediaLinks component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelSocialMediaLinks component.
 */
export const PanelSocialMediaLinks = ( { presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { socialMediaLinks, socialMediaLinksList } = settings.data;
	const {
		alignment,
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
		gap,
		iconColor,
		iconColorOnFocus,
		iconColorOnHover,
		iconSize,
		marginBottom,
		marginLeft,
		marginRight,
		marginTop,
		paddingBottom,
		paddingLeft,
		paddingRight,
		paddingTop,
		placement,
		shadow,
		shadowOnFocus,
		shadowOnHover,
		show,
	} = socialMediaLinks;

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
				socialMediaLinks: {
					...socialMediaLinks,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelSocialMediaLinks component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Social media links', 'bm-custom-login' ) }>
			<FieldsGroup>
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Display social media links', 'bm-custom-login' ) }
					help={ __( 'Social media links will be displayed at the bottom of the login screen.', 'bm-custom-login' ) }
					checked={ show }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							show: ! show,
						} );
					} }
				/>
				{ show && (
					<Fragment>
						<hr />
						<SortableContainer
							addLabel={ __( 'Add new social media link', 'bm-custom-login' ) }
							ItemComponent={ SocialMediaLink }
							items={ socialMediaLinksList }
							label={ __( 'Social media links', 'bm-custom-login' ) }
							/**
							 * Handle adding new item
							 *
							 * @return {void}
							 */
							onAdd={ () => {
								const itemKey = `d:${ Date.now().toString() }0000`;
								const { socialMediaLink: itemTemplate } = settings.templates.socialMediaLinksList;

								setSettings( {
									...settings,
									data: {
										...settings.data,
										socialMediaLinksList: {
											...socialMediaLinksList,
											[ itemKey ]: Object.assign(
												{},
												{
													key: itemKey,
													...itemTemplate,
												}
											),
										},
									},
								} );
							} }
							/**
							 * Update the value
							 *
							 * @param {Object} updatedValues Updated values.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValues ) => {
								setSettings( {
									...settings,
									data: {
										...settings.data,
										socialMediaLinksList: updatedValues,
									},
								} );
							} }
							placeholder={ <p>{ __( 'No social media links set yet.', 'bm-custom-login' ) }</p> }
						/>
						<hr />
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Placement', 'bm-custom-login' ) }
							value={ placement }
							options={ [
								{
									value: 'above_logo',
									label: __( 'Above logo', 'bm-custom-login' ),
								},
								{
									value: 'above_form',
									label: __( 'Above form', 'bm-custom-login' ),
								},
								{
									value: 'above_inline_links',
									label: __( 'Above inline links', 'bm-custom-login' ),
								},
								{
									value: 'above_privacy_policy_link',
									label: __( 'Above privacy policy link', 'bm-custom-login' ),
								},
								{
									value: 'above_language_switcher',
									label: __( 'Above language switcher', 'bm-custom-login' ),
								},
								{
									value: 'at_the_bottom',
									label: __( 'At the bottom', 'bm-custom-login' ),
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
									placement: updatedValue,
								} );
							} }
						/>
						<hr />
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Alignment', 'bm-custom-login' ) }
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
								gradientPalettes={ gradientPalettes }
								colorPalettes={ colorPalettes }
								withAlpha
								withColor
								withGradient
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
								colorPalettes={ colorPalettes }
								withAlpha
								withColor
								withGradient
							/>
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
								gradientPalettes={ gradientPalettes }
								colorPalettes={ colorPalettes }
								withAlpha
								withColor
								withGradient
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
								colorPalettes={ colorPalettes }
								withAlpha
								withColor
								withGradient
							/>
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
								gradientPalettes={ gradientPalettes }
								colorPalettes={ colorPalettes }
								withAlpha
								withColor
								withGradient
							/>
						</FieldsGroup>
						<hr />
						<UnitControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Icon width & height', 'bm-custom-login' ) }
							/**
							 * Update the value
							 *
							 * @param {string} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								setSetting( {
									iconSize: updatedValue,
								} );
							} }
							value={ iconSize }
						/>
						<hr />
						<UnitControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Gap between the icons', 'bm-custom-login' ) }
							/**
							 * Update the value
							 *
							 * @param {string} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								setSetting( {
									gap: updatedValue,
								} );
							} }
							value={ gap }
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
							 * Update the values
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
								top: '16px',
								right: '0px',
								bottom: '16px',
								left: '0px',
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
PanelSocialMediaLinks.propTypes = {
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
