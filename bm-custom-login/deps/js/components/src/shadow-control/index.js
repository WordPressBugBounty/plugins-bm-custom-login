/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { Button, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { cleanForSlug } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { FieldsGroup } from '../fields-group/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * ShadowControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {string}   properties.label    Label.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 * @param {Array}    properties.presets  Predefined shadow presets.
 * @param {string}   properties.value    Field's value.
 *
 * @return {JSX} ShadowControl component.
 */
export const ShadowControl = ( { label, onChange, presets, value } ) => (
	<FieldsGroup className="tsc-shadow-control">
		<TextControl
			__nextHasNoMarginBottom
			__next40pxDefaultSize
			label={ label }
			help={ __( 'A CSS value for the box-shadow property.', 'bm-custom-login' ) }
			value={ value }
			onChange={ onChange }
		/>
		<div className="tsc-shadow-control__presets">
			{ presets.map( ( preset, index ) => (
				<Button
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					key={ `${ cleanForSlug( preset.label ) }-${ index }` }
					onClick={ () => {
						onChange( preset.value );
					} }
					isPressed={ value === preset.value }
					size="compact"
					variant="tertiary"
				>
					{ preset.label }
				</Button>
			) ) }
		</div>
	</FieldsGroup>
);

/**
 * Props validation
 */
ShadowControl.propTypes = {
	label: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
	presets: PropTypes.array.isRequired,
	value: PropTypes.string.isRequired,
};
