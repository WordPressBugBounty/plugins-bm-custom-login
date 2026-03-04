/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { Panel, PanelBody } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { applyFilters } from '@wordpress/hooks';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Import styles
 */
import './styles.scss';

/**
 * SettingsSidebar component
 *
 * @param {Object} properties          Component properties object.
 * @param {Object} properties.product  Product's data object.
 * @param {Object} properties.settings Settings object.
 *
 * @return {JSX} Settings component.
 */
export const SettingsSidebar = ( { product, settings } ) => {
	// Destructure the product object.
	const { key: productKey, type: productType } = product;

	// Collect the necessary data.
	const { helpLinks } = window.teydeaStudio[ productKey ].settingsPage;
	const { slug } = window.teydeaStudio[ productKey ][ productType ];

	/**
	 * Render the component
	 */
	return (
		<div className="tsc-settings-sidebar">
			<Panel>
				{
					/**
					 * Render help links, if provided
					 */
					0 < helpLinks.length && (
						<PanelBody title={ __( 'Help & support', 'bm-custom-login' ) } className="tsc-settings-sidebar__panel">
							<ul>
								{ helpLinks.map( ( { url, title }, index ) => (
									<li key={ index }>
										<a href={ url } target="_blank" rel="noreferrer noopener">
											{ title }
										</a>
									</li>
								) ) }
							</ul>
						</PanelBody>
					)
				}
				{
					/**
					 * Slot for the "upsell" panel
					 *
					 * @param {JSX} panel The "upsell" panel.
					 */
					applyFilters( 'custom_login__upsell_panel', <Fragment /> )
				}
				{
					/**
					 * Slot for the "promoted plugins" panel
					 *
					 * @param {JSX} panel The "promoted plugins" panel.
					 */
					applyFilters( 'custom_login__promoted_plugins_panel', <Fragment /> )
				}
				<PanelBody className="tsc-settings-sidebar__panel" initialOpen={ false } title={ __( 'Write a review', 'bm-custom-login' ) }>
					<p>
						{ sprintf(
							// Translators: %s - either "plugin" or "theme".
							__(
								'If you like this %s, share it with your network and write a review on WordPress.org to help others find it. Thank you!',
								'bm-custom-login'
							),
							productType
						) }
					</p>
					<a
						className="components-button is-secondary is-compact"
						href={ `https://wordpress.org/support/${ productType }/${ slug }/reviews/#new-post` }
						rel="noopener noreferrer"
						target="_blank"
					>
						{ __( 'Write a review', 'bm-custom-login' ) }
					</a>
				</PanelBody>
				<PanelBody className="tsc-settings-sidebar__panel" initialOpen={ false } title={ __( 'Share your feedback', 'bm-custom-login' ) }>
					<p>
						{ __(
							"We're eager to hear your feedback, feature requests, suggestions for improvements etc; we're waiting for a message from you!",
							'bm-custom-login'
						) }
					</p>
					<a className="components-button is-secondary is-compact" href="https://teydeastudio.com/contact/" rel="noopener noreferrer" target="_blank">
						{ __( 'Contact us', 'bm-custom-login' ) }
					</a>
				</PanelBody>
			</Panel>
			{
				/**
				 * Slot for the "after sidebar" panel
				 *
				 * @param {JSX}    panel    The "after sidebar" panel.
				 * @param {Object} settings Settings object.
				 */
				applyFilters( 'custom_login__after_sidebar_panel', <Fragment />, settings )
			}
		</div>
	);
};

/**
 * Props validation
 */
SettingsSidebar.propTypes = {
	product: PropTypes.shape( {
		key: PropTypes.string.isRequired,
		type: PropTypes.oneOf( [ 'plugin', 'theme' ] ),
	} ).isRequired,
	settings: PropTypes.object.isRequired,
};
