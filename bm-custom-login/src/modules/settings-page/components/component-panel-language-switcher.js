/**
 * External dependencies
 */
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalBoxControl as BoxControl,
	Notice,
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * PanelLanguageSwitcher component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelLanguageSwitcher component.
 */
export const PanelLanguageSwitcher = ( { presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { languageSwitcher } = settings.data;
	const { iconColor, marginBottom, marginLeft, marginRight, marginTop, paddingBottom, paddingLeft, paddingRight, paddingTop, show } = languageSwitcher;

	// Destructure the presets object.
	const { colorPalettes, gradientPalettes } = presets;

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
				languageSwitcher: {
					...languageSwitcher,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelLanguageSwitcher component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Language switcher', 'bm-custom-login' ) }>
			<FieldsGroup>
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Allow the language switcher/dropdown', 'bm-custom-login' ) }
					help={ __(
						'If your website supports multiple languages, users can switch to their preferred language using the dropdown below the login form; that functionality can be disabled here.',
						'bm-custom-login'
					) }
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
						<Notice __nextHasNoMarginBottom __next40pxDefaultSize isDismissible={ false } status="info">
							{ __(
								'Note: the select field\'s (language dropdown) styles inherit styles defined in the "Login form input fields" panel above.',
								'bm-custom-login'
							) }
						</Notice>
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
								gradientPalettes={ gradientPalettes }
								colorPalettes={ colorPalettes }
								withAlpha
								withColor
								withGradient
							/>
						</FieldsGroup>
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
								bottom: '24px',
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
					</Fragment>
				) }
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelLanguageSwitcher.propTypes = {
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
