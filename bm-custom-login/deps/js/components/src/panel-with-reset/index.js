/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { rotateLeft } from '@wordpress/icons';

/**
 * Import styles
 */
import './styles.scss';

/**
 * PanelWithReset component
 *
 * @param {Object}   properties             Component properties object.
 * @param {JSX}      properties.children    Children components (panel itself).
 * @param {Object}   properties.settings    Plugin settings.
 * @param {Function} properties.setSettings Function (callback) used to update the settings.
 * @param {string}   properties.slug        Slug identifier for the panel.
 *
 * @return {JSX} PanelWithReset component.
 */
export const PanelWithReset = ( { children, settings, setSettings, slug } ) => {
	let differs = false;

	if ( JSON.stringify( settings.data[ slug ] ) !== JSON.stringify( settings.defaults[ slug ] ) ) {
		differs = true;
	}

	if ( ! differs ) {
		const possibleAssociatedKeys = [ 'List', 'SliderA', 'SliderB' ];

		for ( const key of possibleAssociatedKeys ) {
			// Check if there is an associated key with this panel.
			if ( 'undefined' !== typeof settings.data[ `${ slug }${ key }` ] ) {
				const isDifferent = JSON.stringify( settings.data[ `${ slug }${ key }` ] ) !== JSON.stringify( settings.defaults[ `${ slug }${ key }` ] );
				const isSameEmpty =
					'{}' === JSON.stringify( settings.data[ `${ slug }${ key }` ] ) && '[]' === JSON.stringify( settings.defaults[ `${ slug }${ key }` ] );

				if ( isDifferent && ! isSameEmpty ) {
					differs = true;
				}
			}
		}
	}

	return (
		<div className="tsc-panel-with-reset">
			<Button
				className="tsc-panel-with-reset__button"
				onClick={ () => {
					const updatedData = {
						...settings.data,
						[ slug ]: settings.defaults[ slug ],
					};

					// Reset the associated list, if applicable.
					if ( 'undefined' !== typeof settings.data[ `${ slug }List` ] ) {
						updatedData[ `${ slug }List` ] = settings.defaults[ `${ slug }List` ];
					}

					// Reset the associated slider A, if applicable.
					if ( 'undefined' !== typeof settings.data[ `${ slug }SliderA` ] ) {
						updatedData[ `${ slug }SliderA` ] = settings.defaults[ `${ slug }SliderA` ];
					}

					// Reset the associated slider B, if applicable.
					if ( 'undefined' !== typeof settings.data[ `${ slug }SliderB` ] ) {
						updatedData[ `${ slug }SliderB` ] = settings.defaults[ `${ slug }SliderB` ];
					}

					setSettings( {
						...settings,
						data: updatedData,
					} );
				} }
				icon={ rotateLeft }
				disabled={ ! differs }
				label={ __( 'Reset to defaults', 'bm-custom-login' ) }
				showTooltip={ true }
			/>
			{ children }
		</div>
	);
};

/**
 * Props validation
 */
PanelWithReset.propTypes = {
	children: PropTypes.element.isRequired,
	settings: PropTypes.object.isRequired,
	setSettings: PropTypes.func.isRequired,
	slug: PropTypes.string.isRequired,
};
