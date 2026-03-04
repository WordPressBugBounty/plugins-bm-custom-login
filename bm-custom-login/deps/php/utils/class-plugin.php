<?php
/**
 * Plugin controller class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Plugin" class
 */
final class Plugin extends Container {
	/**
	 * Whether the plugin is currently being activated network-wide
	 *
	 * @var bool
	 */
	protected bool $is_being_activated_network_wide = false;

	/**
	 * Container type
	 *
	 * @var string
	 */
	protected string $type = 'plugin';

	/**
	 * Get the plugin's basename
	 *
	 * @return string Plugin's basename.
	 */
	public function get_basename(): string {
		return plugin_basename( $this->get_main_file() );
	}

	/**
	 * Get the main file of the plugin
	 *
	 * @return string Main file of the plugin.
	 */
	public function get_main_file(): string {
		if ( '' === $this->main_file ) {
			// Generate path to the main file of a plugin.
			$this->main_file = sprintf(
				'%1$s/%2$s.php',
				$this->get_main_dir(),
				$this->get_slug( true ),
			);
		}

		return $this->main_file;
	}

	/**
	 * Get the URL to the plugin's directory
	 *
	 * @return string URL to the plugin's directory.
	 */
	public function get_main_url(): string {
		return plugin_dir_url( $this->get_main_file() );
	}

	/**
	 * Determine if a given plugin is network-enabled
	 *
	 * @return bool Boolean "true" if plugin is network-enabled, "false" otherwise.
	 */
	public function is_network_enabled(): bool {
		if ( false === $this->get_supports_network() ) {
			return false;
		}

		if ( ! is_multisite() ) {
			return false;
		}

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active_for_network( $this->get_basename() ) ) {
			return true;
		}

		// Is plugin currently being activated network-wide?
		if ( doing_action( sprintf( 'activate_%s', $this->get_basename() ) ) && $this->is_being_activated_network_wide ) {
			return true;
		}

		return false;
	}

	/**
	 * Note whether the plugin is currently being activated network-wide
	 *
	 * @param bool $network_wide Whether the plugin is being activated for all sites in the network or just the current site.
	 *
	 * @return void
	 */
	public function note_network_wide_activation_status( bool $network_wide ): void {
		$this->is_being_activated_network_wide = $network_wide;
	}
}
