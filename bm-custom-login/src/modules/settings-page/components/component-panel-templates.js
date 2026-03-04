/**
 * WordPress dependencies
 */
import { Button, Notice, PanelBody } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * PanelTemplates component
 *
 * @return {JSX} PanelTemplates component.
 */
export const PanelTemplates = () => {
	// Get the plugin directory URL and network admin context.
	const { isNetworkAdmin, mainUrl } = window?.teydeaStudio?.bmCustomLogin?.settingsPage?.context || { isNetworkAdmin: false, mainUrl: '' };

	if ( '' === mainUrl ) {
		return null;
	}

	// Configure available templates.
	const templates = [
		{
			name: __( 'Core Default', 'bm-custom-login' ),
			previewFile: 'template-001.png',
		},
		{
			name: __( 'Polished Core Default', 'bm-custom-login' ),
			previewFile: 'template-002.png',
		},
		{
			name: __( 'Clean & Minimal', 'bm-custom-login' ),
			previewFile: 'template-003.png',
		},
		{
			name: __( 'Clean & Minimal - Dark', 'bm-custom-login' ),
			previewFile: 'template-004.png',
		},
		{
			name: __( 'Clean & Minimal with Animated Gradient', 'bm-custom-login' ),
			previewFile: 'template-005.png',
		},
		{
			name: __( 'Purple Nature', 'bm-custom-login' ),
			previewFile: 'template-006.png',
		},
	];

	/**
	 * Return the component
	 *
	 * @return {JSX} PanelTemplates component.
	 */
	return (
		<PanelBody initialOpen={ false } title={ __( 'Templates (Quick Start)', 'bm-custom-login' ) }>
			<p>{ __( 'Apply a preset style instantly. All settings remain editable.', 'bm-custom-login' ) }</p>
			{ isNetworkAdmin && (
				<Notice __nextHasNoMarginBottom __next40pxDefaultSize isDismissible={ false } status="info">
					{ __(
						'Image-related features are limited in the Network Admin because the Media Library is not available in this context.',
						'bm-custom-login'
					) }
				</Notice>
			) }
			<div className="bm-custom-login-settings-page__templates">
				{ templates.map( ( template, index ) => {
					const { name, previewFile } = template;

					return (
						<div key={ index } className="bm-custom-login-settings-page__template">
							<img src={ `${ mainUrl }src/modules/settings-page/images/${ previewFile }` } alt={ name } loading="lazy" />
							<div className="bm-custom-login-settings-page__template-info">
								<h3>{ name }</h3>
								<div className="bm-custom-login-settings-page__template-actions">
									<Button
										__nextHasNoMarginBottom
										__next40pxDefaultSize
										variant="primary"
										size="compact"
										onClick={ () => {} }
										disabled={ true }
									>
										{ __( 'Use this template', 'bm-custom-login' ) }
									</Button>
								</div>
							</div>
						</div>
					);
				} ) }
				<div className="bm-custom-login-settings-page__templates-overlay">
					<a
						href="https://wpcustomlogin.com/pricing/?utm_source=WP+Custom+Login"
						target="_blank"
						rel="noreferrer noopener"
						className="components-button is-primary is-compact"
					>
						{ sprintf( '%s →', __( 'Unlock with PRO', 'bm-custom-login' ) ) }
					</a>
				</div>
			</div>
		</PanelBody>
	);
};

/**
 * Props validation
 */
PanelTemplates.propTypes = {};
