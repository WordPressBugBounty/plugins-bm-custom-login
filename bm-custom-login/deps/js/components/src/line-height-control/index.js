/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { LineHeightControl as CoreLineHeightControl } from '@wordpress/block-editor';

/**
 * LineHeightControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 * @param {number}   properties.value    Field's value.
 *
 * @return {JSX} LineHeightControl component.
 */
export const LineHeightControl = ( { onChange, value } ) => (
	<CoreLineHeightControl
		__next40pxDefaultSize
		__unstableInputWidth="100%"
		value={ value }
		/**
		 * Update the value
		 *
		 * @param {string} updatedValue Updated value.
		 *
		 * @return {void}
		 */
		onChange={ ( updatedValue ) => {
			// Fallback value.
			if ( 'undefined' === typeof updatedValue || '' === updatedValue ) {
				updatedValue = 1;
			}

			onChange( Number.parseFloat( updatedValue ) );
		} }
	/>
);

/**
 * Props validation
 */
LineHeightControl.propTypes = {
	onChange: PropTypes.func.isRequired,
	value: PropTypes.number.isRequired,
};
