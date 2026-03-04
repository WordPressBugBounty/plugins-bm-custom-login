/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { useMedia } from '@teydeastudio/utils/src/use-media.js';

/**
 * WordPress dependencies
 */
import { MediaUpload } from '@wordpress/block-editor';
import { Button, FocalPointPicker, Notice, SelectControl } from '@wordpress/components';
import { Fragment, useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FieldsGroup } from '../fields-group/index.js';
import { MediaUploadCheck } from '../media-upload-check/index.js';
import { VisualEffectControl } from '../visual-effect-control/index.js';

/**
 * Import styles
 */
import './styles.scss';

/**
 * Recognize the attachment type based on a given MIME type
 *
 * @param {string} mimeType MIME type of the attachment.
 *
 * @return {string} Attachment type (like "image", "video", etc.)
 */
const recognizeAttachmentType = ( mimeType ) => mimeType.substring( 0, mimeType.indexOf( '/' ) );

/**
 * Normalize the media object structure
 *
 * Data provided by the MediaUpload "onSelect" method, and data provided
 * by the useMedia hook returns media objects with a different shapes.
 *
 * @param {Object} media Media object to normalize.
 *
 * @return {Object} Normalized media object.
 */
const normalizeMedia = ( media = undefined ) => {
	const size = 'medium';
	const result = {
		id: 0,
		alt: '',
		src: '',
		width: 300,
		height: 300,
		type: undefined,
	};

	if ( 'undefined' !== typeof media ) {
		result.id = media.id;
		result.type = media.type;

		if ( 'undefined' !== typeof media?.alt_text ) {
			result.alt = media.alt_text;
		} else if ( 'undefined' !== typeof media?.alt ) {
			result.alt = media.alt;
		}

		if ( 'undefined' !== typeof media?.mime_type ) {
			result.type = recognizeAttachmentType( media.mime_type );
		} else if ( 'undefined' !== typeof media?.mime ) {
			result.type = recognizeAttachmentType( media.mime );
		}

		if ( 'undefined' !== typeof media?.media_details?.sizes?.[ size ] ) {
			result.src = media.media_details.sizes[ size ].source_url;
			result.width = media.media_details.sizes[ size ].width;
			result.height = media.media_details.sizes[ size ].height;
		} else if ( 'undefined' !== typeof media?.media_details ) {
			result.src = media?.source_url ?? '';
			result.width = media.media_details.width;
			result.height = media.media_details.height;
		} else if ( 'undefined' !== typeof media?.sizes?.[ size ] ) {
			result.src = media.sizes[ size ].url;
			result.width = media.sizes[ size ].width;
			result.height = media.sizes[ size ].height;
		} else if ( 'video' === result.type ) {
			result.src = media?.url ?? result.src;
			result.width = media?.width ?? result.width;
			result.height = media?.height ?? result.height;
		}
	}

	return result;
};

/**
 * MediaControl component
 *
 * @param {Object}   properties                         Component properties object.
 * @param {Array}    properties.allowedTypes            Array with the types of the media to upload/select from the media library.
 * @param {boolean}  properties.isNetworkAdmin          Whether the current context is the network admin.
 * @param {string}   properties.label                   Label.
 * @param {Function} properties.onChange                Function callback to trigger on value change.
 * @param {Object}   properties.values                  Field's values object.
 * @param {boolean}  properties.withFocalPointPicker    Whether to render the focal point picker fields.
 * @param {boolean}  properties.withSizeRepeatSelector  Whether to render the position selector.
 * @param {boolean}  properties.withVisualEffectControl Whether to render the visual effects controls.
 *
 * @return {JSX} MediaControl component.
 */
