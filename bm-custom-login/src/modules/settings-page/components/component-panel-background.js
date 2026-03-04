/**
 * External dependencies
 */
import { ColorControl } from '@teydeastudio/components/src/color-control/index.js';
import { FieldsGroup } from '@teydeastudio/components/src/fields-group/index.js';
import { MediaControl } from '@teydeastudio/components/src/media-control/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * PanelBackground component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.context     Additional context object.
 * @param {Object}   properties.presets     Presets to use in components.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} PanelBackground component.
 */
export const PanelBackground = ( { context, presets, settings, setSettings } ) => {
	// Destructure the settings object.
	const { background } = settings.data;
	const { color, focalPointX, focalPointY, mediaId, sizeRepeat } = background;

	// Destructure the context object.
	const { isNetworkAdmin } = context;

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
				background: {
					...background,
					...setting,
				},
			},
		} );
	};

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelBackground component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Background', 'bm-custom-login' ) }>
			<FieldsGroup>
				<FieldsGroup label={ __( 'Colors', 'bm-custom-login' ) } withBoxBorder withBaseControl>
					<ColorControl
						value={ color }
						label={ __( 'Background', 'bm-custom-login' ) }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							setSetting( {
								color: updatedValue,
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
				<MediaControl
					allowedTypes={ [ 'image' ] }
					isNetworkAdmin={ isNetworkAdmin }
					label={ __( 'background image', 'bm-custom-login' ) } // Lower case intentional.
					values={ {
						focalPointX,
						focalPointY,
						mediaId,
						sizeRepeat,
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
					withFocalPointPicker
					withSizeRepeatSelector
				/>
			</FieldsGroup>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelBackground.propTypes = {
	context: PropTypes.object.isRequired,
	presets: PropTypes.object.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
