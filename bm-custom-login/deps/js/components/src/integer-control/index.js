/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { getIntegerWithinRange } from '@teydeastudio/utils/src/get-integer-within-range.js';

/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose';
import { TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { DetectOutside } from '../detect-outside/index.js';
import { FieldNotice } from '../field-notice/index.js';

/**
 * IntegerControl component
 *
 * @param {Object}   properties              Component properties object.
 * @param {string}   properties.label        Field's label.
 * @param {string}   properties.help         Field's help.
 * @param {number}   properties.min          Field's minimum accepted value.
 * @param {number}   properties.max          Field's maximum accepted value.
 * @param {number}   properties.value        Field's value.
 * @param {number}   properties.defaultValue Field's default value.
 * @param {boolean}  properties.disabled     Whether the field is disabled.
 * @param {Function} properties.onChange     Function callback to trigger on value change.
 *
 * @return {Element} IntegerControl component.
 */
export const IntegerControl = ( { label, help, min, max, value, defaultValue, disabled = false, onChange } ) => {
	// Manage the notice state.
	const [ fieldNotice, setFieldNotice ] = useState( '' );

	// Manage edited value.
	const [ editedValue, setEditedValue ] = useState( value.toString() );

	// Stable id so the validation notice can be wired to the input via `aria-describedby`.
	const noticeId = useInstanceId( IntegerControl, 'tsc-integer-control-notice' );

	/**
	 * Ensure data consistency
	 */
	if ( 'undefined' === typeof max || min > max ) {
		max = null;
	}

	/**
	 * Update field's "edited value" any time the given value changes
	 */
	useEffect( () => {
		setEditedValue( value.toString() );
	}, [ value ] );

	/**
	 * Update the field notice
	 */
	useEffect( () => {
		if ( editedValue === getIntegerWithinRange( editedValue, min, max ).toString() ) {
			setFieldNotice( '' );
		} else if ( null !== max ) {
			setFieldNotice(
				sprintf(
					// Translators: %1$s - field value, %2$d - minimum accepted value, %3$d - maximum accepted value.
					__( '"%1$s" is not within the accepted range (%2$d-%3$d).', 'bm-custom-login' ),
					editedValue,
					min,
					max,
				),
			);
		} else {
			setFieldNotice(
				sprintf(
					// Translators: %1$s - field value, %2$d - minimum accepted value.
					__( '"%1$s" must be greater than or equal to %2$d.', 'bm-custom-login' ),
					editedValue,
					min,
				),
			);
		}
	}, [ editedValue, min, max ] );

	/**
	 * Return component
	 *
	 * @return {Element} IntegerControl component.
	 */
	return (
		<div className="tsc-integer-control">
			<DetectOutside
				/**
				 * Validate the field's value
				 *
				 * @return {void}
				 */
				onFocusOutside={ () => {
					// A disabled field is non-interactive: never commit a pending edited value (e.g. one left over from before the field was disabled).
					if ( disabled ) {
						return;
					}

					const updatedValue =
						editedValue === getIntegerWithinRange( editedValue, min, max ).toString() ? Number.parseInt( editedValue, 10 ) : defaultValue;

					/**
					 * Only propagate a real change; a no-op commit on blur would otherwise
					 * fire spurious updates (and can resurrect a row being deleted).
					 */
					if ( updatedValue !== value ) {
						onChange( updatedValue );
					}

					setEditedValue( updatedValue.toString() );
				} }
			>
				<TextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					label={ label }
					help={ help }
					value={ editedValue }
					type="number"
					disabled={ disabled }
					aria-describedby={ '' !== fieldNotice ? noticeId : undefined }

					/**
					 * Update the value
					 *
					 * @param {string} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						setEditedValue( '' === updatedValue ? '' : Number.parseInt( updatedValue, 10 ).toString() );
					} }
				/>
			</DetectOutside>
			{ '' !== fieldNotice && <FieldNotice id={ noticeId } message={ fieldNotice } /> }
		</div>
	);
};

/**
 * Props validation
 */
IntegerControl.propTypes = {
	label: PropTypes.string.isRequired,
	help: PropTypes.string,
	min: PropTypes.number.isRequired,
	max: PropTypes.number,
	value: PropTypes.number.isRequired,
	defaultValue: PropTypes.number.isRequired,
	disabled: PropTypes.bool,
	onChange: PropTypes.func.isRequired,
};
