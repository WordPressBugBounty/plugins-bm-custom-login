<?php
/**
 * Dynamic_Fields_Group class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

use Closure;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Dynamic_Fields_Group class
 *
 * @phpstan-import-type Type_Fields_Config from Fields_Group
 * @phpstan-type Type_Dynamic_Fields_Config array<string,array{fields:Type_Fields_Config,restorer?:?Closure,sanitizer?:?Closure,validator?:?Closure}>
 */
final class Dynamic_Fields_Group extends Fields_Group {
	/**
	 * Fields group type
	 *
	 * @var string
	 */
	const TYPE = 'dynamic';

	/**
	 * Fields configuration array
	 *
	 * @var Type_Dynamic_Fields_Config
	 */
	protected array $fields_config;

	/**
	 * Templates for dynamic fields construction
	 *
	 * @var Fields_Group[]
	 */
	protected array $templates = [];

	/**
	 * Optional, additional restorers of the value for dynamic fields
	 *
	 * @var Closure[]
	 */
	protected array $restorers = [];

	/**
	 * Optional, additional sanitizers of the value for dynamic fields
	 *
	 * @var Closure[]
	 */
	protected array $sanitizers = [];

	/**
	 * Optional, additional validators for dynamic fields
	 *
	 * @var Closure[]
	 */
	protected array $validators = [];

	/**
	 * Construct the object
	 *
	 * @param string                     $key           Fields group key.
	 * @param Type_Dynamic_Fields_Config $fields_config Fields configuration array.
	 */
	public function __construct( string $key, array $fields_config ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->key           = $key;
		$this->fields_config = $fields_config;

		// Build template fields groups.
		foreach ( $this->fields_config as $key => $config ) {
			$this->templates[ $key ] = new Fields_Group( $key, $config['fields'] );

			if ( isset( $config['restorer'] ) && $config['restorer'] instanceof Closure ) { // @phpstan-ignore instanceof.alwaysTrue
				$this->restorers[ $key ] = $config['restorer'];
			}

			if ( isset( $config['sanitizer'] ) && $config['sanitizer'] instanceof Closure ) { // @phpstan-ignore instanceof.alwaysTrue
				$this->sanitizers[ $key ] = $config['sanitizer'];
			}

			if ( isset( $config['validator'] ) && $config['validator'] instanceof Closure ) { // @phpstan-ignore instanceof.alwaysTrue
				$this->validators[ $key ] = $config['validator'];
			}
		}
	}

	/**
	 * Build dynamically configured fields groups
	 *
	 * @param array<string,array<string,mixed>> $values      Values array.
	 * @param bool                              $use_restore Whether the value restore should happen on validation error or not.
	 *
	 * @return true|WP_Error Nothing on success, instance of WP_Error on failure.
	 */
	public function load_values( array $values, bool $use_restore = false ) {
		/** @var array{type?:string} $config */
		foreach ( $values as $key => $config ) {
			if ( ! isset( $config['type'] ) || ! isset( $this->fields_config[ $config['type'] ] ) ) {
				continue;
			}

			$group        = new Fields_Group( $key, $this->fields_config[ $config['type'] ]['fields'] );
			$group_values = $group->load_values( $config, $use_restore );

			if ( $group_values instanceof WP_Error ) {
				return $group_values;
			}

			$this->fields[ $key ] = $group;
		}

		return true;
	}

	/**
	 * Get dynamic fields
	 *
	 * @param string $key Fields group key.
	 *
	 * @return Fields_Group Fields group of dynamically configured fields.
	 */
	public function get_dynamic_fields( string $key ): Fields_Group {
		$fields_config = [];

		foreach ( $this->get_fields() as $group ) {
			if ( ! $group instanceof Fields_Group ) {
				continue;
			}

			/** @var string $field_type */
			$field_type = $group->get_field_value( 'type' );

			/** @var string $field_key */
			$field_key = $group->get_field_value( 'key' );

			$field_config = null;
			$restorer     = $this->restorers[ $field_type ] ?? null;
			$sanitizer    = $this->sanitizers[ $field_type ] ?? null;
			$validator    = $this->validators[ $field_type ] ?? null;

			if ( null !== $restorer ) {
				/**
				 * Extend the restorer function by passing the instance
				 * of the current object
				 *
				 * @param mixed $value Value to restore.
				 *
				 * @return mixed Restored value.
				 */
				$restorer = function ( $value ) use ( $restorer, $group ) {
					return call_user_func( $restorer, $value, $group );
				};
			}

			if ( null !== $validator ) {
				/**
				 * Extend the validator function by passing the instance
				 * of the current object
				 *
				 * @param mixed $value Value to validate.
				 *
				 * @return mixed Validated value.
				 */
				$validator = function ( $value ) use ( $validator, $group ) {
					return call_user_func( $validator, $value, $group );
				};
			}

			switch ( $field_type ) {
				case 'array_of_strings':
					/** @var ?string[] $default_value */
					$default_value = $group->get_field_value( 'default_value' );

					$field_config = Configuration::array_of_strings_field(
						$default_value ?? [],
						$restorer,
						$sanitizer,
						$validator,
					);

					break;
				case 'boolean':
					/** @var ?bool $default_value */
					$default_value = $group->get_field_value( 'default_value' );

					$field_config = Configuration::boolean_field(
						$default_value ?? false,
						$restorer,
						$sanitizer,
						$validator,
					);

					break;
				case 'date':
				case 'salary':
				case 'text':
				case 'url':
					/** @var ?string $default_value */
					$default_value = $group->get_field_value( 'default_value' );

					$field_config = Configuration::string_field(
						$default_value ?? '',
						$restorer,
						$sanitizer,
						$validator,
					);

					break;
				case 'integer':
					/** @var ?int $default_value */
					$default_value = $group->get_field_value( 'default_value' );

					/** @var int $min */
					$min = $group->get_field_value( 'min' );

					/** @var bool $use_max */
					$use_max = $group->get_field_value( 'use_max' );

					/** @var ?int $max */
					$max = $use_max ? $group->get_field_value( 'max' ) : null;

					/**
					 * Ensure field configuration is valid
					 */
					if ( $use_max && is_int( $max ) && $max < $min ) {
						$max = $min;
					}

					if ( $default_value < $min ) {
						$default_value = $min;
					}

					if ( $use_max && is_int( $max ) && $default_value > $max ) {
						$default_value = $max;
					}

					$field_config = Configuration::integer_field(
						$default_value ?? 0,
						$min,
						$max,
						$restorer,
						$sanitizer,
						$validator,
					);

					break;
			}

			if ( null !== $field_config ) {
				$fields_config[ $field_key ] = $field_config;
			}
		}

		/** @var Type_Fields_Config $fields_config */
		return new Fields_Group( $key, $fields_config );
	}

	/**
	 * Get the templates of all fields in this dynamic field group
	 *
	 * @return array<string,array<string,mixed>> Templates of all fields in this dynamic field group.
	 */
	public function get_templates(): array {
		$templates = [];

		foreach ( $this->templates as $group ) {
			$group->load_values( [], true );
			$template = [];

			foreach ( $group->get_fields() as $field ) {
				if ( 'key' === $field->get_key() ) {
					continue;
				}

				$template[ $field->get_key_camel_case() ] = $field->get_value();
			}

			$templates[ $group->get_key_camel_case() ] = $template;
		}

		return $templates;
	}
}
