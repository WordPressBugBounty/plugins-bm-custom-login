/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { PanelBody, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * PanelCustomCSS component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelCustomCSS component.
 */
export const PanelCustomCSS = ( { settings, setSettings } ) => {
	// Destructure the settings object.
	const { customCss } = settings.data;
	const { css } = customCss;

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
				customCss: {
					...customCss,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelCustomCSS component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Custom CSS', 'bm-custom-login' ) }>
			<TextareaControl
				__nextHasNoMarginBottom
				__next40pxDefaultSize
				label={ __( 'Additional, custom CSS', 'bm-custom-login' ) }
				value={ css }
				/**
				 * Update the value
				 *
				 * @param {string} updatedValue Updated value.
				 *
				 * @return {void}
				 */
				onChange={ ( updatedValue ) => {
					setSetting( {
						css: updatedValue,
					} );
				} }
			/>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelCustomCSS.propTypes = {
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
