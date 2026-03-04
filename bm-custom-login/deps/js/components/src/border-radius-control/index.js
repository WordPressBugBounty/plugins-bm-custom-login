/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { __experimentalBorderRadiusControl as CoreBorderRadiusControl } from '@wordpress/block-editor'; // eslint-disable-line @wordpress/no-unsafe-wp-apis

/**
 * Import styles
 */
import './styles.scss';

/**
 * BorderRadiusControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 * @param {Object}   properties.value    Field's value.
 *
 * @return {JSX} BorderRadiusControl component.
 */
export const BorderRadiusControl = ( { onChange, value } ) => (
	<div className="tsc-border-radius-control">
		<CoreBorderRadiusControl
			values={ value }
			/**
			 * Update the value
			 *
			 * @param {string|Object} updatedValue Updated value.
			 *
			 * @return {void}
			 */
			onChange={ ( updatedValue ) => {
				if ( 'undefined' === typeof updatedValue ) {
					updatedValue = '';
				}

				if ( 'string' === typeof updatedValue ) {
					if ( '' === updatedValue ) {
						updatedValue = {
							bottomLeft: '0px',
							bottomRight: '0px',
							topLeft: '0px',
							topRight: '0px',
						};
					} else {
						updatedValue = {
							bottomLeft: updatedValue,
							bottomRight: updatedValue,
							topLeft: updatedValue,
							topRight: updatedValue,
						};
					}
				}

				onChange( updatedValue );
			} }
		/>
	</div>
);

/**
 * Props validation
 */
BorderRadiusControl.propTypes = {
	onChange: PropTypes.func.isRequired,
	value: PropTypes.object.isRequired,
};
