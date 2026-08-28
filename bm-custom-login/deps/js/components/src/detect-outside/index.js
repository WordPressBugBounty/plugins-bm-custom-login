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
	class extends Component {
		/**
		 * Handle focus outside event
		 *
		 * @param {Event} event Focus event.
		 *
		 * @return {void}
		 */
		handleFocusOutside( event ) {
			this.props.onFocusOutside( event );
		}

		/**
		 * Render the component.
		 *
		 * @return {Element} Component to render.
		 */
		render() {
			return this.props.children;
		}
	},
);

/**
 * Props validation
 */
DetectOutside.propTypes = {
	children: PropTypes.element.isRequired,
	onFocusOutside: PropTypes.func.isRequired,
};
