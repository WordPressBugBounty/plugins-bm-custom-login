/**
 * External dependencies
 */
import { composeColorPalettes } from '@teydeastudio/utils/src/compose-color-palettes.js';
import { composeFontFamilies } from '@teydeastudio/utils/src/compose-font-families.js';
import { composeFontSizes } from '@teydeastudio/utils/src/compose-font-sizes.js';
import { composeFontWeights } from '@teydeastudio/utils/src/compose-font-weights.js';
import { composeGradientPalettes } from '@teydeastudio/utils/src/compose-gradient-palettes.js';
import { composeShadowPresets } from '@teydeastudio/utils/src/compose-shadow-presets.js';
import { PanelWithReset } from '@teydeastudio/components/src/panel-with-reset/index.js';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { Panel } from '@wordpress/components';
import { Fragment, useEffect, useState } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import { PanelBackground } from './component-panel-background.js';
import { PanelCustomCSS } from './component-panel-custom-css.js';
import { PanelFooter } from './component-panel-footer.js';
import { PanelLanguageSwitcher } from './component-panel-language-switcher.js';
import { PanelLoginFormButtonPrimary } from './component-panel-login-form-button-primary.js';
import { PanelLoginFormButtonSecondary } from './component-panel-login-form-button-secondary.js';
import { PanelLoginFormCheckboxFields } from './component-panel-login-form-checkbox-fields.js';
import { PanelLoginFormContainer } from './component-panel-login-form-container.js';
import { PanelLoginFormInputFields } from './component-panel-login-form-input-fields.js';
import { PanelLoginFormLabels } from './component-panel-login-form-labels.js';
import { PanelLoginFormRememberMeCheckbox } from './component-panel-login-form-remember-me-checkbox.js';
import { PanelLogo } from './component-panel-logo.js';
import { PanelNotices } from './component-panel-notices.js';
import { PanelPrivacyPolicyLink } from './component-panel-privacy-policy-link.js';
import { PanelSocialMediaLinks } from './component-panel-social-media-links.js';
import { PanelTemplates } from './component-panel-templates.js';
import { PanelUnderFormLinks } from './component-panel-under-form-links.js';

/**
 * TabDesign component
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 *
 * @return {JSX} TabDesign component.
 */
export const TabDesign = ( { settings, setSettings } ) => {
	// Destructure the settingsPage object.
	const { context, styles } = window.teydeaStudio.bmCustomLogin.settingsPage;

	// Destructure the context object.
	const { supportsMultipleLanguages } = context;

	// State management.
	const [ colorPalettes, setColorPalettes ] = useState( composeColorPalettes( styles.colorPalettes, settings ) );
	const [ fontFamilies ] = useState( composeFontFamilies( styles.fontFamilies ) );
	const [ fontSizes ] = useState( composeFontSizes( styles.fontSizes ) );
	const [ fontWeights ] = useState( composeFontWeights( styles.fontFamilies ) );
	const [ gradientPalettes ] = useState( composeGradientPalettes( styles.gradientPalettes ) );
	const [ shadowPresets ] = useState( composeShadowPresets( styles.shadowPresets ) );

	// Presets convenience object.
	const presets = {
		colorPalettes,
		fontFamilies,
		fontSizes,
		fontWeights,
		gradientPalettes,
		shadowPresets,
	};

	/**
	 * Whenever the settings change,
	 * update the color palettes
	 */
	useEffect( () => {
		setColorPalettes( composeColorPalettes( styles.colorPalettes, settings ) );
	}, [ settings ] ); // eslint-disable-line react-hooks/exhaustive-deps

	/**
	 * Prepare a list of panels to be displayed in the tab
	 */
	let panels = {
		background: <PanelBackground context={ context } presets={ presets } settings={ settings } setSettings={ setSettings } />,
		logo: <PanelLogo context={ context } settings={ settings } setSettings={ setSettings } />,
		loginFormContainer: <PanelLoginFormContainer presets={ presets } settings={ settings } setSettings={ setSettings } />,
		loginFormLabels: <PanelLoginFormLabels context={ context } presets={ presets } settings={ settings } setSettings={ setSettings } />,
		loginFormInputFields: <PanelLoginFormInputFields context={ context } presets={ presets } settings={ settings } setSettings={ setSettings } />,
		loginFormCheckboxFields: <PanelLoginFormCheckboxFields presets={ presets } settings={ settings } setSettings={ setSettings } />,
		loginFormRememberMeCheckbox: <PanelLoginFormRememberMeCheckbox context={ context } settings={ settings } setSettings={ setSettings } />,
		loginFormButtonPrimary: <PanelLoginFormButtonPrimary context={ context } presets={ presets } settings={ settings } setSettings={ setSettings } />,
		loginFormButtonSecondary: <PanelLoginFormButtonSecondary context={ context } presets={ presets } settings={ settings } setSettings={ setSettings } />,
		notices: <PanelNotices context={ context } presets={ presets } settings={ settings } setSettings={ setSettings } />,
		underFormLinks: <PanelUnderFormLinks presets={ presets } settings={ settings } setSettings={ setSettings } />,
		socialMediaLinks: <PanelSocialMediaLinks presets={ presets } settings={ settings } setSettings={ setSettings } />,
		privacyPolicyLink: <PanelPrivacyPolicyLink presets={ presets } settings={ settings } setSettings={ setSettings } />,
		footer: <PanelFooter presets={ presets } settings={ settings } setSettings={ setSettings } />,
	};

	if ( supportsMultipleLanguages ) {
		panels.languageSwitcher = <PanelLanguageSwitcher presets={ presets } settings={ settings } setSettings={ setSettings } />;
	}

	panels.customCss = <PanelCustomCSS settings={ settings } setSettings={ setSettings } />;

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
	panels = applyFilters( 'custom_login__settings_page_design_panels', panels, settings, setSettings, context, presets );

	/**
	 * Allow other plugins and modules to filter
	 * the Templates panel component
	 *
	 * @param {JSX}      templates   The PanelTemplates component.
	 * @param {Object}   settings    Plugin settings.
	 * @param {Function} setSettings Function (callback) used to update the settings.
	 */
	const templates = applyFilters(
		'custom_login__settings_page_design_templates',
		<PanelTemplates settings={ settings } setSettings={ setSettings } />,
		settings,
		setSettings
	);

	/**
	 * Render the component
	 */
	return (
		<Fragment>
			{ null !== templates && <Panel>{ templates }</Panel> }
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
TabDesign.propTypes = {
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
};
