/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { TextControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FieldsGroup } from '../fields-group/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * MultilingualTextControl component
 *
 * @param {Object}   properties          Component properties object.
 * @param {string}   properties.label    Label.
 * @param {Function} properties.onChange Function callback to trigger on value change.
 * @param {Object}   properties.original Original translations.
 * @param {Object}   properties.values   Field's value.
 *
 * @return {JSX} MultilingualTextControl component.
 */
export const MultilingualTextControl = ( { label, onChange, original, values } ) => (
	<FieldsGroup className="tsc-multilingual-text-control" label={ label } withBaseControl>
		{ Object.keys( values ).map( ( key ) => {
			const help = original?.[ key ];
			const fieldLabel = key
				.split( /(?=[A-Z])/ )
				.map( ( piece, index ) => ( 1 === index ? `_${ piece }` : piece ) )
				.join( '' );

			return (
				<TextControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					className="tsc-multilingual-text-control__field"
					key={ key }
					label={ fieldLabel }
					value={ values[ key ] }
					help={
						'undefined' !== typeof help
							? sprintf(
									// Translators: %s - original text.
									__( 'Original: "%s"', 'bm-custom-login' ),
									help
							  )
							: undefined
					}
					/**
					 * Update the value
					 *
					 * @param {Object} updatedValue Updated value.
					 *
					 * @return {void}
					 */
					onChange={ ( updatedValue ) => {
						onChange( {
							...values,
							[ key ]: updatedValue,
						} );
					} }
				/>
			);
		} ) }
	</FieldsGroup>
);

/**
 * Props validation
 */
MultilingualTextControl.propTypes = {
	label: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
	original: PropTypes.object.isRequired,
	values: PropTypes.object.isRequired,
};
