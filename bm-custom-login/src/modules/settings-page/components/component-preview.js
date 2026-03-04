/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { WaitingIndicator } from '@teydeastudio/components/src/waiting-indicator/index.js';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { Notice, Panel } from '@wordpress/components';
import { useEffect, useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Preview component
 *
 * @param {Object} properties          Component properties object.
 * @param {Object} properties.settings Plugin settings.
 *
 * @return {JSX} Preview component.
 */
export const Preview = ( { settings } ) => {
	// Get the necessary data from global object.
	const currentLocale = window?.teydeaStudio?.bmCustomLogin?.settingsPage?.context?.currentLocale || 'en_US';

	// State management.
	const [ previewHTML, setPreviewHTML ] = useState( null );
	const [ isLoading, setIsLoading ] = useState( false );
	const [ loadingError, setLoadingError ] = useState( '' );

	// Refs for aborting fetch requests and debouncing.
	const abortControllerRef = useRef( null );
	const debounceTimeoutRef = useRef( null );

	/**
	 * Fetch preview HTML when settings change
	 *
	 * Ensure the API call is debounced to avoid excessive requests.
	 */
	useEffect( () => {
		// Set loading state immediately when settings change.
		setIsLoading( true );

		// Clear existing debounce timeout.
		if ( debounceTimeoutRef.current ) {
			clearTimeout( debounceTimeoutRef.current );
		}

		// Define async function for fetching preview.
		const fetchPreview = async () => {
			// Cancel any previous request.
			if ( abortControllerRef.current ) {
				abortControllerRef.current.abort();
			}

			// Create new abort controller.
			abortControllerRef.current = new AbortController();

			try {
				const response = await apiFetch( {
					path: `/bm-custom-login/v1/preview?wp_lang=${ currentLocale }`,
					method: 'POST',
					signal: abortControllerRef.current.signal,
					data: {
						settings: settings.data,
					},
					parse: false, // Don't parse JSON, we want the raw HTML response.
				} );

				const html = await response.text();
				setPreviewHTML( html );
			} catch ( error ) {
				if ( 'AbortError' !== error.name ) {
					setLoadingError( `Failed to fetch preview: ${ error.message }` );
				}
			} finally {
				setIsLoading( false );
			}
		};

		// Debounce the API call - only execute after 1 second of no changes.
		debounceTimeoutRef.current = setTimeout( () => {
			fetchPreview();
		}, 1000 );

		// Cleanup function.
		return () => {
			if ( debounceTimeoutRef.current ) {
				clearTimeout( debounceTimeoutRef.current );
			}

			if ( abortControllerRef.current ) {
				abortControllerRef.current.abort();
			}
		};
	}, [ currentLocale, settings ] );

	/**
	 * Trigger custom event when preview HTML is updated
	 * so that other components can react to the change
	 */
	useEffect( () => {
		if ( false === isLoading && null !== previewHTML && '' === loadingError ) {
			const event = new Event( 'custom-login/preview-updated' );
			window.dispatchEvent( event );
		}
	}, [ isLoading, previewHTML, loadingError ] );

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelUnderFormLinks component.
	 */
	return (
		<Panel className="bm-custom-login-settings-page__preview-panel" header={ __( 'Preview', 'bm-custom-login' ) }>
			{
				/**
				 * Loading failed notice
				 */
				loadingError && (
					<Notice __nextHasNoMarginBottom __next40pxDefaultSize isDismissible={ false } status="error">
						{ loadingError }
					</Notice>
				)
			}
			{
				/**
				 * Loading indicator
				 */
				null === previewHTML && '' === loadingError && <WaitingIndicator message={ __( 'Loading preview…', 'bm-custom-login' ) } />
			}
			{
				/**
				 * Preview iframe
				 */
				null !== previewHTML && '' === loadingError && (
					<div className="bm-custom-login-settings-page__preview">
						<iframe
							title={ __( 'Login Screen Preview', 'bm-custom-login' ) }
							sandbox="allow-scripts"
							srcDoc={ previewHTML }
							style={ {
								opacity: isLoading ? 0.2 : 1,
							} }
						/>
						{ isLoading && (
							<div className="bm-custom-login-settings-page__preview-reload-wrapper">
								<WaitingIndicator message={ __( 'Updating preview…', 'bm-custom-login' ) } />
							</div>
						) }
					</div>
				)
			}
		</Panel>
	);
};

/**
 * Props validation
 */
Preview.propTypes = {
	settings: PropTypes.object.isRequired,
};
