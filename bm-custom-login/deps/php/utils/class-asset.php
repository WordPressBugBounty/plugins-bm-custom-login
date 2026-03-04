<?php
/**
 * Asset class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Asset" class
 */
final class Asset {
	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Asset's slug
	 *
	 * @var string
	 */
	protected string $asset_slug;

	/**
	 * Constructor
	 *
	 * @param Container $container  Container instance.
	 * @param string    $asset_slug Asset's slug.
	 */
	public function __construct( Container $container, string $asset_slug ) {
		$this->container  = $container;
		$this->asset_slug = $asset_slug;
	}

	/**
	 * Build the asset (script, style) handle
	 *
	 * @param string $type      Asset's type; for example: "script", "editor-script", "style", etc.
	 * @param bool   $for_block Whether the handle should be generated for a block.
	 *
	 * @return string Asset's handle.
	 */
	public function build_handle( string $type, bool $for_block = false ): string {
		return sprintf( '%1$s-%2$s-%3$s', ( $for_block ? 'teydeastudio' : $this->container->get_slug() ), $this->asset_slug, $type );
	}

	/**
	 * Get the path to the asset's PHP configuration file
	 *
	 * @return string Path to the asset's PHP configuration file.
	 */
	public function get_asset_file_path(): string {
		return sprintf( '%1$s/build/%2$s/index.asset.php', $this->container->get_main_dir(), $this->asset_slug );
	}

	/**
	 * Get the path of the specific file in the assets's directory
	 *
	 * @param string $file   File to get the path for.
	 * @param bool   $direct Whether the $file argument contains a direct path to the file.
	 *
	 * @return string Path to the asset's file.
	 */
	public function get_file_path( string $file, bool $direct = true ): string {
		$path = $direct
			? $file
			: sprintf( 'build/%1$s/%2$s', $this->asset_slug, $file );

		switch ( $this->container->get_type() ) {
			case 'plugin':
				return plugin_dir_path( $this->container->get_main_file() ) . $path;
			case 'theme':
				return get_theme_file_path( $path );
		}

		return '';
	}

	/**
	 * Get the URI of the specific file in the assets's directory
	 *
	 * @param string $file   File to get the path for.
	 * @param bool   $direct Whether the $file argument contains a direct path to the file.
	 *
	 * @return string URI of the asset's file.
	 */
	public function get_file_uri( string $file, bool $direct = true ): string {
		$path = $direct
			? $file
			: sprintf( 'build/%1$s/%2$s', $this->asset_slug, $file );

		switch ( $this->container->get_type() ) {
			case 'plugin':
				return plugins_url( $path, $this->container->get_main_file() );
			case 'theme':
				return get_theme_file_uri( $path );
		}

		return '';
	}

	/**
	 * Enqueue script
	 *
	 * @param bool                 $with_translation     Whether to set script translations or not.
	 * @param ?array<string,mixed> $with_inline_data     Whether to add inline data to enqueued script or not.
	 * @param string[]             $with_additional_deps Array of additional dependencies of the enqueued stylesheet.
	 *
	 * @return void
	 */
	public function enqueue_script( bool $with_translation = true, ?array $with_inline_data = null, array $with_additional_deps = [] ): void {
		$asset_data = include $this->get_asset_file_path();

		wp_enqueue_script(
			$this->build_handle( 'script' ),
			$this->get_file_uri( 'index.js', false ),
			array_merge(
				$asset_data['dependencies'] ?? [],
				$with_additional_deps,
			),
			$asset_data['version'] ?? $this->container->get_version(),
			[
				'strategy'  => 'defer',
				'in_footer' => true,
			],
		);

		if ( true === $with_translation ) {
			wp_set_script_translations(
				$this->build_handle( 'script' ),
				$this->container->get_slug(),
				$this->get_file_path( 'languages' ),
			);
		}

		if ( null !== $with_inline_data ) {
			$this->add_inline_script(
				'script',
				$with_inline_data,
			);
		}
	}

