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
 * @param {string} [properties.id]    Optional DOM id, so a control can reference the notice via `aria-describedby`.
 *
 * @return {Element} FieldNotice component.
 */
export const FieldNotice = ( { message, id } ) => (
	<div className="tsc-field-notice" id={ id } aria-live="polite">
		<p>{ message }</p>
	</div>
);

/**
 * Props validation
 */
FieldNotice.propTypes = {
	message: PropTypes.string.isRequired,
	id: PropTypes.string,
};
