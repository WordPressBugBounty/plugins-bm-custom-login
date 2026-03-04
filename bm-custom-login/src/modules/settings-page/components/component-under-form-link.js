/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { URLControl } from '@teydeastudio/components/src/url-control/index.js';

/**
 * WordPress dependencies
 */
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * UnderFormLink component
 *
 * @param {Object}   properties          Component properties object.
 * @param {Object}   properties.data     Component data object.
 * @param {Function} properties.onChange Function (callback) used to update the data.
 *
 * @return {JSX} UnderFormLink component.
 */
export const UnderFormLink = ( { data, onChange } ) => {
	// Destructure the data object.
	const { link, text, openInNewTab } = data;

	/**
	 * Render the component
	 */
	return (
		<PanelBody
			initialOpen={ false }
			title={
				'' === text.trim()
					? __( 'Link under form', 'bm-custom-login' )
					: sprintf(
							// Translators: %s - link text (anchor).
							__( '"%s" link under form', 'bm-custom-login' ),
							text
					  )
			}
		>
			<FieldsGroup>
				<TextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Link text (anchor)', 'bm-custom-login' ) }
					value={ text }
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
							text: updatedValue,
						} );
					} }
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
UnderFormLink.propTypes = {
	data: PropTypes.shape( {
		text: PropTypes.string.isRequired,
		link: PropTypes.string.isRequired,
		openInNewTab: PropTypes.bool.isRequired,
	} ).isRequired,
	onChange: PropTypes.func.isRequired,
};
