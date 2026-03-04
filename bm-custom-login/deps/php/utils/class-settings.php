<?php
/**
 * Plugin settings
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin settings class
 *
 * @phpstan-import-type Type_Dynamic_Fields_Config from Validatable_Fields\Dynamic_Fields_Group
 * @phpstan-import-type Type_Fields_Config from Validatable_Fields\Fields_Group
 *
 * @phpstan-type Type_Settings_Fields_Config array<string,array{type:'dynamic',config:Type_Dynamic_Fields_Config}|array{type:'static',config:Type_Fields_Config}>
 */
class Settings {
	use Validatable_Fields\Validatable;

	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Settings fields configuration array
	 *
	 * @var Type_Settings_Fields_Config
	 */
	protected array $fields_config;

	/**
	 * Option key to use for the settings storage
	 *
	 * @var string
	 */
	protected string $option_key;

	/**
	 * Fields groups
	 *
	 * @var Validatable_Fields\Fields_Group[]
	 */
	protected array $fields_groups = [];

	/**
	 * Flag: is strict mode
	 * - when set to "true", any errors returned by the fields are thrown
	 * - when set to "false", any errors returned by the fields are ignored, and field values are replaced with defaults
	 *
	 * @var bool
	 */
	protected bool $is_strict_mode = true;

	/**
	 * Construct the object
	 *
	 * @param Container                          $container     Container instance.
	 * @param Type_Settings_Fields_Config        $fields_config Settings fields configuration array, if not provided, defaults to empty array.
	 * @param ?array<string,array<string,mixed>> $config        Configuration data; if not provided, data will be loaded from the database.
	 */
	public function __construct( Container $container, array $fields_config = [], ?array $config = null ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->container     = $container;
		$this->fields_config = $fields_config;

		// Set the option key.
		$this->option_key = sprintf( '%s__settings', $this->container->get_data_prefix() );

		/**
		 * Get the configuration data from the database
		 * if not provided
		 */
		if ( null === $config ) {
			$this->is_strict_mode = false;
			$config               = $this->load_from_db();
		}

		/**
		 * Construct the fields groups
		 */
		foreach ( $this->fields_config as $field_config_key => $field_config ) {
			$group = Validatable_Fields\Dynamic_Fields_Group::TYPE === $field_config['type']
				? new Validatable_Fields\Dynamic_Fields_Group( $field_config_key, $field_config['config'] )
				: new Validatable_Fields\Fields_Group( $field_config_key, $field_config['config'] );

			$values_to_load = isset( $config[ $group->get_key_camel_case() ] ) && is_array( $config[ $group->get_key_camel_case() ] )
				? $config[ $group->get_key_camel_case() ]
				: [];

			$values = $group->load_values( $values_to_load, ! $this->is_strict_mode );

			if ( $values instanceof WP_Error ) {
				$this->add_validation_error( $values );
			}

			$this->fields_groups[ $field_config_key ] = $group;
		}
	}

	/**
	 * Get a single fields group
	 *
	 * @param string $key The fields group key.
	 *
	 * @return ?Validatable_Fields\Fields_Group Instance of the Fields Group if found by key, null otherwise.
	 */
	public function get_fields_group( string $key ): ?Validatable_Fields\Fields_Group {
		return $this->fields_groups[ $key ] ?? null;
	}

	/**
	 * Structure the settings data in a form that can be saved in the
	 * database or passed through the REST API
	 *
	 * - for JS processing and database operation, we use camelCase keys
	 * - for PHP processing, we use snake_case keys
	 *
	 * @return ?array<string,mixed> Array of the normalized settings data, or null in case of unresolved validation errors.
	 */
	public function get_normalized_data(): ?array {
		if ( $this->has_validation_errors() ) {
			return null;
		}

		$data = [];

		foreach ( array_keys( $this->fields_config ) as $field_config_key ) {
			$group                                = $this->fields_groups[ $field_config_key ];
			$data[ $group->get_key_camel_case() ] = $group->get_value( true );
		}

		return $data;
	}

