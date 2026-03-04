/**
 * External dependencies
 */
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { FontControl } from '@teydeastudio/components/src/font-control/index.js';
import { SortableContainer } from '@teydeastudio/components/src/sortable-container/index.js';
import { ShadowControl } from '@teydeastudio/components/src/shadow-control/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalBoxControl as BoxControl,
	CheckboxControl,
	PanelBody,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { UnderFormLink } from './component-under-form-link.js';

/**
 * PanelUnderFormLinks component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelUnderFormLinks component.
 */
export const PanelUnderFormLinks = ( { presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { underFormLinks, underFormLinksList } = settings.data;
	const {
		alignment,
		disableBackLink,
		fontFamily,
		fontSize,
		fontWeight,
		letterCase,
		lineHeight,
		linkColor,
		linkColorOnFocus,
		linkColorOnHover,
		marginBottom,
		marginLeft,
		marginRight,
		marginTop,
		paddingBottom,
		paddingLeft,
		paddingRight,
		paddingTop,
		separator,
		separatorColor,
		shadow,
		shadowOnFocus,
		shadowOnHover,
		textDecoration,
	} = underFormLinks;

	// Destructure the presets object.
	const { colorPalettes, fontFamilies, fontSizes, fontWeights, gradientPalettes, shadowPresets } = presets;

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
				underFormLinks: {
					...underFormLinks,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelUnderFormLinks component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Under form links', 'bm-custom-login' ) }>
			<FieldsGroup>
				<SortableContainer
					addLabel={ __( 'Add new link', 'bm-custom-login' ) }
					ItemComponent={ UnderFormLink }
					items={ underFormLinksList }
					label={ __( 'Additional links under the form', 'bm-custom-login' ) }
					/**
					 * Handle adding new item
					 *
					 * @return {void}
					 */
					onAdd={ () => {
						const itemKey = `d:${ Date.now().toString() }0000`;
						const { underFormLink: itemTemplate } = settings.templates.underFormLinksList;

						setSettings( {
							...settings,
							data: {
								...settings.data,
								underFormLinksList: {
									...underFormLinksList,
									[ itemKey ]: Object.assign( {}, { key: itemKey, ...itemTemplate } ),
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
								underFormLinksList: updatedValues,
							},
						} );
					} }
					placeholder={ <p>{ __( 'No additional links under the form set yet.', 'bm-custom-login' ) }</p> }
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
				<TextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Separator', 'bm-custom-login' ) }
					value={ separator }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setSetting( {
							separator: updatedValue,
						} );
					} }
				/>
				<hr />
				<CheckboxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Disable the "← Go to…" back link', 'bm-custom-login' ) }
					help={ __( 'This link redirects to the home page.', 'bm-custom-login' ) }
					checked={ disableBackLink }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							disableBackLink: ! disableBackLink,
						} );
					} }
				/>
				<hr />
				<FieldsGroup label={ __( 'Colors', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ linkColor }
						label={ __( 'Link', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								linkColor: updatedValue,
							} );
						} }
						gradientPalettes={ gradientPalettes }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
						withGradient
					/>
					<ColorControl
						value={ separatorColor }
						label={ __( 'Separator', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								separatorColor: updatedValue,
							} );
						} }
						colorPalettes={ colorPalettes }
						withAlpha
						withColor
					/>
				</FieldsGroup>
				<FieldsGroup label={ __( 'Colors (on hover)', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ linkColorOnHover }
						label={ __( 'Link', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								linkColorOnHover: updatedValue,
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
						value={ linkColorOnFocus }
						label={ __( 'Link', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								linkColorOnFocus: updatedValue,
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
						right: '24px',
						bottom: '0px',
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
						top: '16px',
						right: '0px',
						bottom: '16px',
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
PanelUnderFormLinks.propTypes = {
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
