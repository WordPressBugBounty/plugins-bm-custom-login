/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { withFocusOutside } from '@wordpress/components';
import { Component } from '@wordpress/element';

/**
 * DetectOutside component
 */
export const DetectOutside = withFocusOutside(
	// eslint-disable-next-line react/display-name
	class extends Component {
		/**
		 * Handle focus outside event
		 *
		 * @param {Event} event Focus event.
		 *
		 * @return {void}
		 */
		handleFocusOutside( event ) {
			this.props.onFocusOutside( event ); // eslint-disable-line react/prop-types
		}

		/**
		 * Render the component.
		 *
		 * @return {JSX} Component to render.
		 */
		render() {
			return this.props.children; // eslint-disable-line react/prop-types
		}
	}
);

/**
 * Props validation
 */
DetectOutside.propTypes = {
	children: PropTypes.element.isRequired,
	onFocusOutside: PropTypes.func.isRequired,
};
