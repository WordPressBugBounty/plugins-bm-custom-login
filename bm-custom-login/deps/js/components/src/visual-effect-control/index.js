/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FieldsGroup } from '../fields-group/index.js';

/**
 * Controls configuration
 */
const controls = [
	{
		defaultValue: 0,
		key: 'filterBlur',
		label: __( 'Blur (px)', 'bm-custom-login' ),
		help: __( 'Applies a blur effect to the image/video. A larger value will create more blur. Defaults to "0".', 'bm-custom-login' ),
		min: 0,
		max: 500,
	},
	{
		defaultValue: 100,
		key: 'filterBrightness',
		label: __( 'Brightness (%)', 'bm-custom-login' ),
		help: __(
			'Adjusts the brightness of the image/video. 0% will make the image completely black. 100% is default and represents the original image. Values over 100% will provide brighter results. Values under 100% will provide darker results.',
			'bm-custom-login'
		),
		min: 0,
		max: 500,
	},
	{
		defaultValue: 100,
		key: 'filterContrast',
		label: __( 'Contrast (%)', 'bm-custom-login' ),

		// eslint-disable-next-line @wordpress/i18n-translator-comments
		help: __(
			'Adjusts the contrast of the image/video. 0% will make the image completely gray. 100% is default, and represents the original image. Values over 100% increases the contrast. Values under 100% decreases the contrast.',
			'bm-custom-login'
		),
		min: 0,
		max: 500,
	},
	{
		defaultValue: 0,
		key: 'filterGrayscale',
		label: __( 'Grayscale (%)', 'bm-custom-login' ),
		help: __(
			'Converts the image to grayscale. 0% is default and represents the original image. 100% will make the image completely grayscale.',
			'bm-custom-login'
		),
		min: 0,
		max: 100,
	},
	{
		defaultValue: 0,
		key: 'filterHueRotation',
		label: __( 'Hue rotation (deg)', 'bm-custom-login' ),
		help: __(
			'Applies a hue rotation on the image. The value defines the number of degrees around the color circle the image samples will be adjusted. 0deg is default, and represents the original image. Maximum value is 360deg.',
			'bm-custom-login'
		),
		min: 0,
		max: 360,
	},
	{
		defaultValue: 0,
		key: 'filterInvert',
		label: __( 'Invert (%)', 'bm-custom-login' ),
		help: __(
			'Inverts the samples in the image. 0% is default and represents the original image. 100% will make the image completely inverted.',
			'bm-custom-login'
		),
		min: 0,
		max: 100,
	},
	{
		defaultValue: 100,
		key: 'filterOpacity',
		label: __( 'Opacity (%)', 'bm-custom-login' ),
		help: __(
			'Sets the opacity level for the image. The opacity-level describes the transparency-level, where 0% is completely transparent. 100% is default and represents the original image (no transparency).',
			'bm-custom-login'
		),
		min: 0,
		max: 100,
	},
	{
		defaultValue: 100,
		key: 'filterSaturate',
		label: __( 'Saturate (%)', 'bm-custom-login' ),

		// eslint-disable-next-line @wordpress/i18n-translator-comments
		help: __(
			'Saturates the image. 0% will make the image completely un-saturated. 100% is default and represents the original image. Values over 100% provides super-saturated results.',
			'bm-custom-login'
		),
		min: 0,
		max: 500,
	},
	{
		defaultValue: 0,
		key: 'filterSepia',
		label: __( 'Sepia (%)', 'bm-custom-login' ),
		help: __( 'Converts the image to sepia. 0% is default and represents the original image. 100% will make the image completely sepia.', 'bm-custom-login' ),
		min: 0,
		max: 100,
	},
];

/**
 * VisualEffectControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 * @param {Object}   properties.values   Field's values object.
 *
 * @return {JSX} VisualEffectControl component.
 */
export const VisualEffectControl = ( { onChange, values } ) => (
	<FieldsGroup>
		{ controls.map( ( { defaultValue, key, label, help, min, max } ) => (
			<Fragment key={ key }>
				<hr />
				<RangeControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					allowReset={ true }
					label={ label }
					help={ help }
					value={ values[ key ] }
					/**
					 * Update the value
					 *
					 * @param {number} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( {
							...values,
							[ key ]: updatedValue,
						} );
					} }
					min={ min }
					max={ max }
					resetFallbackValue={ defaultValue }
					withInputField
				/>
			</Fragment>
		) ) }
	</FieldsGroup>
);

/**
 * Props validation
 */
VisualEffectControl.propTypes = {
	onChange: PropTypes.func.isRequired,
	values: PropTypes.shape( {
		filterBlur: PropTypes.number.isRequired,
		filterBrightness: PropTypes.number.isRequired,
		filterContrast: PropTypes.number.isRequired,
		filterGrayscale: PropTypes.number.isRequired,
		filterHueRotation: PropTypes.number.isRequired,
		filterInvert: PropTypes.number.isRequired,
		filterOpacity: PropTypes.number.isRequired,
		filterSaturate: PropTypes.number.isRequired,
		filterSepia: PropTypes.number.isRequired,
	} ).isRequired,
};
