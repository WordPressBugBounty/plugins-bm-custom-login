/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * Import styles
 */
import './styles.scss';

/**
 * FieldNotice component
 *
 * @param {Object} properties         Component properties object.
 * @param {string} properties.message Message to display in the notice.
 *
 * @return {JSX} FieldNotice component.
 */
export const FieldNotice = ( { message } ) => (
	<div className="tsc-field-notice">
		<p>{ message }</p>
	</div>
);

/**
 * Props validation
 */
FieldNotice.propTypes = {
	message: PropTypes.string.isRequired,
};
