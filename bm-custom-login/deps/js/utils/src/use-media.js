/**
 * WordPress dependencies
 */
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';

/**
 * Loads the media information from the server
 *
 * @param {number} id The media ID.
 *
 * @return {Object} Media data object.
 */
export const useMedia = ( id ) =>
	useSelect(
		( select ) => {
			if ( 'undefined' === typeof id ) {
				return {
					media: undefined,
					isResolvingMedia: false,
					hasResolvedMedia: false,
				};
			}

			const { getEntityRecord, isResolving, hasFinishedResolution } = select( coreStore );
			const entityParameters = [ 'postType', 'attachment', id, { context: 'view' } ];

			return {
				media: getEntityRecord( ...entityParameters ),
				isResolvingMedia: isResolving( 'getEntityRecord', entityParameters ),
				hasResolvedMedia: hasFinishedResolution( 'getEntityRecord', entityParameters ),
			};
		},
		[ id ]
	);
