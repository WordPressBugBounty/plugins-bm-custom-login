/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { buildId } from '@teydeastudio/utils/src/build-id.js';

/**
 * WordPress dependencies
 */
import { BaseControl, useBaseControlProps } from '@wordpress/components';
import { cleanForSlug } from '@wordpress/url';

/**
 * Import styles
 */
import './styles.scss';

/**
 * FieldsGroup component
 *
 * @param {Object}  properties                 Component properties object.
 * @param {JSX}     properties.children        Child component to render.
 * @param {string}  properties.className       Additional class name to use on container.
 * @param {string}  properties.help            BaseControl component help.
 * @param {string}  properties.label           BaseControl component label.
 * @param {boolean} properties.withBaseControl Whether to wrap the fields group with BaseControl component.
 * @param {boolean} properties.withBoxBorder   Whether to add border to all inner elements.
 * @param {boolean} properties.withReducedGap  Whether the gap between fields should be reduced.
 *
 * @return {JSX} FieldsGroup component.
 */
export const FieldsGroup = ( {
	children,
	className = '',
	help = '',
	label = '',
	withBaseControl = false,
	withBoxBorder = false,
	withReducedGap = false,
	...otherProps
} ) => {
	let classNames = [
		className,
		'tsc-fields-group',
		withReducedGap ? 'tsc-fields-group--with-reduced-gap' : '',
		withBoxBorder ? 'tsc-fields-group--with-box-border' : '',
	];

	// Skip empty values.
	classNames = classNames.filter( ( value ) => '' !== value );

	if ( withBaseControl ) {
		// Get the base control props.
		const { baseControlProps, controlProps } = useBaseControlProps( {
			preferredId: buildId( 'npm-components', 'fields-group', cleanForSlug( label ) ),
		} );

		return (
			<BaseControl { ...baseControlProps } __nextHasNoMarginBottom label={ label } help={ help }>
				<div { ...controlProps } className={ classNames.join( ' ' ) } { ...otherProps }>
					{ children }
				</div>
			</BaseControl>
		);
	}

	return (
		<div className={ classNames.join( ' ' ) } { ...otherProps }>
			{ children }
		</div>
	);
};

/**
 * Props validation
 */
FieldsGroup.propTypes = {
	children: PropTypes.element.isRequired,
	className: PropTypes.string,
	help: PropTypes.string,
	label: PropTypes.string,
	withBaseControl: PropTypes.bool,
	withBoxBorder: PropTypes.bool,
	withReducedGap: PropTypes.bool,
};
