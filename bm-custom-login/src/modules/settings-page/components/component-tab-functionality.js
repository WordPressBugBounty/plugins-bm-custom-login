/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { PanelWithReset } from '@teydeastudio/components/src/panel-with-reset/index.js';

/**
 * WordPress dependencies
 */
import { Panel } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import { PanelMiscellaneous } from './component-panel-miscellaneous.js';

/**
 * TabFunctionality component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} TabFunctionality component.
 */
export const TabFunctionality = ( { settings, setSettings } ) => {
	/**
	 * Allow other plugins and modules to filter
	 * the panels displayed in this tab
	 *
	 * @param {Object}   panels      The current panels.
	 * @param {Object}   settings    Plugin settings.
	 * @param {Function} setSettings Function (callback) used to update the settings.
	 * @param {Object}   context     The context object.
	 * @param {Object}   presets     The presets object.
	 */
	const panels = applyFilters(
		'custom_login__settings_page_functionality_panels',
		{
			miscellaneous: <PanelMiscellaneous settings={ settings } setSettings={ setSettings } />,
		},
		settings,
		setSettings,
		{},
		{}
	);

	/**
	 * Render the component
	 */
	return (
		<Fragment>
			<Panel>
				{ Object.keys( panels ).map( ( panelKey, index ) => {
					const panel = panels[ panelKey ];

					return (
						<PanelWithReset slug={ panelKey } key={ index } settings={ settings } setSettings={ setSettings }>
							{ panel }
						</PanelWithReset>
					);
				} ) }
			</Panel>
		</Fragment>
	);
};

/**
 * Props validation
 */
TabFunctionality.propTypes = {
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
