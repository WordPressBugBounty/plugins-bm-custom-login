/**
 * External dependencies
 */
import { PromotedPluginsPanel } from '@teydeastudio/components/src/promoted-plugins-panel/index.js';
import { SettingsContainer } from '@teydeastudio/components/src/settings-container/index.js';
import { SettingsTabs } from '@teydeastudio/components/src/settings-tabs/index.js';
import { render } from '@teydeastudio/utils/src/render.js';
import { UpsellPanel } from '@teydeastudio/components/src/upsell-panel/index.js';
import { withSettings } from '@teydeastudio/components/src/with-settings/index.js';

/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { MediaUpload } from '@wordpress/media-utils';

/**
 * Internal dependencies
 */
import { Preview } from './components/component-preview.js';
import { TabFunctionality } from './components/component-tab-functionality.js';
import { TabDesign } from './components/component-tab-design.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * Filter the MediaUpload component to make
 * it work in our settings page
 *
 * @see https://github.com/WordPress/gutenberg/blob/wp/6.7/packages/block-editor/src/components/media-upload/README.md#setup
 */
addFilter( 'editor.MediaUpload', 'teydeastudio/custom-login/settings-page', () => MediaUpload );

/**
 * Filter the settings page tabs configuration to include
 * the plugin settings
 */
addFilter(
	'custom_login__settings_page_tabs',
	'teydeastudio/custom-login/settings-page',

	/**
	 * Filter the settings page tabs configuration
	 *
	 * @param {Array}    tabsConfig  Settings page tabs configuration.
	 * @param {Object}   settings    Settings object.
	 * @param {Function} setSettings Settings setter.
	 *
	 * @return {Array} Updated settings page tabs configuration.
	 */
	( tabsConfig, settings, setSettings ) => {
		/**
		 * Add custom tabs configuration to
		 * the filtered array
		 */
		tabsConfig.push( {
			name: 'design',
			title: __( 'Design', 'bm-custom-login' ),
			component: <TabDesign settings={ settings } setSettings={ setSettings } />,
		} );

		tabsConfig.push( {
			name: 'functionality',
			title: __( 'Functionality', 'bm-custom-login' ),
			component: <TabFunctionality settings={ settings } setSettings={ setSettings } />,
		} );

		return tabsConfig;
	}
);

/**
 * Render the "promoted plugins" panel
 */
addFilter(
	'custom_login__promoted_plugins_panel',
	'teydeastudio/custom-login/settings-page',

	/**
	 * Render the "promoted plugins" panel
	 *
	 * @return {JSX} Updated "promoted plugins" panel.
	 */
	() => (
		<PromotedPluginsPanel
			plugins={ [
				{
					url: 'https://wppasswordpolicy.com/?utm_source=WP+Custom+Login',
					name: __( 'WP Password Policy', 'bm-custom-login' ),
					description: __(
						"Define advanced password policies, enforce strong password requirements, and improve your WordPress site's security.",
						'bm-custom-login'
					),
				},
			] }
		/>
	)
);

/**
 * Render the "upsell" panel
 */
addFilter(
	'custom_login__upsell_panel',
	'teydeastudio/custom-login/settings-page',

	/**
	 * Render the "upsell" panel
	 *
	 * @param {JSX} panel The "upsell" panel.
	 *
	 * @return {JSX} Updated "upsell" panel.
	 */
	( panel ) => {
		// Load the panel only if PRO version of the plugin is not active.
		if ( ! window?.teydeaStudio?.bmCustomLogin?.plugin?.isPro ) {
			panel = (
				<UpsellPanel
					url="https://wpcustomlogin.com/pricing/?utm_source=WP+Custom+Login"
					benefits={ [
						__( '20+ predefined templates to choose from for a quick start', 'bm-custom-login' ),
						__( 'Advanced background customization options for a unique login experience', 'bm-custom-login' ),
						__( 'Configurable post-login redirects for all users, specific users, or based on user roles', 'bm-custom-login' ),
						__( 'Custom login path to replace the default wp-login.php for improved security', 'bm-custom-login' ),
						__( 'Access to PRO updates and our premium support', 'bm-custom-login' ),
					] }
				/>
			);
		}

		// Return updated panel component.
		return panel;
	}
);

/**
 * Render the login screen preview after
 * the settings sidebar panel
 */
addFilter(
	'custom_login__after_sidebar_panel',
	'teydeastudio/custom-login/settings-page',

	/**
	 * Render the login screen preview after
	 * the settings sidebar panel
	 *
	 * @param {JSX}    component The "after sidebar" panel.
	 * @param {Object} settings  Settings object.
	 *
	 * @return {JSX} Updated "after sidebar" panel.
	 */
	( component, settings ) => <Preview settings={ settings } />
);

/**
 * Define the product data
 */
const product = {
	key: 'bmCustomLogin',
	type: 'plugin',
};

// Destructure the product object.
const { key: productKey } = product;

// Collect the necessary data.
const { pageTitle } = window.teydeaStudio[ productKey ].settingsPage;

/**
 * SettingsPage component
 *
 * @return {JSX}
 */
const SettingsPage = withSettings( ( { SaveSettingsButton, setSettings, settings } ) => {
	/**
	 * Render the component
	 */
	return (
		<SettingsContainer actions={ <SaveSettingsButton /> } pageTitle={ pageTitle } product={ product }>
			<SettingsTabs product={ product } settings={ settings } setSettings={ setSettings } />
		</SettingsContainer>
	);
} );

/**
 * Render the settings form
 */
render(
	<SettingsPage
		product={ product }
		/**
		 * LoadingContainer component
		 *
		 * @param {JSX} children Children to render.
		 *
		 * @return {JSX} LoadingContainer component.
		 */
		LoadingContainer={ ( { children } ) => (
			<SettingsContainer pageTitle={ pageTitle } product={ product }>
				{ children }
			</SettingsContainer>
		) }
	/>,
	document.querySelector( 'div#bm-custom-login-settings-page' )
);
