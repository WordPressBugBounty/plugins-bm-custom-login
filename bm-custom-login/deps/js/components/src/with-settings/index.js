/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { Button, Notice, SnackbarList } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useEffect, useReducer } from '@wordpress/element';
import { applyFilters, doActionAsync } from '@wordpress/hooks';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { WaitingIndicator } from '../waiting-indicator/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * Data state reducer
 *
 * @param {Object} state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Object} Updated state object.
 */
const dataStateReducer = ( state, action ) => {
	switch ( action.type ) {
		/**
		 * Settings has been fetched from the REST API endpoint
		 */
		case 'fetchedSettings': {
			return {
				...state,
				settings: action.settings,
				hasFetchedSettings: true,
				isSettingsFetchFailed: false,
			};
		}

		/**
		 * Settings fetch failed
		 */
		case 'settingsFetchFailed': {
			return {
				...state,
				hasFetchedSettings: true,
				isSettingsFetchFailed: true,
			};
		}

		/**
		 * Save updated settings
		 */
		case 'saveSettings': {
			return {
				...state,
				isSavingSettings: true,
			};
		}

		/**
		 * Settings saved
		 */
		case 'settingsSaved': {
			return {
				...state,
				notices: [
					...state.notices,
					{
						id: `n:${ Date.now().toString() }`,
						status: 'success',
						content: __( 'Settings saved', 'bm-custom-login' ),
						isDismissible: true,
						explicitDismiss: false,
					},
				],
				isSavingSettings: false,
			};
		}

		/**
		 * Settings save failed
		 */
		case 'settingsSaveFailed': {
			let content = __( 'Settings were not saved, something went wrong.', 'bm-custom-login' );

			// Try to be more specific.
			if ( 'Invalid parameter(s): settings' === action.error.message && 'string' === typeof action.error?.data?.params?.settings ) {
				content = sprintf(
					// Translators: %s - error message.
					__( 'Settings were not saved due to validation error: %s Please update the invalid field value and try again.', 'bm-custom-login' ),
					action.error.data.params.settings
				);
			}

			return {
				...state,
				notices: [
					...state.notices,
					{
						id: `n:${ Date.now().toString() }`,
						status: 'error',
						content,
						isDismissible: true,
						explicitDismiss: false,
					},
				],
				isSavingSettings: false,
			};
		}

		/**
		 * Settings changed
		 */
		case 'settingsChanged': {
			/**
			 * Allow other plugins and modules to modify the settings
			 * object before it's updated state is saved
			 *
			 * @param {Object} settings Settings object.
			 */
			const updatedSettings = applyFilters( 'custom_login__pre_change_settings', action.settings );

			return {
				...state,
				settings: updatedSettings,
			};
		}

		/**
		 * Snackbar notice removed
		 */
		case 'noticeRemoved': {
			return {
				...state,
				notices: [ ...state.notices.filter( ( notice ) => notice.id !== action.noticeId ) ],
			};
		}
	}

	return state;
};

/**
 * Render the SnackbarList component near the given
 * children and control the notices displayed
 *
 * @param {Object} properties          Component properties object.
 * @param {JSX}    properties.children Child component to render.
 *
 * @return {JSX} WithSettings component.
 */
