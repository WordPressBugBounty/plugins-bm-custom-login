/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';

/**
 * Import styles
 */
import './styles.scss';

/**
 * WaitingIndicator component
 *
 * @param {Object}  properties             Component properties object.
 * @param {string}  properties.message     Message to display along the spinner.
 * @param {boolean} properties.isCentered  Whether to center the whole element.
 * @param {boolean} properties.withPadding Whether to render additional top & bottom padding.
 *
 * @return {JSX} WaitingIndicator component.
 */
export const WaitingIndicator = ( { message, isCentered = false, withPadding = false } ) => {
	const classNames = [ 'tsc-waiting-indicator' ];

	if ( isCentered ) {
		classNames.push( 'tsc-waiting-indicator--centered' );
	}

	if ( withPadding ) {
		classNames.push( 'tsc-waiting-indicator--with-padding' );
	}

	return (
		<div className={ classNames.join( ' ' ) }>
			<Spinner __nextHasNoMarginBottom __next40pxDefaultSize />
			<p>{ message }</p>
		</div>
	);
};

/**
 * Props validation
 */
WaitingIndicator.propTypes = {
	message: PropTypes.string.isRequired,
	isCentered: PropTypes.bool,
	withPadding: PropTypes.bool,
};
