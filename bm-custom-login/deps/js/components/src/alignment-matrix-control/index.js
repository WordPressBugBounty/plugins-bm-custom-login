/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { AlignmentMatrixControl as CoreAlignmentMatrixControl, Button, Dropdown, Icon, SelectControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FieldsGroup } from '../fields-group/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * AlignmentMatrixControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {string}   properties.label    Label.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 * @param {string}   properties.value    Field's value.
 *
 * @return {JSX} AlignmentMatrixControl component.
 */
export const AlignmentMatrixControl = ( { label, onChange, value } ) => {
	// State management.
	const [ type, setType ] = useState( 'default' === value ? 'default' : 'custom' );

	/**
	 * Update the type when the value changes
	 * from outside
	 */
	useEffect( () => {
		setType( 'default' === value ? 'default' : 'custom' );
	}, [ value ] );

	/**
	 * Render the component
	 *
	 * @return {JSX} AlignmentMatrixControl component.
	 */
	return (
		<FieldsGroup className="tsc-alignment-matrix-control">
			<SelectControl
				__nextHasNoMarginBottom
				__next40pxDefaultSize
				label={ label }
				value={ type }
				options={ [
					{ value: 'default', label: __( 'Default', 'bm-custom-login' ) },
					{ value: 'custom', label: __( 'Custom', 'bm-custom-login' ) },
				] }
				/**
				 * Update the value
				 *
				 * @param {string} updatedValue Updated value.
				 *
				 * @return {void}
				 */
				onChange={ ( updatedValue ) => {
					setType( updatedValue );
					onChange( 'default' === updatedValue ? 'default' : 'center center' );
				} }
			/>
			{ 'custom' === type && (
				<Dropdown
					/**
					 * Render the dropdown toggle
					 *
					 * @param {Object}   properties          Toggle component properties.
					 * @param {boolean}  properties.isOpen   Whether the dropdown is currently open.
					 * @param {Function} properties.onToggle Callback function to toggle the dropdown.
					 *
					 * @return {JSX} Toggle component.
					 */
					renderToggle={ ( { isOpen, onToggle } ) => (
						<Button
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							aria-expanded={ isOpen }
							className="tsc-alignment-matrix-control__toggle"
							onClick={ onToggle }
							variant="tertiary"
						>
							<Icon icon={ <CoreAlignmentMatrixControl.Icon value={ value } /> } />
						</Button>
					) }
					renderContent={ () => <CoreAlignmentMatrixControl value={ value } onChange={ onChange } /> }
				/>
			) }
		</FieldsGroup>
	);
};

/**
 * Props validation
 */
AlignmentMatrixControl.propTypes = {
	label: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
	value: PropTypes.string.isRequired,
};
