/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Maybe render the panel with promoted plugins
 *
 * @param {Object} properties         Component properties object.
 * @param {Array}  properties.plugins Data of the promoted plugins.
 *
 * @return {null|JSX} Promoted plugins panel component.
 */
export const PromotedPluginsPanel = ( { plugins } ) => {
	const contents = [];

	for ( const plugin of plugins ) {
		const { url, name, description } = plugin;

		contents.push(
			<p>
				<strong>
					<a href={ url } rel="noopener noreferrer" target="_blank">
						{ name }
					</a>
				</strong>{ ' ' }
				- { description }
			</p>
		);
	}

	// Do we have anything to render?
	if ( 0 === contents.length ) {
		return null;
	}

	/**
	 * Render the panel
	 *
	 * @return {JSX}
	 */
	return <PanelBody title={ __( 'Also by Teydea Studio', 'bm-custom-login' ) }>{ contents }</PanelBody>;
};

/**
 * Props validation
 */
PromotedPluginsPanel.propTypes = {
	plugins: PropTypes.shape( {
		url: PropTypes.string.isRequired,
		name: PropTypes.string.isRequired,
		description: PropTypes.string.isRequired,
	} ).isRequired,
};