export const withSettings = createHigherOrderComponent( ( Component ) => {
	return ( props ) => {
		// Destructure the props object.
		const { product, LoadingContainer } = props; // eslint-disable-line react/prop-types

		// Destructure the product object.
		const { key: productKey, type: productType } = product; // eslint-disable-line react/prop-types

		// Collect the necessary data.
		const { slug } = window.teydeaStudio[ productKey ][ productType ];
		const { nonce } = window.teydeaStudio[ productKey ].settingsPage;

		// Data state.
		const [ dataState, dispatchDataState ] = useReducer( dataStateReducer, {
			notices: [],
			settings: {},
			hasFetchedSettings: false,
			isSettingsFetchFailed: false,
			isSavingSettings: false,
		} );

		/**
		 * Save updated settings
		 */
		useEffect( () => {
			if ( true === dataState.isSavingSettings ) {
				apiFetch( {
					path: `/${ slug }/v1/settings`,
					method: 'POST',
					data: {
						nonce,
						settings: dataState.settings,
					},
				} )
					.then( async ( response ) => {
						// Dispatch data state.
						dispatchDataState( { type: 'settingsSaved' } );

						/**
						 * Allow other modules to handle custom
						 * actions after the plugin settings
						 * has been saved
						 */
						await doActionAsync( 'custom_login__settings_saved' );

						// Continue the response chain.
						return response;
					} )
					.catch( ( error ) => {
						console.error( error ); // eslint-disable-line no-console
						dispatchDataState( {
							type: 'settingsSaveFailed',
							error,
						} );
					} );
			}
		}, [ dataState.isSavingSettings ] ); // eslint-disable-line react-hooks/exhaustive-deps

		/**
		 * Fetch saved settings on initial render
		 */
		useEffect( () => {
			apiFetch( {
				path: `/${ slug }/v1/settings`,
				method: 'GET',
			} )
				.then( ( settings ) => {
					dispatchDataState( { type: 'fetchedSettings', settings } );
					return settings;
				} )
				.catch( ( error ) => {
					console.error( error ); // eslint-disable-line no-console
					dispatchDataState( { type: 'settingsFetchFailed' } );
				} );
		}, [] ); // eslint-disable-line react-hooks/exhaustive-deps

		/**
		 * The "save settings" button component
		 *
		 * @return {JSX} SaveSettingsButton component.
		 */
		const SaveSettingsButton = () => (
			<Button
				__nextHasNoMarginBottom
				__next40pxDefaultSize
				disabled={ dataState.isSavingSettings || ! dataState.hasFetchedSettings || dataState.isSettingsFetchFailed }
				isBusy={ dataState.isSavingSettings }
				onClick={ () => {
					dispatchDataState( { type: 'saveSettings' } );
				} }
				variant="primary"
			>
				{ dataState.isSavingSettings ? __( 'Saving…', 'bm-custom-login' ) : __( 'Save all settings', 'bm-custom-login' ) }
			</Button>
		);

		/**
		 * Still fetching?
		 */
		if ( false === dataState.hasFetchedSettings ) {
			return (
				<LoadingContainer>
					<WaitingIndicator message={ __( 'Loading…', 'bm-custom-login' ) } isCentered withPadding />
				</LoadingContainer>
			);
		}

		/**
		 * Fetch failed?
		 */
		if ( true === dataState.isSettingsFetchFailed ) {
			return (
				<LoadingContainer>
					<Notice __nextHasNoMarginBottom __next40pxDefaultSize status="error" isDismissible={ false }>
						<p>{ __( 'Settings fetch failed.', 'bm-custom-login' ) }</p>
						<p>{ __( 'Please try again; if the issue keeps repeating, reach out to our support team.', 'bm-custom-login' ) }</p>
					</Notice>
				</LoadingContainer>
			);
		}

		/**
		 * Render the component
		 */
		return (
			<div className="tsc-with-settings">
				<SnackbarList
					notices={ dataState.notices }
					/**
					 * Remove single notice
					 *
					 * @param {string} noticeId Notice ID.
					 */
					onRemove={ ( noticeId ) => {
						dispatchDataState( {
							type: 'noticeRemoved',
							noticeId,
						} );
					} }
				/>
				<Component
					{ ...props }
					SaveSettingsButton={ SaveSettingsButton }
					settings={ dataState.settings }
					/**
					 * Settings setter
					 *
					 * @param {Object} updatedSettings Updated settings object.
					 *
					 * @return {void}
					 */
					setSettings={ ( updatedSettings ) => {
						dispatchDataState( {
							type: 'settingsChanged',
							settings: updatedSettings,
						} );
					} }
				/>
			</div>
		);
	};
}, 'withSettings' );
