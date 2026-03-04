/**
 * External dependencies
 */
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
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * PanelLoginFormRememberMeCheckbox component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.context     Additional context object.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelLoginFormRememberMeCheckbox component.
 */
export const PanelLoginFormRememberMeCheckbox = ( { context, settings, setSettings } ) => {
	// Destructure the settings object.
	const { loginFormRememberMeCheckbox } = settings.data;
	const { marginBottom, marginLeft, marginRight, marginTop, visibility } = loginFormRememberMeCheckbox;

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
	 * @return {JSX} PanelLoginFormRememberMeCheckbox component.
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
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( "Field's label", 'bm-custom-login' ) }
							original={ translations?.[ 'Remember Me' ] }
							values={ Object.fromEntries(
								languages.map( ( language ) => [ language, loginFormRememberMeCheckbox?.[ sprintf( 'labelRememberMe.%s', language ) ] ?? '' ] )
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
										languages.map( ( language ) => [ sprintf( 'labelRememberMe.%s', language ), updatedValues[ language ] ] )
									)
								);
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
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
