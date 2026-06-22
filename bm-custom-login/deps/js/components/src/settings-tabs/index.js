/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { TabPanel } from '@wordpress/components';
import { Fragment, useCallback, useEffect, useMemo, useRef, useState } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { cleanForSlug } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { SettingsSidebar } from '../settings-sidebar/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * Convert a value to a URL-safe slug, returning an empty string when the
 * value is not a non-empty string or cannot produce a usable slug.
 *
 * @param {*} value Candidate value to sanitize.
 *
 * @return {string} URL-safe slug, or empty string when no valid slug can be derived.
 */
const toSlug = ( value ) => {
	if ( 'string' !== typeof value || '' === value ) {
		return '';
	}

	return cleanForSlug( value );
};

/**
 * Read the current URL hash and return it as a sanitized slug.
 *
 * @return {string} Sanitized hash slug, or empty string when unavailable.
 */
const readHashSlug = () => {
	if ( 'undefined' === typeof window || ! window.location ) {
		return '';
	}

	return toSlug( window.location.hash.replace( /^#/, '' ) );
};

/**
 * Find the first tab whose `name` sanitizes to the given slug.
 *
 * @param {Array}  tabs Tab configuration array.
 * @param {string} slug Candidate slug.
 *
 * @return {Object|undefined} Matching tab object, or `undefined` when no tab matches.
 */
const findTabBySlug = ( tabs, slug ) => {
	if ( '' === slug || ! Array.isArray( tabs ) ) {
		return undefined;
	}

	return tabs.find( ( tab ) => {
		const tabSlug = toSlug( tab?.name );

		return '' !== tabSlug && tabSlug === slug;
	} );
};

/**
 * SettingsTabs component
 *
 * Wraps `@wordpress/components`'s `TabPanel` with URL-hash deep-linking:
 * the active tab is reflected in `window.location.hash`, and arriving with
 * a matching hash activates the corresponding tab on mount. Hash updates
 * use `history.replaceState` so they do not create extra history entries
 * or trigger a page reload. External hash changes (back/forward navigation,
 * manual edits, in-page anchor links) are observed via the `hashchange`
 * event and reflected back into the active tab.
 *
 * If a tab has no name (or its name cannot be sanitized into a URL-safe
 * slug), it is silently excluded from the deep-link mapping and the
 * component continues to behave like the underlying `TabPanel`.
 *
 * @param {Object}   properties             Component properties object.
 * @param {Object}   properties.product     Product's data object.
 * @param {Object}   properties.settings    Settings object.
 * @param {Function} properties.setSettings Settings setter.
 *
 * @return {Element} SettingsTabs component.
 */
export const SettingsTabs = ( { product, settings, setSettings } ) => {
	/**
	 * Tabs configuration. Memoized so the hashchange effect below does not
	 * re-register its listener on every render.
	 *
	 * @param {Array}    tabsConfig  Settings page tabs configuration.
	 * @param {Object}   settings    Settings object.
	 * @param {Function} setSettings Settings setter.
	 */
	const tabsConfig = useMemo( () => applyFilters( 'custom_login__settings_page_tabs', [], settings, setSettings ), [ settings, setSettings ] );

	/**
	 * Resolve the initial active tab from the current URL hash; falls back
	 * to `undefined`, which lets `TabPanel` pick its own default (the first tab).
	 */
	const [ activeTabName, setActiveTabName ] = useState( () => findTabBySlug( tabsConfig, readHashSlug() )?.name );

	/**
	 * `TabPanel` reads `initialTabName` only on mount, so we force it to
	 * remount when the active tab is changed by something other than the
	 * user (e.g. a `hashchange` event) by bumping this key.
	 */
	const [ remountKey, setRemountKey ] = useState( 0 );

	/**
	 * Latest `activeTabName` mirrored into a ref so the hashchange effect
	 * below can read it without re-registering its listener on every change.
	 */
	const activeTabNameRef = useRef( activeTabName );
	activeTabNameRef.current = activeTabName;

	/**
	 * Observe external hash changes and sync the active tab to match.
	 */
	useEffect( () => {
		if ( 'undefined' === typeof window ) {
			return undefined;
		}

		/**
		 * Handle `hashchange` events.
		 */
		const handleHashChange = () => {
			const nextTabName = findTabBySlug( tabsConfig, readHashSlug() )?.name;

			if ( nextTabName !== activeTabNameRef.current ) {
				setActiveTabName( nextTabName );
				setRemountKey( ( previous ) => previous + 1 );
			}
		};

		window.addEventListener( 'hashchange', handleHashChange );

		return () => window.removeEventListener( 'hashchange', handleHashChange );
	}, [ tabsConfig ] );

	/**
	 * Update local state and reflect the selected tab in the URL hash
	 * when a tab is activated via the `TabPanel` UI.
	 *
	 * @param {string} tabName Name of the selected tab.
	 */
	const handleSelect = useCallback( ( tabName ) => {
		setActiveTabName( tabName );

		if ( 'undefined' === typeof window || ! window.history ) {
			return;
		}

		const slug = toSlug( tabName );

		/**
		 * Tab has no usable slug (opt-in contract): clear any stale hash so a
		 * reload/share doesn't reopen a previously linked, unrelated tab.
		 */
		if ( '' === slug ) {
			if ( '' !== window.location.hash ) {
				window.history.replaceState( null, '', window.location.pathname + window.location.search );
			}

			return;
		}

		const newHash = `#${ slug }`;

		if ( window.location.hash !== newHash ) {
			window.history.replaceState( null, '', newHash );
		}
	}, [] );

	/**
	 * Render the component
	 */
	return (
		<div className="tsc-settings-tabs">
			<TabPanel
				__nextHasNoMarginBottom
				__next40pxDefaultSize
				key={ remountKey }
				initialTabName={ activeTabName }
				onSelect={ handleSelect }
				tabs={ tabsConfig }
				className="tsc-settings-tabs__wrapper"
			>
				{
					/**
					 * Render single tab
					 *
					 * @param {Object} tab Tab object.
					 *
					 * @return {Element} Tab component.
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
