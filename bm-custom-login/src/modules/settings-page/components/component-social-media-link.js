/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { IconControl } from '@teydeastudio/components/src/icon-control/index.js';
import { URLControl } from '@teydeastudio/components/src/url-control/index.js';

/**
 * WordPress dependencies
 */
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * SocialMediaLink component
 *
 * @param {Object}   properties          Component properties object.
 * @param {Object}   properties.data     Component data object.
 * @param {Function} properties.onChange Function (callback) used to update the data.
 *
 * @return {JSX} SocialMediaLink component.
 */
export const SocialMediaLink = ( { data, onChange } ) => {
	// Destructure the data object.
	const { icon, link, ariaLabel, openInNewTab } = data;

	/**
	 * Render the component
	 */
	return (
		<PanelBody
			initialOpen={ false }
			title={
				'' === ariaLabel.trim()
					? __( 'Social media link', 'bm-custom-login' )
					: sprintf(
							// Translators: %s - link's ARIA label.
							__( '"%s" social media link', 'bm-custom-login' ),
							ariaLabel
					  )
			}
		>
			<FieldsGroup>
				<IconControl
					label={ __( 'Icon', 'bm-custom-login' ) }
					value={ icon }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( {
							...data,
							icon: updatedValue,
						} );
					} }
					preset="socialMedia"
				/>
				<URLControl
					label={ __( 'Link URL', 'bm-custom-login' ) }
					value={ link }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( {
							...data,
							link: updatedValue,
						} );
					} }
				/>
				<TextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Link\'s "aria-label"', 'bm-custom-login' ) }
					value={ ariaLabel }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( {
							...data,
							ariaLabel: updatedValue,
						} );
					} }
				/>
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Open link in new tab', 'bm-custom-login' ) }
					checked={ openInNewTab }
					/**
					 * Update the value
					 *
					 * @return {void}
					 */
					onChange={ () => {
						onChange( {
							...data,
							openInNewTab: ! openInNewTab,
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
SocialMediaLink.propTypes = {
	data: PropTypes.shape( {
		icon: PropTypes.string.isRequired,
		ariaLabel: PropTypes.string.isRequired,
		link: PropTypes.string.isRequired,
		openInNewTab: PropTypes.bool.isRequired,
	} ).isRequired,
	onChange: PropTypes.func.isRequired,
};
