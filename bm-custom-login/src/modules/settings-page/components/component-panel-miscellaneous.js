/**
 * External dependencies
 */
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { CheckboxControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * PanelMiscellaneous component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelMiscellaneous component.
 */
export const PanelMiscellaneous = ( { settings, setSettings } ) => {
	// Destructure the settings object.
	const { miscellaneous } = settings.data;
	const { disableAutocomplete, disableAutofocus, disableShakeEffect } = miscellaneous;

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
				miscellaneous: {
					...miscellaneous,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelMiscellaneous component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Miscellaneous', 'bm-custom-login' ) }>
			<FieldsGroup>
				<CheckboxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Disable autocomplete on the login form fields', 'bm-custom-login' ) }
					help={ __(
						'Ask the browser not to remember the values for the login form fields. This is a hint to browsers; some may not comply with this choice.',
						'bm-custom-login'
					) }
					checked={ disableAutocomplete }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							disableAutocomplete: ! disableAutocomplete,
						} );
					} }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Disable autofocus on the "username" login form field', 'bm-custom-login' ) }
					help={ __(
						'When entering the login screen, the "username" field of the login form field is automatically focused; that functionality can be disabled here.',
						'bm-custom-login'
					) }
					checked={ disableAutofocus }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							disableAutofocus: ! disableAutofocus,
						} );
					} }
				/>
				<CheckboxControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Disable shake effect on login form fields', 'bm-custom-login' ) }
					help={ __( 'The "shake" effect comes from a WordPress core and is used if a value of a field is incorrect.', 'bm-custom-login' ) }
					checked={ disableShakeEffect }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						setSetting( {
							disableShakeEffect: ! disableShakeEffect,
						} );
					} }
				/>
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelMiscellaneous.propTypes = {
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