	/**
	 * Get the templates of all dynamic field groups
	 *
	 * @return array<string,array<string,array<string,mixed>>> Templates of all dynamic field groups.
	 */
	public function get_templates(): array {
		$templates = [];

		foreach ( $this->fields_config as $field_config_key => $field_config ) {
			if ( Validatable_Fields\Dynamic_Fields_Group::TYPE !== $field_config['type'] ) {
				continue;
			}

			$group                                     = new Validatable_Fields\Dynamic_Fields_Group( $field_config_key, $field_config['config'] );
			$templates[ $group->get_key_camel_case() ] = $group->get_templates();
		}

		return $templates;
	}

	/**
	 * Get the option key
	 *
	 * @return string Option key.
	 */
	public function get_option_key(): string {
		return $this->option_key;
	}

	/**
	 * Load settings from database
	 *
	 * @return array<string,mixed> Empty array if no settings found in the database, array of settings otherwise.
	 */
	protected function load_from_db(): array {
		/** @var false|array<string,mixed> $data */
		$data = $this->container->is_network_enabled()
			? get_network_option( get_current_network_id(), $this->get_option_key(), $this->get_fallback() )
			: get_option( $this->get_option_key(), $this->get_fallback() );

		$data = is_array( $data ) ? $data : $this->get_fallback();

		/**
		 * Allow other plugins and modules to filter out
		 * the settings loaded from the database
		 *
		 * @param array<string,mixed> $data Loaded settings data.
		 */
		return apply_filters( 'custom_login__settings_loaded', $data );
	}

	/**
	 * Get default settings
	 *
	 * @return array<string,mixed> Array of default settings.
	 */
	public function get_defaults(): array {
		$data = [];

		/**
		 * Construct the fields groups
		 */
		foreach ( $this->fields_config as $field_config_key => $field_config ) {
			$group = Validatable_Fields\Dynamic_Fields_Group::TYPE === $field_config['type']
				? new Validatable_Fields\Dynamic_Fields_Group( $field_config_key, $field_config['config'] )
				: new Validatable_Fields\Fields_Group( $field_config_key, $field_config['config'] );

			$group->load_values( [], true );
			$data[ $group->get_key_camel_case() ] = $group->get_value( true );
		}

		return $data;
	}

	/**
	 * Get fallback settings
	 *
	 * Used only in case if settings were not loaded
	 * from the database:
	 * - not yet configured by the user,
	 * - typically right after the plugin installation.
	 *
	 * @return array{}|array<string,array<string,mixed>> Empty array if no default settings defined, array of default settings otherwise.
	 */
	protected function get_fallback(): array {
		return [];
	}

	/**
	 * Save settings into the database
	 *
	 * @return true|WP_Error Boolean "true" on success, instance of WP_Error on failure.
	 */
	public function save() {
		// Verify the user permissions to save the changes.
		if ( false === ( new User( $this->container ) )->has_managing_permissions() ) {
			return new WP_Error(
				'insufficient_permissions',
				__( 'Sorry, you are not allowed to do that.', 'bm-custom-login' ),
			);
		}

		// Ensure there's no validation errors.
		if ( $this->has_validation_errors() ) {
			return new WP_Error(
				'validation_errors_found',
				__( 'Can\'t save settings into the database; resolve validation errors first.', 'bm-custom-login' ),
			);
		}

		$data = $this->get_normalized_data();

		if ( null === $data ) {
			return new WP_Error(
				'validation_errors_found',
				__( 'Can\'t save settings into the database; resolve validation errors first.', 'bm-custom-login' ),
			);
		}

		// Compare with the old data to only proceed if there are any updates.
		$old_data = $this->load_from_db();

		if ( wp_json_encode( $data ) !== wp_json_encode( $old_data ) ) {
			if ( $this->container->is_network_enabled() ) {
				update_network_option( get_current_network_id(), $this->get_option_key(), $data );
			} else {
				update_option( $this->get_option_key(), $data, false );
			}

			/**
			 * Let the other plugins and modules handle their actions after
			 * the plugin settings updates
			 *
			 * @param array<string,mixed> $data     Updated plugin data.
			 * @param array<string,mixed> $old_data Plugin data prior to the recent update.
			 * @param Settings            $settings Self instance for reference.
			 */
			do_action( 'custom_login__settings_updated', $data, $old_data, $this );
		}

		return true;
	}
}
