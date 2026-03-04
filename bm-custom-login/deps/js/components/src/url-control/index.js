/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { isURL } from '@teydeastudio/utils/src/is-url.js';

/**
 * WordPress dependencies
 */
import { TextControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { isEmail } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { DetectOutside } from '../detect-outside/index.js';
import { FieldNotice } from '../field-notice/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * URLControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {string}   properties.label    Field's label.
 * @param {string}   properties.help     Field's help.
 * @param {string}   properties.value    Field's value.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 *
 * @return {JSX} URLControl component.
 */
export const URLControl = ( { label, help, value, onChange } ) => {
	// Manage the notice state.
	const [ fieldNotice, setFieldNotice ] = useState( '' );

	/**
	 * Return component
	 *
	 * @return {JSX} URLControl component.
	 */
	return (
		<div className="tsc-url-control">
			<DetectOutside
				/**
				 * Validate the field's value
				 *
				 * @return {void}
				 */
				onFocusOutside={ () => {
					if ( '' === value || isEmail( value ) || isURL( value ) ) {
						setFieldNotice( '' );
					} else {
						onChange( '' );

						setFieldNotice(
							sprintf(
								// Translators: %s - field value.
								__(
									'"%s" is not a valid URL; field value has been emptied. Please put the valid URL or leave the field empty.',
									'bm-custom-login'
								),
								value
							)
						);
					}
				} }
			>
				<TextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ label }
					help={ help }
					value={ value }
					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( updatedValue );
					} }
				/>
			</DetectOutside>
			{ '' !== fieldNotice && <FieldNotice message={ fieldNotice } /> }
		</div>
	);
};

/**
 * Props validation
 */
URLControl.propTypes = {
	label: PropTypes.string.isRequired,
	help: PropTypes.string,
	value: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
};
