<?php
/**
 * Fields_Group class
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
 * Fields_Group class
 *
 * @phpstan-type Type_Fields_Config array<string,array{type:'array_of_strings',default_value:string[],restorer?:?Closure,sanitizer?:?Closure,validator?:?Closure,skip_default_sanitization?:bool}|array{type:'boolean',default_value:bool,restorer?:?Closure,sanitizer?:?Closure,validator?:?Closure}|array{type:'float',default_value:float,min:float,max:?float,precision?:int,restorer?:?Closure,sanitizer?:?Closure,validator?:?Closure}|array{type:'integer',default_value:int,min:int,max:?int,restorer?:?Closure,sanitizer?:?Closure,validator?:?Closure}|array{type:'string_of_choice',default_value:string,allowed_values?:string[],restorer?:?Closure,sanitizer?:?Closure,validator?:?Closure,skip_default_sanitization?:bool}|array{type:'string',default_value:string|Closure,restorer?:?Closure,sanitizer?:?Closure,validator?:?Closure,skip_default_sanitization?:bool}>
 */
class Fields_Group {
	/**
	 * Ensure objects of this class are key identifiable
	 */
	use Key_Identifiable;

	/**
	 * Fields group type
	 *
	 * @var string
	 */
	const TYPE = 'static';

	/**
	 * Fields
	 *
	 * @var array<string,Field|Fields_Group>
	 */
	protected array $fields = [];

	/**
	 * Construct the object
	 *
	 * @param string             $key           Unique key identifying the group of fields.
	 * @param Type_Fields_Config $fields_config Fields configuration array.
	 */
	public function __construct( string $key, array $fields_config ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->key = $key;

		/**
		 * Initialize fields
		 */
		foreach ( $fields_config as $field_key => $field_config ) {
			if ( $field_config['default_value'] instanceof Closure ) {
				$field_config['default_value'] = call_user_func( $field_config['default_value'] );
			}

			switch ( $field_config['type'] ) {
				case 'array_of_strings':
					$field = new Field_Array_Of_Strings(
						$field_key,
						$field_config['default_value'],
						$field_config['restorer'] ?? null,
						$field_config['sanitizer'] ?? null,
						$field_config['validator'] ?? null,
						$field_config['skip_default_sanitization'] ?? false,
					);

					break;
				case 'boolean':
					$field = new Field_Boolean(
						$field_key,
						$field_config['default_value'],
						$field_config['restorer'] ?? null,
						$field_config['sanitizer'] ?? null,
						$field_config['validator'] ?? null,
					);

					break;
				case 'float':
					$field = new Field_Float(
						$field_key,
						$field_config['default_value'],
						$field_config['min'],
						$field_config['max'],
						$field_config['precision'] ?? 2,
						$field_config['restorer'] ?? null,
						$field_config['sanitizer'] ?? null,
						$field_config['validator'] ?? null,
					);

					break;
				case 'integer':
					$field = new Field_Integer(
						$field_key,
						$field_config['default_value'],
						$field_config['min'],
						$field_config['max'],
						$field_config['restorer'] ?? null,
						$field_config['sanitizer'] ?? null,
						$field_config['validator'] ?? null,
					);

					break;
				case 'string_of_choice':
					$field = new Field_String_Of_Choice(
						$field_key,
						$field_config['default_value'],
						$field_config['allowed_values'] ?? [],
						$field_config['restorer'] ?? null,
						$field_config['sanitizer'] ?? null,
						$field_config['validator'] ?? null,
						$field_config['skip_default_sanitization'] ?? false,
					);

					break;
				case 'string':
					$field = new Field_String(
						$field_key,
						$field_config['default_value'],
						$field_config['restorer'] ?? null,
						$field_config['sanitizer'] ?? null,
						$field_config['validator'] ?? null,
						$field_config['skip_default_sanitization'] ?? false,
					);

					break;
			}

			$this->fields[ $field_key ] = $field;
		}
	}

	/**
	 * Load values for fields in group
	 *
	 * @param array<string,mixed> $values      Values array.
	 * @param bool                $use_restore Whether the value restore should happen on validation error or not.
	 *
	 * @return true|WP_Error Nothing on success, instance of WP_Error on failure.
	 */
	public function load_values( array $values, bool $use_restore = false ) {
		foreach ( $this->get_fields() as $field ) {
			if ( ! $field instanceof Field ) {
				continue;
			}

			$field->set_value( $values[ $field->get_key_camel_case() ] ?? $field->get_default_value(), $use_restore );

			if ( $field->get_value() instanceof WP_Error ) {
				return $field->get_value();
			}
		}

		return true;
	}

	/**
	 * Get fields or field groups that belongs to this group
	 *
	 * @return array<string,Field|Fields_Group> Array of fields or field groups that belongs to this group.
	 */
	public function get_fields(): array {
		return $this->fields;
	}

	/**
	 * Get keys of all fields in this group
	 *
	 * @return string[] Array of field keys.
	 */
	public function get_all_fields_keys(): array {
		return array_keys( $this->fields );
	}

	/**
	 * Get values of all fields in this group
	 *
	 * @return array<string,mixed> Array of values of all fields in this group.
	 */
	public function get_all_fields_values(): array {
		$values = [];

		foreach ( $this->get_fields() as $field ) {
			if ( ! $field instanceof Field ) {
				continue;
			}

			$values[ $field->get_key() ] = $field->get_value();
		}

		return $values;
	}

	/**
	 * Get single field that belongs to this group
	 *
	 * @param string $key Field key.
	 *
	 * @return null|Field|Fields_Group Field or fields group requested, null if not found.
	 */
	public function get_field( string $key ): ?object {
		return $this->fields[ $key ] ?? null;
	}

	/**
	 * Get value of a single field
	 *
	 * @param string $key Field key.
	 *
	 * @return mixed Field value.
	 */
	public function get_field_value( string $key ) {
		$field = $this->fields[ $key ] ?? null;

		return $field instanceof Field
			? $field->get_value()
			: null;
	}

	/**
	 * Get values of all fields in this group
	 *
	 * @param bool $use_camel_case Whether to use camel case for keys (JS & DB operations), or snake case (PHP operations).
	 *
	 * @return array<string,mixed>|WP_Error Fields values array, or instance of WP_Error if any of the fields returned such.
	 */
	public function get_value( bool $use_camel_case = false ) {
		$values = [];

		foreach ( $this->get_fields() as $field ) {
			$value = $field instanceof Fields_Group
				? $field->get_value( $use_camel_case )
				: $field->get_value();

			if ( $value instanceof WP_Error ) {
				return $value;
			}

			$key = $use_camel_case
				? $field->get_key_camel_case()
				: $field->get_key();

			$values[ $key ] = $value;
		}

		return $values;
	}
}
