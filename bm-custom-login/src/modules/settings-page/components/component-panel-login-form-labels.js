/**
 * External dependencies
 */
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { FontControl } from '@teydeastudio/components/src/font-control/index.js';
import { MultilingualTextControl } from '@teydeastudio/components/src/multilingual-text-control/index.js';
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
import { Fragment } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * PanelLoginFormLabels component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.context     Additional context object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelLoginFormLabels component.
 */
export const PanelLoginFormLabels = ( { context, presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { loginFormLabels } = settings.data;
	const {
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
		show,
		textColor,
	} = loginFormLabels;

	// Destructure the presets object.
	const { colorPalettes, fontFamilies, fontSizes, fontWeights, gradientPalettes } = presets;

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
				loginFormLabels: {
					...loginFormLabels,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelLoginFormLabels component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Login form labels', 'bm-custom-login' ) }>
			<FieldsGroup>
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Show labels above the login form fields', 'bm-custom-login' ) }
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
								fallbackSize: '14px',
								fontFamilies,
								fontSizes,
								fontWeights,
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
								top: '0px',
								right: '0px',
								bottom: '3px',
								left: '0px',
							} }
						/>
						<hr />
						<MultilingualTextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( '"Username or Email Address" field\'s label', 'bm-custom-login' ) }
							original={ translations?.[ 'Username or Email Address' ] }
							values={ Object.fromEntries(
								languages.map( ( language ) => [ language, loginFormLabels?.[ sprintf( 'labelUsernameOrEmailAddress.%s', language ) ] ?? '' ] )
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
										languages.map( ( language ) => [ sprintf( 'labelUsernameOrEmailAddress.%s', language ), updatedValues[ language ] ] )
									)
								);
							} }
						/>
						<hr />
						<MultilingualTextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( '"Password" field\'s label', 'bm-custom-login' ) }
							original={ translations?.Password }
							values={ Object.fromEntries(
								languages.map( ( language ) => [ language, loginFormLabels?.[ sprintf( 'labelPassword.%s', language ) ] ?? '' ] )
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
										languages.map( ( language ) => [ sprintf( 'labelPassword.%s', language ), updatedValues[ language ] ] )
									)
								);
							} }
						/>
						{ anyoneCanRegister && (
							<Fragment>
								<hr />
								<MultilingualTextControl
									__nextHasNoMarginBottom
									__next40pxDefaultSize
									label={ __( '"Username" field\'s label', 'bm-custom-login' ) }
									original={ translations?.Username }
									values={ Object.fromEntries(
										languages.map( ( language ) => [ language, loginFormLabels?.[ sprintf( 'labelUsername.%s', language ) ] ?? '' ] )
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
												languages.map( ( language ) => [ sprintf( 'labelUsername.%s', language ), updatedValues[ language ] ] )
											)
										);
									} }
								/>
								<hr />
								<MultilingualTextControl
									__nextHasNoMarginBottom
									__next40pxDefaultSize
									label={ __( '"Email" field\'s label', 'bm-custom-login' ) }
									original={ translations?.Email }
									values={ Object.fromEntries(
										languages.map( ( language ) => [ language, loginFormLabels?.[ sprintf( 'labelEmail.%s', language ) ] ?? '' ] )
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
												languages.map( ( language ) => [ sprintf( 'labelEmail.%s', language ), updatedValues[ language ] ] )
											)
										);
									} }
								/>
							</Fragment>
						) }
					</Fragment>
				) }
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelLoginFormLabels.propTypes = {
	context: PropTypes.object.isRequired,
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
