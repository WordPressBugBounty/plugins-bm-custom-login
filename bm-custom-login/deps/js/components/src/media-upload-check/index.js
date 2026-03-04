/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { WaitingIndicator } from '../waiting-indicator/index.js';

/**
 * MediaUploadCheck component
 *
 * @param {Object}  properties                Component properties object.
 * @param {JSX}     properties.children       Children component, to be rendered when user has the necessary capabilities.
 * @param {JSX}     properties.fallback       Fallback component.
 * @param {boolean} properties.isNetworkAdmin Whether the current context is the network admin.
 *
 * @return {JSX} MediaUploadCheck component.
 */
export const MediaUploadCheck = ( { children, fallback, isNetworkAdmin = false } ) => {
	/**
	 * Get the data from WordPress data store.
	 *
	 * In network admin context, the Media Library is not available,
	 * so the permission check result is irrelevant.
	 */
	const { hasFinishedResolution, hasUploadPermissions } = useSelect(
		( select ) => ( {
			hasFinishedResolution: select( 'core' ).hasFinishedResolution( 'canUser', [ 'create', 'media' ] ),
			hasUploadPermissions: select( 'core' ).canUser( 'create', 'media' ),
		} ),
		[]
	);

	/**
	 * In network admin context, render the fallback immediately
	 * without waiting for the permission check to resolve.
	 */
	if ( isNetworkAdmin ) {
		return <Fragment>{ fallback }</Fragment>;
	}

	/**
	 * Waiting indicator
	 */
	if ( ! hasFinishedResolution ) {
		return <WaitingIndicator message={ __( 'Checking the media upload permissions…', 'bm-custom-login' ) } />;
	}

	/**
	 * Render the component
	 */
	return <Fragment>{ hasUploadPermissions ? children : fallback }</Fragment>;
};

/**
 * Props validation
 */
MediaUploadCheck.propTypes = {
	children: PropTypes.element.isRequired,
	fallback: PropTypes.element.isRequired,
	isNetworkAdmin: PropTypes.bool,
};
