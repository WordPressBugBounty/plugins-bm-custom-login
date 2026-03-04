/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { ColorIndicator, ColorPalette, Dropdown, GradientPicker, TabPanel } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Import styles
 */
import './styles.scss';

/**
 * ColorControl component
 *
 * @param {Object}   properties                  Component properties object.
 * @param {Array}    properties.colorPalettes    Predefined color palettes.
 * @param {Array}    properties.gradientPalettes Preconfigured gradients array.
 * @param {string}   properties.label            Label.
 * @param {Function} properties.onChange         Function callback to trigger on value change.
 * @param {string}   properties.value            Field's value.
 * @param {boolean}  properties.withAlpha        Whether the alpha choice should be allowed.
 * @param {boolean}  properties.withColor        Whether the color selector should be shown.
 * @param {boolean}  properties.withGradient     Whether the gradient selector should be shown.
 *
 * @return {JSX} ColorControl component.
 */
export const ColorControl = ( { colorPalettes, gradientPalettes, label, onChange, value, withAlpha = false, withColor = false, withGradient = false } ) => {
	// Recognize whether a given value represents gradient.
	const isGradient = 'string' === typeof value && value.includes( 'gradient' );

	/**
	 * Tabs configuration
	 */
	const tabsConfig = [];

	if ( withColor ) {
		tabsConfig.push( {
			name: 'color',
			title: __( 'Color', 'bm-custom-login' ),
			component: <ColorPalette colors={ colorPalettes } value={ isGradient ? undefined : value } enableAlpha={ withAlpha } onChange={ onChange } />,
		} );
	}

	if ( withGradient ) {
		tabsConfig.push( {
			name: 'gradient',
			title: __( 'Gradient', 'bm-custom-login' ),
			component: (
				<GradientPicker value={ isGradient ? value : undefined } onChange={ onChange } enableAlpha={ withAlpha } gradients={ gradientPalettes ?? [] } />
			),
		} );
	}

	/**
	 * Render the component
	 */
	return (
		<Dropdown
			className="tsc-color-control"
			popoverProps={ {
				className: 'tsc-color-control__popover',
			} }
			/**
			 * Render the color control toggle button
			 *
			 * @param {Object}   properties          Properties object.
			 * @param {boolean}  properties.isOpen   Whether the dropdown is currently open.
			 * @param {Function} properties.onToggle Callback function to call on toggle button click.
			 *
			 * @return {JSX} Toggle button component.
			 */
			renderToggle={ ( { isOpen, onToggle } ) => {
				const classNames = [ 'tsc-color-control__button' ];

				if ( isOpen ) {
					classNames.push( 'tsc-color-control__button--active' );
				}

				return (
					<button aria-expanded={ isOpen } className={ classNames.join( ' ' ) } onClick={ onToggle }>
						<ColorIndicator colorValue={ value } />
						{ label }
					</button>
				);
			} }
			/**
			 * Render the dropdown inner component
			 *
			 * @return {JSX} Dropdown inner component.
			 */
			renderContent={ () => (
				<TabPanel __nextHasNoMarginBottom __next40pxDefaultSize tabs={ tabsConfig }>
					{
						/**
						 * Render single tab
						 *
						 * @param {Object} tab Tab object.
						 *
						 * @return {JSX} Tab component.
						 */
						( tab ) => <Fragment key={ tab.name }>{ tab.component }</Fragment>
					}
				</TabPanel>
			) }
		/>
	);
};

/**
 * Props validation
 */
ColorControl.propTypes = {
	colorPalettes: PropTypes.array.isRequired,
	gradientPalettes: PropTypes.array,
	label: PropTypes.string.isRequired,
	onChange: PropTypes.func.isRequired,
	value: PropTypes.string.isRequired,
	withAlpha: PropTypes.bool.isRequired,
	withColor: PropTypes.bool.isRequired,
	withGradient: PropTypes.bool.isRequired,
};
