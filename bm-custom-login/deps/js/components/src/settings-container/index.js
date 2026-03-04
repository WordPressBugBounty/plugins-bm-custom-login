/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import { ProductIcon } from '../product-icon/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * SettingsContainer component
 *
 * @param {Object}  properties           Component properties object.
 * @param {JSX}     properties.actions   Actions components.
 * @param {JSX}     properties.children  Child component to render.
 * @param {string}  properties.pageTitle Page title.
 * @param {Object}  properties.product   Product's data object.
 * @param {boolean} properties.width     Width of the inner container; either "full" or "wide".
 *
 * @return {JSX} Settings component.
 */
export const SettingsContainer = ( { actions, children, pageTitle, product, width = 'full' } ) => {
	// Destructure the product object.
	const { key: productKey, type: productType } = product;

	// Collect the necessary data.
	const { slug } = window.teydeaStudio[ productKey ][ productType ];

	/**
	 * Render the component
	 */
	return (
		<div className="tsc-settings-container">
			<header className="tsc-settings-container__header">
				<ProductIcon slug={ slug } />
				<h1>{ pageTitle }</h1>
				<div className="tsc-settings-container__actions">{ actions }</div>
			</header>
			<div className={ `tsc-settings-container__container tsc-settings-container__container--${ width }-width` }>{ children }</div>
		</div>
	);
};

/**
 * Props validation
 */
SettingsContainer.propTypes = {
	actions: PropTypes.element.isRequired,
	children: PropTypes.element.isRequired,
	pageTitle: PropTypes.string.isRequired,
	product: PropTypes.shape( {
		key: PropTypes.string.isRequired,
		type: PropTypes.oneOf( [ 'plugin', 'theme' ] ),
	} ).isRequired,
	width: PropTypes.oneOf( [ 'full', 'wide' ] ),
};