	/**
	 * Enqueue style
	 *
	 * @param string[] $with_additional_deps Array of additional dependencies of the enqueued stylesheet.
	 *
	 * @return void
	 */
	public function enqueue_style( array $with_additional_deps = [] ): void {
		wp_enqueue_style(
			$this->build_handle( 'style' ),
			$this->get_file_uri( 'index.css', false ),
			$with_additional_deps,
			$this->container->get_version(),
		);
	}

	/**
	 * Add inline script
	 *
	 * @param string              $handle Script handle.
	 * @param array<string,mixed> $data   Inline data.
	 *
	 * @return void
	 */
	public function add_inline_script( string $handle, array $data ): void {
		$parts = [
			// Foundational object.
			'window.teydeaStudio=window.teydeaStudio||{};',

			/**
			 * Container's object.
			 */
			sprintf(
				'window.teydeaStudio.%1$s=window.teydeaStudio.%1$s||{};',
				esc_attr( Strings::to_camel_case( $this->container->get_slug() ) ),
			),

			/**
			 * Environment data.
			 */
			sprintf(
				'window.teydeaStudio.%1$s.environment=window.teydeaStudio.%1$s.environment||{type:\'%2$s\',domain:\'%3$s\',homeUrl:\'%4$s\'};',
				esc_attr( Strings::to_camel_case( $this->container->get_slug() ) ),
				esc_attr( wp_get_environment_type() ),
				esc_attr( Environment::get_domain() ?? '' ),
				esc_attr( get_home_url() ),
			),

			/**
			 * Container's data.
			 */
			sprintf(
				'window.teydeaStudio.%1$s.%2$s=window.teydeaStudio.%1$s.%2$s||{name:\'%3$s\',slug:\'%4$s\',dataPrefix:\'%5$s\',isPro:%6$s};',
				esc_attr( Strings::to_camel_case( $this->container->get_slug() ) ),
				esc_attr( $this->container->get_type() ),
				esc_attr( $this->container->get_name() ),
				esc_attr( $this->container->get_slug() ),
				esc_attr( $this->container->get_data_prefix() ),
				esc_attr( $this->container->get_is_pro() ? 'true' : 'false' ),
			),

			/**
			 * Script's data.
			 */
			sprintf(
				'window.teydeaStudio.%1$s.%2$s=%3$s;',
				esc_attr( Strings::to_camel_case( $this->container->get_slug() ) ),
				esc_attr( Strings::to_camel_case( $this->asset_slug ) ),
				wp_json_encode( $data ) ?: '{}',
			),
		];

		wp_add_inline_script(
			$this->build_handle( $handle ),
			implode( '', $parts ),
			'before',
		);
	}

	/**
	 * Add inline script specific to the block
	 *
	 * @param array<string,mixed> $data Inline data.
	 *
	 * @return void
	 */
	public function add_block_inline_script( array $data ): void {
		// Just in case; ensure we don't load custom inline scripts in the admin area.
		if ( is_admin() ) {
			return;
		}

		$parts = [
			// Foundational object.
			'window.teydeaStudio=window.teydeaStudio||{};',

			/**
			 * Container's object.
			 */
			sprintf(
				'window.teydeaStudio.%1$s=window.teydeaStudio.%1$s||{};',
				esc_attr( Strings::to_camel_case( $this->container->get_slug() ) ),
			),

			/**
			 * Script's data.
			 */
			sprintf(
				'window.teydeaStudio.%1$s.%2$s=%3$s;',
				esc_attr( Strings::to_camel_case( $this->container->get_slug() ) ),
				esc_attr( Strings::to_camel_case( $this->asset_slug ) ),
				wp_json_encode( $data ) ?: '{}',
			),
		];

		wp_add_inline_script(
			$this->build_handle( 'view-script', true ),
			implode( '', $parts ),
			'before',
		);
	}
}