export const MediaControl = ( {
	allowedTypes,
	isNetworkAdmin = false,
	label,
	onChange,
	values,
	withFocalPointPicker = false,
	withSizeRepeatSelector = false,
	withVisualEffectControl = false,
} ) => {
	// Destructure the values object.
	const {
		filterBlur,
		filterBrightness,
		filterContrast,
		filterGrayscale,
		filterHueRotation,
		filterInvert,
		filterOpacity,
		filterSaturate,
		filterSepia,
		focalPointX,
		focalPointY,
		mediaId,
		sizeRepeat,
	} = values;

	// State management.
	const [ media, setMedia ] = useState();
	const currentMedia = useMedia( mediaId );

	// Whether media ID is not empty.
	const hasMediaId = 'undefined' !== typeof media?.id && 0 !== media.id;

	/**
	 * Update the focal point values
	 *
	 * @param {Object} focalPoint   Focal points object.
	 * @param {number} focalPoint.x Value of the x focal point.
	 * @param {number} focalPoint.y Value of the y focal point.
	 *
	 * @return {void}
	 */
	const setFocalPoint = ( { x, y } ) => {
		onChange( {
			...values,
			focalPointX: x,
			focalPointY: y,
		} );
	};

	/**
	 * Get the media object if selection
	 * is provided on initial render.
	 */
	useEffect( () => {
		if ( 'undefined' !== typeof currentMedia?.media && 'undefined' === typeof media ) {
			setMedia( normalizeMedia( currentMedia.media ) );
		}
	}, [ currentMedia ] ); // eslint-disable-line react-hooks/exhaustive-deps

	/**
	 * Update the selected media ID
	 */
	useEffect( () => {
		if ( 'undefined' !== typeof media && mediaId !== media?.id ) {
			onChange( {
				...values,
				mediaId: hasMediaId ? media.id : 0,
			} );
		}
	}, [ media, mediaId ] ); // eslint-disable-line react-hooks/exhaustive-deps

	/**
	 * Render the component
	 */
	return (
		<MediaUploadCheck
			isNetworkAdmin={ isNetworkAdmin }
			fallback={
				<Notice __nextHasNoMarginBottom __next40pxDefaultSize isDismissible={ false } status={ isNetworkAdmin ? 'info' : 'warning' }>
					{ isNetworkAdmin
						? __(
								"The Media Library is not available in the Network Admin dashboard. To use custom images, configure image settings from an individual site's dashboard.",
								'bm-custom-login'
						  )
						: __( 'You have no permissions to upload to the Media Library!', 'bm-custom-login' ) }
				</Notice>
			}
		>
			<FieldsGroup className="tsc-media-control" label={ label } withBaseControl>
				<MediaUpload
					/**
					 * Update the field's value
					 *
					 * @param {Object} updatedMedia Selected media object.
					 *
					 * @return {void}
					 */
					onSelect={ ( updatedMedia ) => {
						setMedia( normalizeMedia( updatedMedia ) );
					} }
					allowedTypes={ allowedTypes }
					value={ media?.id }
					/**
					 * Render the component
					 *
					 * @param {Object}   properties      Component properties.
					 * @param {Function} properties.open Callback function to open the Media Library
					 *
					 * @return {JSX} Component.
					 */
					render={ ( { open } ) => (
						<Fragment>
							{ hasMediaId &&
								( withFocalPointPicker ? (
									<FocalPointPicker
										__nextHasNoMarginBottom
										url={ media.src }
										value={ {
											x: focalPointX,
											y: focalPointY,
										} }
										onDragStart={ setFocalPoint }
										onDrag={ setFocalPoint }
										onChange={ setFocalPoint }
									/>
								) : (
									<button className="tsc-media-control__thumbnail" onClick={ open } type="button">
										<img src={ media.src } alt={ media.alt } width={ media.width } height={ media.height } loading="lazy" />
									</button>
								) ) }
							<FieldsGroup withReducedGap>
								<Button __nextHasNoMarginBottom __next40pxDefaultSize onClick={ open } variant="tertiary">
									{ hasMediaId
										? sprintf(
												// Translators: %s - label.
												__( 'Replace %s', 'bm-custom-login' ),
												label
										  )
										: sprintf(
												// Translators: %s - label.
												__( 'Set %s', 'bm-custom-login' ),
												label
										  ) }
								</Button>
								{ hasMediaId && (
									<Button
										__nextHasNoMarginBottom
										__next40pxDefaultSize
										onClick={ () => {
											setMedia( normalizeMedia() );
										} }
										variant="tertiary"
									>
										{ sprintf(
											// Translators: %s - label.
											__( 'Remove %s', 'bm-custom-login' ),
											label
										) }
									</Button>
								) }
							</FieldsGroup>
						</Fragment>
					) }
				/>
				{ withSizeRepeatSelector && hasMediaId && 'image' === media?.type && (
					<SelectControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Background image size & repeat', 'bm-custom-login' ) }
						value={ sizeRepeat }
						options={ [
							{
								value: 'size-auto--repeat',
								label: __( 'Size: auto; Repeat: yes', 'bm-custom-login' ),
							},
							{
								value: 'size-auto--no-repeat',
								label: __( 'Size: auto; Repeat: no', 'bm-custom-login' ),
							},
							{
								value: 'size-auto--repeat-x',
								label: __( 'Size: auto; Repeat: X', 'bm-custom-login' ),
							},
							{
								value: 'size-auto--repeat-y',
								label: __( 'Size: auto; Repeat: Y', 'bm-custom-login' ),
							},
							{
								value: 'size-auto--repeat-round',
								label: __( 'Size: auto; Repeat: round', 'bm-custom-login' ),
							},
							{
								value: 'size-auto--repeat-space',
								label: __( 'Size: auto; Repeat: space', 'bm-custom-login' ),
							},
							{
								value: 'size-contain--repeat',
								label: __( 'Size: contain; Repeat: yes', 'bm-custom-login' ),
							},
							{
								value: 'size-contain--no-repeat',
								label: __( 'Size: contain; Repeat: no', 'bm-custom-login' ),
							},
							{
								value: 'size-contain--repeat-x',
								label: __( 'Size: contain; Repeat: X', 'bm-custom-login' ),
							},
							{
								value: 'size-contain--repeat-y',
								label: __( 'Size: contain; Repeat: Y', 'bm-custom-login' ),
							},
							{
								value: 'size-cover',
								label: __( 'Size: cover', 'bm-custom-login' ),
							},
						] }
						/**
						 * Update the value
						 *
						 * @param {string} updatedValue Updated value.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValue ) => {
							onChange( {
								...values,
								sizeRepeat: updatedValue,
							} );
						} }
					/>
				) }
				{ withVisualEffectControl && hasMediaId && (
					<VisualEffectControl
						/**
						 * Update the values
						 *
						 * @param {Object} updatedValues Updated values.
						 *
						 * @return {void}
						 */
						onChange={ ( updatedValues ) => {
							onChange( {
								...values,
								...updatedValues,
							} );
						} }
						values={ {
							filterBlur,
							filterBrightness,
							filterContrast,
							filterGrayscale,
							filterHueRotation,
							filterInvert,
							filterOpacity,
							filterSaturate,
							filterSepia,
						} }
					/>
				) }
			</FieldsGroup>
		</MediaUploadCheck>
	);
};

/**
 * Props validation
 */
MediaControl.propTypes = {
	allowedTypes: PropTypes.array.isRequired,
	isNetworkAdmin: PropTypes.bool,
	label: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
	values: PropTypes.shape( {
		filterBlur: PropTypes.number,
		filterBrightness: PropTypes.number,
		filterContrast: PropTypes.number,
		filterGrayscale: PropTypes.number,
		filterHueRotation: PropTypes.number,
		filterInvert: PropTypes.number,
		filterOpacity: PropTypes.number,
		filterSaturate: PropTypes.number,
		filterSepia: PropTypes.number,
		focalPointX: PropTypes.number.isRequired,
		focalPointY: PropTypes.number.isRequired,
		mediaId: PropTypes.string.isRequired,
		sizeRepeat: PropTypes.string,
	} ).isRequired,
	withFocalPointPicker: PropTypes.bool,
	withSizeRepeatSelector: PropTypes.bool,
	withVisualEffectControl: PropTypes.bool,
};
