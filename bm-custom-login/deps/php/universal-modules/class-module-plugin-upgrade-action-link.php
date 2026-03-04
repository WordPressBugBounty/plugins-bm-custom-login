<?php
/**
 * Add the plugin upgrade action link
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Universal_Modules
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Universal_Modules;

use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Plugin_Upgrade_Action_Link" class
 */
class Module_Plugin_Upgrade_Action_Link extends Utils\Module {
	/**
	 * Plugin upgrade link
	 *
	 * @var string
	 */
	protected string $upgrade_link = '';

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Filter the plugin action links.
		add_filter( sprintf( 'network_admin_plugin_action_links_%s', $this->container->get_basename() ), [ $this, 'filter_plugin_action_links' ] );
		add_filter( sprintf( 'plugin_action_links_%s', $this->container->get_basename() ), [ $this, 'filter_plugin_action_links' ] );
	}

	/**
	 * Filter the plugin action links
	 *
	 * @param array<string,string> $actions An array of plugin action links. By default this can include 'activate', 'deactivate', and 'delete'. With Multisite active this can also include 'network_active' and 'network_only' items.
	 *
	 * @return array<string,string> Updated array of plugin action links.
	 */
	public function filter_plugin_action_links( array $actions ): array {
		if ( '' !== $this->upgrade_link ) {
			$actions = array_merge(
				[
					'upgrade' => sprintf(
						'<a href="%1$s" target="_blank" rel="noreferrer noopener" style="font-weight:bold">%2$s</a>',
						$this->upgrade_link,
						__( 'Upgrade', 'bm-custom-login' ),
					),
				],
				$actions,
			);
		}

		return $actions;
	}
}
