/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { TabPanel } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import { SettingsSidebar } from '../settings-sidebar/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * SettingsTabs component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.product     Product's data object.
 * @param {Object}   properties.settings    Settings object.
 * @param {Function} properties.setSettings Settings setter.
 *
 * @return {JSX} SettingsTabs component.
 */
export const SettingsTabs = ( { product, settings, setSettings } ) => {
	/**
	 * Tabs configuration
	 *
	 * @param {Array}    tabsConfig  Settings page tabs configuration.
	 * @param {Object}   settings    Settings object.
	 * @param {Function} setSettings Settings setter.
	 */
	const tabsConfig = applyFilters( 'custom_login__settings_page_tabs', [], settings, setSettings );

	/**
	 * Render the component
	 */
	return (
		<div className="tsc-settings-tabs">
			<TabPanel __nextHasNoMarginBottom __next40pxDefaultSize tabs={ tabsConfig } className="tsc-settings-tabs__wrapper">
				{
					/**
					 * Render single tab
					 *
					 * @param {Object} tab Tab object.
					 *
					 * @return {JSX} Tab component.
					 */
					( tab ) => <Fragment key={ tab.name }>{ tab.component }</Fragment>
				}
			</TabPanel>
			<SettingsSidebar product={ product } settings={ settings } />
		</div>
	);
};

/**
 * Props validation
 */
SettingsTabs.propTypes = {
	product: PropTypes.shape( {
		key: PropTypes.string.isRequired,
		type: PropTypes.oneOf( [ 'plugin', 'theme' ] ),
	} ).isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
