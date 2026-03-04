/**
 * WordPress dependencies
 */
import { isURL as coreIsURL, getProtocol, getAuthority, isValidAuthority, isValidProtocol } from '@wordpress/url';

/**
 * Verify whether a given string is an URL
 *
 * @param {string} string The input string.
 *
 * @return {boolean} Whether the string given is an URL.
 */
export const isURL = ( string ) => {
	const protocol = getProtocol( string );

	// Verify the protocol.
	if ( ! isValidProtocol( protocol ) ) {
		return false;
	}

	const authority = getAuthority( string );

	// Verify the authority.
	if ( ! isValidAuthority( authority ) || authority.startsWith( '.' ) || authority.endsWith( '.' ) ) {
		return false;
	}

	// Only allow the "http" and "https" protocols.
	if ( ! string.startsWith( 'http://' ) && ! string.startsWith( 'https://' ) ) {
		return false;
	}

	// Use default WordPress validator.
	return coreIsURL( string );
};
