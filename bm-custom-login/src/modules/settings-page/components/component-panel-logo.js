/**
 * External dependencies
 */
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { IntegerControl } from '@teydeastudio/components/src/integer-control/index.js';
import { MediaControl } from '@teydeastudio/components/src/media-control/index.js';
import { MultilingualTextControl } from '@teydeastudio/components/src/multilingual-text-control/index.js';
import { URLControl } from '@teydeastudio/components/src/url-control/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * PanelLogo component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.context     Additional context object.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelLogo component.
 */
export const PanelLogo = ( { context, settings, setSettings } ) => {
	// Destructure the settings object.
	const { logo } = settings.data;
	const { alignment, asLink, link, mediaId, openInNewTab, show, strictWidth, logoSource } = logo;

	// Destructure the context object.
	const { isNetworkAdmin, languages, translations } = context;

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
				logo: {
					...logo,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelLogo component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Logo', 'bm-custom-login' ) }>
			<FieldsGroup>
				<ToggleControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ __( 'Show logo above the login form', 'bm-custom-login' ) }
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
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Logo image alignment', 'bm-custom-login' ) }
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
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Logo source', 'bm-custom-login' ) }
							value={ logoSource }
							options={ [
								{
									value: 'core',
									label: __( 'Core (default WordPress.org logo)', 'bm-custom-login' ),
								},
								{
									value: 'custom',
									label: __( 'Custom', 'bm-custom-login' ),
								},
								{
									value: 'site_icon',
									label: __( 'Site icon', 'bm-custom-login' ),
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
									logoSource: updatedValue,
								} );
							} }
						/>
					</Fragment>
				) }
				{ show && 'core' !== logoSource && (
					<FieldsGroup>
						{ 'custom' === logoSource && (
							<MediaControl
								allowedTypes={ [ 'image' ] }
								isNetworkAdmin={ isNetworkAdmin }
								label={ __( 'logo image', 'bm-custom-login' ) } // Lower case intentional.
								values={ {
									mediaId,
								} }
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
							/>
						) }
						<IntegerControl
							label={ __( 'Strict width of the logo image (px)', 'bm-custom-login' ) }
							help={ __( 'Set to "0" to use the original image width.', 'bm-custom-login' ) }
							min={ 0 }
							max={ 1000 }
							value={ strictWidth }
							defaultValue={ 0 }
							/**
							 * Update the value
							 *
							 * @param {number} updatedValue Updated value.
							 *
							 * @return {void}
							 */
							onChange={ ( updatedValue ) => {
								setSetting( {
									strictWidth: updatedValue,
								} );
							} }
						/>
					</FieldsGroup>
				) }
				{ show && (
					<Fragment>
						<hr />
						<ToggleControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Make logo a link', 'bm-custom-login' ) }
							checked={ asLink }
							/**
							 * Update the value
							 *
							 * @return {void}
							 */
							onChange={ () => {
								setSetting( {
									asLink: ! asLink,
								} );
							} }
						/>
						{ asLink && (
							<FieldsGroup>
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
										setSetting( {
											openInNewTab: ! openInNewTab,
										} );
									} }
								/>
								<URLControl
									label={ __( 'Logo link', 'bm-custom-login' ) }
									help={ __( 'Leave empty to remove the link entirely and only render the image.', 'bm-custom-login' ) }
									value={ link }
									/**
									 * Update the value
									 *
									 * @param {string} updatedValue Updated value.
									 *
									 * @return {void}
									 */
									onChange={ ( updatedValue ) => {
										setSetting( {
											link: updatedValue,
										} );
									} }
								/>
								<MultilingualTextControl
									__nextHasNoMarginBottom
									__next40pxDefaultSize
									label={ __( 'Logo link title', 'bm-custom-login' ) }
									original={ translations?.[ 'Powered by WordPress' ] }
									values={ Object.fromEntries(
										languages.map( ( language ) => [ language, logo?.[ sprintf( 'linkTitle.%s', language ) ] ?? '' ] )
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
												languages.map( ( language ) => [ sprintf( 'linkTitle.%s', language ), updatedValues[ language ] ] )
											)
										);
									} }
								/>
							</FieldsGroup>
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
PanelLogo.propTypes = {
	context: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
