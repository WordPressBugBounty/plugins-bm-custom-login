<?php
/**
 * Settings page
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Universal_Modules
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Universal_Modules;

use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Settings_Page" class
 */
class Module_Settings_Page extends Utils\Module {
	/**
	 * Option key for the onboarding initiation
	 *
	 * @var string
	 */
	const OPTION_KEY__SHOULD_INITIATE_ONBOARDING = 'custom_login__should_initiate_onboarding';

	/**
	 * Settings page title in the admin menu context
	 *
	 * @var ?string
	 */
	protected ?string $menu_title = null;

	/**
	 * Settings page title
	 *
	 * @var ?string
	 */
	protected ?string $page_title = null;

	/**
	 * Settings page parent slug for single site installations
	 *
	 * @var string
	 */
	protected string $parent_slug = 'options-general.php';

	/**
	 * Settings page parent slug for network site installations
	 *
	 * @var string
	 */
	protected string $network_parent_slug = 'settings.php';

	/**
	 * Help & support links rendered on the settings page sidebar panel
	 *
	 * @var array<int,array{title:string,url:string}>
	 */
	protected array $help_links = [];

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Setup the values of the class properties.
		add_action( 'init', [ $this, 'setup_class_properties' ] );

		// Register settings pages.
		add_action( 'network_admin_menu', [ $this, 'register_settings_page' ] );
		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );

		// Maybe redirect user to the settings screen.
		add_action( 'admin_init', [ $this, 'maybe_redirect_after_activation' ] );

		// Enqueue required scripts and styles.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// Filter the body classes in admin settings page.
		add_filter( 'admin_body_class', [ $this, 'filter_admin_body_class' ] );

		// Filter the plugin action links.
		if ( 'plugin' === $this->container->get_type() ) {
			add_filter( sprintf( 'network_admin_plugin_action_links_%s', $this->container->get_basename() ), [ $this, 'filter_plugin_action_links' ] );
			add_filter( sprintf( 'plugin_action_links_%s', $this->container->get_basename() ), [ $this, 'filter_plugin_action_links' ] );
		}
	}

	/**
	 * Setup the values of the class properties
	 *
	 * @return void
	 */
	public function setup_class_properties(): void {
		// Define the page title if not provided.
		if ( null === $this->page_title ) {
			$this->page_title = __( 'Settings', 'bm-custom-login' );
		}

		// Define the menu title if not provided.
		if ( null === $this->menu_title ) {
			$this->menu_title = $this->page_title;
		}
	}

	/**
	 * Insert the option flag to redirect the user who has activated
	 * the container, to the settings page
	 *
	 * @return void
	 */
	public function on_container_activation(): void {
		// Skip redirect if activating container through a WP-CLI command.
		if ( Utils\Environment::is_wp_cli_request() ) {
			return;
		}

		// Don't do redirects when multiple containers are bulk activated.
		if (
			( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- just checking the admin action.
			&&
			( isset( $_REQUEST['checked'] ) && is_array( $_REQUEST['checked'] ) && count( $_REQUEST['checked'] ) > 1 ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- just checking the admin action.
		) {
			return;
		}

		$user    = new Utils\User( $this->container );
		$user_id = $user->get_user_id();

		if ( null !== $user_id ) {
			if ( $this->container->is_network_enabled() ) {
				add_network_option( get_current_network_id(), self::OPTION_KEY__SHOULD_INITIATE_ONBOARDING, $user_id );
			} else {
				add_option( self::OPTION_KEY__SHOULD_INITIATE_ONBOARDING, $user_id );
			}
		}
	}

	/**
	 * Get the settings page slug
	 *
	 * @return string The settings page slug.
	 */
	protected function get_page_slug(): string {
		return sprintf( '%s-settings-page', $this->container->get_slug() );
	}

	/**
	 * Get settings page URL
	 *
	 * @return string The settings page URL.
	 */
	protected function get_settings_page_url(): string {
		return $this->container->is_network_enabled()
			? network_admin_url(
				add_query_arg(
					[ 'page' => $this->get_page_slug() ],
					$this->network_parent_slug,
				),
			)
			: admin_url(
				add_query_arg(
					[ 'page' => $this->get_page_slug() ],
					$this->parent_slug,
				),
			);
	}

	/**
	 * Check if the page requested is a settings page
	 *
	 * @return bool Whether the page requested is a settings page or not.
	 */
	public function is_settings_page(): bool {
		$screen = new Utils\Screen( $this->container );
		return $screen->is( $this->get_page_slug(), 'settings_page' );
	}

	/**
	 * Register settings page
	 *
	 * @return void
	 */
	public function register_settings_page(): void {
		// Only register the settings page if titles are defined.
		if ( null === $this->page_title || null === $this->menu_title ) {
			return;
		}

		add_submenu_page(
			(
				$this->container->is_network_enabled()
					? $this->network_parent_slug
					: $this->parent_slug
			),
			$this->page_title,
			$this->menu_title,
			$this->container->get_managing_capability(),
			$this->get_page_slug(),
			[ $this, 'render_page' ],
		);
	}

	/**
	 * Filter the plugin action links
	 *
	 * @param array<string,string> $actions An array of plugin action links. By default this can include 'activate', 'deactivate', and 'delete'. With Multisite active this can also include 'network_active' and 'network_only' items.
	 *
	 * @return array<string,string> Updated array of plugin action links.
	 */
	public function filter_plugin_action_links( array $actions ): array {
		$user = new Utils\User( $this->container );

		if ( $user->has_managing_permissions() ) {
			$actions = array_merge(
				[
					'settings' => sprintf(
						'<a href="%1$s">%2$s</a>',
						$this->get_settings_page_url(),
						__( 'Settings', 'bm-custom-login' ),
					),
				],
				$actions,
			);
		}

		return $actions;
	}

	/**
	 * Maybe redirect user to the settings screen
	 *
	 * @return void
	 */
	public function maybe_redirect_after_activation(): void {
		// Get the ID of the user who has initiated the activation.
		$user_id = $this->container->is_network_enabled()
			? get_network_option( get_current_network_id(), self::OPTION_KEY__SHOULD_INITIATE_ONBOARDING, null )
			: get_option( self::OPTION_KEY__SHOULD_INITIATE_ONBOARDING, null );

		if ( null === $user_id ) {
			return;
		}

		$user = new Utils\User( $this->container );

		// Only proceed further if processing request from the same user.
		if ( Utils\Type::ensure_int( $user_id ) !== $user->get_user_id() ) {
			return;
		}

		// Ensure the environment dependencies match.
		if ( ! is_admin() || Utils\Environment::is_cron_request() || Utils\Environment::is_ajax_request() || Utils\Environment::is_wp_cli_request() ) {
			return;
		}

		// Delete option so the redirection only happens once.
		if ( $this->container->is_network_enabled() ) {
			delete_network_option( get_current_network_id(), self::OPTION_KEY__SHOULD_INITIATE_ONBOARDING );
		} else {
			delete_option( self::OPTION_KEY__SHOULD_INITIATE_ONBOARDING );
		}

		// No need to worry about the network URL as we skip this action for the network admins.
		wp_safe_redirect(
			$this->get_settings_page_url(),
		);

		exit;
	}

	/**
	 * Enqueue required scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		if ( ! $this->is_settings_page() ) {
			return;
		}

		$asset = new Utils\Asset( $this->container, 'settings-page' );
		$nonce = new Utils\Nonce( $this->container, 'save_settings' );

		$asset->enqueue_style(
			array_merge(
				// Required by core components.
				[ 'wp-edit-blocks', 'wp-block-editor' ],

				/**
				 * Allow other plugins and modules to pass some additional
				 * stylesheet dependencies
				 *
				 * @param string[] $dependencies Additional stylesheet dependencies.
				 */
				apply_filters( 'custom_login__settings_page_stylesheet_additional_dependencies', [] ),
			),
		);

		$asset->enqueue_script(
			true,
			/**
			 * Allow other plugins and modules to filter inline data passed
			 * to the settings page script
			 *
			 * @param array<string,mixed> $data Inline data array.
			 */
			apply_filters(
				'custom_login__settings_page_script_inline_data',
				[
					'nonce'     => $nonce->create(),
					'pageTitle' => $this->page_title,
					'helpLinks' => $this->help_links,
				],
			),
			/**
			 * Allow other plugins and modules to pass some additional
			 * script dependencies
			 *
			 * @param string[] $dependencies Additional script dependencies.
			 */
			apply_filters( 'custom_login__settings_page_script_additional_dependencies', [] ),
		);

		/**
		 * Allow other plugins and modules to load their scripts
		 * on a plugin settings page
		 */
		do_action( 'custom_login__enqueue_settings_page_scripts' );
	}

	/**
	 * Filter the body classes in admin settings page.
	 *
	 * @param string $classes Space-separated list of CSS classes.
	 */
	public function filter_admin_body_class( string $classes ): string {
		$class_name = ' teydeastudio-admin-page ';

		if ( $this->is_settings_page() && ! Utils\Strings::str_contains( $classes, $class_name ) ) {
			$classes .= $class_name;
		}

		return $classes;
	}

	/**
	 * Render page
	 *
	 * @return void
	 */
	public function render_page(): void {
		?>
		<div id="<?php echo esc_attr( $this->get_page_slug() ); ?>"></div>
		<?php
	}
}
