/**
 * External dependencies
 */
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { MultilingualTextControl } from '@teydeastudio/components/src/multilingual-text-control/index.js';
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
 * PanelLoginFormRememberMeCheckbox component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.context     Additional context object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {Element} PanelLoginFormRememberMeCheckbox component.
 */
export const PanelLoginFormRememberMeCheckbox = ( { context, presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { loginFormRememberMeCheckbox } = settings.data;
	const {
		helpBubbleBackgroundColor,
		helpBubbleBorderColor,
		helpBubbleTextColor,
		helpIconColor,
		helpIconColorOnHover,
		marginBottom,
		marginLeft,
		marginRight,
		marginTop,
		showHelp,
		visibility,
	} = loginFormRememberMeCheckbox;

	// Destructure the context object.
	const { languages, translations } = context;

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
				loginFormRememberMeCheckbox: {
					...loginFormRememberMeCheckbox,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {Element} PanelLoginFormRememberMeCheckbox component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Login form\'s "Remember Me" checkbox', 'bm-custom-login' ) }>
			<FieldsGroup>
				<SelectControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( '"Remember Me" checkbox visibility', 'bm-custom-login' ) }
					value={ visibility }
					options={ [
						{
							value: 'visible',
							label: __( 'Visible (default)', 'bm-custom-login' ),
						},
						{
							value: 'hidden-checked',
							label: __( 'Hidden, checked', 'bm-custom-login' ),
						},
						{
							value: 'hidden-unchecked',
							label: __( 'Hidden, unchecked', 'bm-custom-login' ),
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
							visibility: updatedValue,
						} );
					} }
				/>
				{ 'visible' === visibility && (
					<Fragment>
						<hr />
						<MultilingualTextControl
							label={ __( "Field's label", 'bm-custom-login' ) }
							original={ translations?.[ 'Remember Me' ] }
							values={ Object.fromEntries(
								languages.map( ( language ) => [ language, loginFormRememberMeCheckbox?.[ sprintf( 'labelRememberMe.%s', language ) ] ?? '' ] ),
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
										languages.map( ( language ) => [ sprintf( 'labelRememberMe.%s', language ), updatedValues[ language ] ] ),
									),
								);
							} }
						/>
						<hr />
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Show the help tooltip next to the checkbox', 'bm-custom-login' ) }
							help={ __(
								'WordPress 7.1 added a tooltip explaining what "Remember Me" does. Turning this off removes it from the login form entirely; earlier WordPress versions never render it.',
								'bm-custom-login',
							) }
							checked={ showHelp }

							/**
							 * Update the value
							 *
							 * @return {void}
							 */
							onChange={ () => {
								setSetting( {
									showHelp: ! showHelp,
								} );
							} }
						/>
						{ showHelp && (
							<Fragment>
								<hr />
								<MultilingualTextControl
									label={ __( 'Help tooltip content', 'bm-custom-login' ) }
									original={
										translations?.[
											'Selecting "Remember Me" increases the length of time until you&#8217;re asked to log in again on this device. To keep your account secure, use this option only on your personal devices.'
										]
									}
									values={ Object.fromEntries(
										languages.map( ( language ) => [
											language,
											loginFormRememberMeCheckbox?.[ sprintf( 'helpText.%s', language ) ] ?? '',
										] ),
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
												languages.map( ( language ) => [ sprintf( 'helpText.%s', language ), updatedValues[ language ] ] ),
											),
										);
									} }
								/>
								<hr />
								<FieldsGroup label={ __( 'Help tooltip colors', 'bm-custom-login' ) } withBoxBorder withBaseControl>
									<ColorControl
										value={ helpIconColor }
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
												helpIconColor: updatedValue,
											} );
										} }
										gradientPalettes={ gradientPalettes }
										colorPalettes={ colorPalettes }
										withAlpha
										withColor
										withGradient={ false }
									/>
									<ColorControl
										value={ helpIconColorOnHover }
										label={ __( 'Icon (on hover and focus)', 'bm-custom-login' ) }

										/**
										 * Update the value
										 *
										 * @param {string} updatedValue Updated value.
										 *
										 * @return {void}
										 */
										onChange={ ( updatedValue ) => {
											setSetting( {
												helpIconColorOnHover: updatedValue,
											} );
										} }
										gradientPalettes={ gradientPalettes }
										colorPalettes={ colorPalettes }
										withAlpha
										withColor
										withGradient={ false }
									/>
									<ColorControl
										value={ helpBubbleBackgroundColor }
										label={ __( 'Tooltip background', 'bm-custom-login' ) }

										/**
										 * Update the value
										 *
										 * @param {string} updatedValue Updated value.
										 *
										 * @return {void}
										 */
										onChange={ ( updatedValue ) => {
											setSetting( {
												helpBubbleBackgroundColor: updatedValue,
											} );
										} }
										gradientPalettes={ gradientPalettes }
										colorPalettes={ colorPalettes }
										withAlpha
										withColor
										withGradient={ false }
									/>
									<ColorControl
										value={ helpBubbleBorderColor }
										label={ __( 'Tooltip border', 'bm-custom-login' ) }

										/**
										 * Update the value
										 *
										 * @param {string} updatedValue Updated value.
										 *
										 * @return {void}
										 */
										onChange={ ( updatedValue ) => {
											setSetting( {
												helpBubbleBorderColor: updatedValue,
											} );
										} }
										gradientPalettes={ gradientPalettes }
										colorPalettes={ colorPalettes }
										withAlpha
										withColor
										withGradient={ false }
									/>
									<ColorControl
										value={ helpBubbleTextColor }
										label={ __( 'Tooltip text', 'bm-custom-login' ) }

										/**
										 * Update the value
										 *
										 * @param {string} updatedValue Updated value.
										 *
										 * @return {void}
										 */
										onChange={ ( updatedValue ) => {
											setSetting( {
												helpBubbleTextColor: updatedValue,
											} );
										} }
										gradientPalettes={ gradientPalettes }
										colorPalettes={ colorPalettes }
										withAlpha
										withColor
										withGradient={ false }
									/>
								</FieldsGroup>
							</Fragment>
						) }
						<hr />
						<BoxControl
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
								bottom: '0px',
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
PanelLoginFormRememberMeCheckbox.propTypes = {
	context: PropTypes.object.isRequired,
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
