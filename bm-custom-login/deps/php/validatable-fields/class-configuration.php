<?php
/**
 * Configuration class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

use Closure;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * Configuration class
 */
final class Configuration {
	/**
	 * Configure the "array of strings" field
	 *
	 * @param string[] $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'array_of_strings',default_value:string[],restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function array_of_strings_field( array $default_value = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'array_of_strings',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "boolean" field
	 *
	 * @param bool     $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'boolean',default_value:bool,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function boolean_field( bool $default_value = false, ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'boolean',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "character tokens" field
	 *
	 * @param string[] $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'array_of_strings',default_value:string[],restorer:?Closure,sanitizer:?Closure,validator:?Closure,skip_default_sanitization:bool} Field configuration array.
	 */
	public static function character_tokens_field( array $default_value = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'                      => 'array_of_strings',
			'default_value'             => $default_value,
			'restorer'                  => $restorer ?? Closures::character_tokens_field_restorer_and_sanitizer(),
			'sanitizer'                 => $sanitizer ?? Closures::character_tokens_field_restorer_and_sanitizer(),
			'validator'                 => $validator ?? Closures::character_tokens_field_validator(),
			'skip_default_sanitization' => true,
		];
	}

	/**
	 * Configure the "css" field
	 *
	 * @param string   $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'string',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function css_field( string $default_value = '', ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'string',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer ?? Closures::css_field_sanitizer(),
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "color" field
	 *
	 * Stores a CSS color value, restricting it on save to a valid hex,
	 * rgb(a)/hsl(a), or named color so that an attacker-controlled value
	 * cannot break out of the CSS declaration it is later composed into.
	 *
	 * @param string   $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'string',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function color_field( string $default_value = '', ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'string',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer ?? Closures::color_field_sanitizer(),
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "box shadow" field
	 *
	 * Stores a CSS box-shadow value, restricting it on save to the character
	 * set valid for box-shadow so that an attacker-controlled value cannot
	 * break out of the CSS declaration it is later composed into.
	 *
	 * @param string   $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'string',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function box_shadow_field( string $default_value = '', ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'string',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer ?? Closures::box_shadow_field_sanitizer(),
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "date" field
	 *
	 * @param string   $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'string',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function date_field( string $default_value = '', ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'string',
			'default_value' => $default_value,
			'restorer'      => $restorer ?? Closures::date_field_restorer(),
			'sanitizer'     => $sanitizer,
			'validator'     => $validator ?? Closures::date_field_validator(),
		];
	}

	/**
	 * Generate a unique key for a dynamic field
	 *
	 * @return string Dynamic field key in the format "d:XXXXXXXXXXXXXXXXX" (19 chars total).
	 */
	public static function generate_dynamic_field_key(): string {
		$microtime = str_replace( '.', '', sprintf( '%.6F', microtime( true ) ) );
		return sprintf( 'd:%s%d', substr( $microtime, -16 ), wp_rand( 0, 9 ) );
	}

	/**
	 * Configure the "dynamic field key" field
	 *
	 * @return array{type:'string',default_value:Closure,validator:?Closure} Field configuration array.
	 */
	public static function dynamic_field_key_field(): array {
		return [
			'type'          => 'string',

			/**
			 * Build default value of this field dynamically
			 *
			 * @return string Field value.
			 */
			'default_value' => function (): string {
				return self::generate_dynamic_field_key();
			},

			/**
			 * Additional validation, specific for this field
			 *
			 * @param mixed $value Provided value.
			 *
			 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
			 */
			'validator'     => function ( $value ) {
				if ( ! is_string( $value ) || ! Utils\Strings::str_starts_with( $value, 'd:' ) || 19 !== strlen( $value ) || 1 !== preg_match( '/^\\d+$/', str_replace( 'd:', '', $value ) ) ) {
					return new WP_Error(
						'field_key_incorrect',
						sprintf(
							// Translators: %s - invalid field key value.
							__( '"%s" is not a valid key of the dynamic field.', 'bm-custom-login' ),
							Utils\Type::ensure_string( $value ),
						),
					);
				}

				return true;
			},
		];
	}

	/**
	 * Configure the "exact string" field
	 *
	 * @param string $exact_value Exact value of the field.
	 *
	 * @return array{type:'string',default_value:string,restorer:Closure,validator:Closure} Field configuration array.
	 */
	public static function exact_string_field( string $exact_value ): array {
		return [
			'type'          => 'string',
			'default_value' => $exact_value,

			/**
			 * Restore the value to expected one
			 *
			 * @return string Field value.
			 */
			'restorer'      => function () use ( $exact_value ): string {
				return $exact_value;
			},

			/**
			 * Ensure this field value always match
			 * the exact value defined
			 *
			 * @param mixed $value Provided value.
			 *
			 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
			 */
			'validator'     => function ( $value ) use ( $exact_value ) {
				if ( $value !== $exact_value ) {
					return new WP_Error(
						'field_value_incorrect',
						sprintf(
							// Translators: %s - expected exact value.
							__( 'Field\'s value must be exactly "%s".', 'bm-custom-login' ),
							$exact_value,
						),
					);
				}

				return true;
			},
		];
	}

	/**
	 * Configure the "float" field
	 *
	 * @param float    $default_value Default value of the field.
	 * @param float    $min           Minimum valid value.
	 * @param ?float   $max           Maximum valid value.
	 * @param int      $precision     Float precision.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'float',default_value:float,min:float,max:?float,precision:int,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function float_field( float $default_value = 0.00, float $min = 0, ?float $max = null, int $precision = 2, ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'float',
			'default_value' => $default_value,
			'min'           => $min,
			'max'           => $max,
			'precision'     => $precision,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "integer" field
	 *
	 * @param int      $default_value Default value of the field.
	 * @param int      $min           Minimum valid value.
	 * @param ?int     $max           Maximum valid value.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'integer',default_value:int,min:int,max:?int,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function integer_field( int $default_value = 0, int $min = 0, ?int $max = null, ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'integer',
			'default_value' => $default_value,
			'min'           => $min,
			'max'           => $max,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "integer of choice" field
	 *
	 * @param int      $default_value  Default value of the field.
	 * @param int[]    $allowed_values Array of allowed values.
	 * @param ?Closure $restorer       Additional function for value restore.
	 * @param ?Closure $sanitizer      Additional sanitizer function.
	 * @param ?Closure $validator      Additional validation function.
	 *
	 * @return array{type:'integer_of_choice',default_value:int,allowed_values:int[],restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function integer_of_choice_field( int $default_value = 0, array $allowed_values = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'           => 'integer_of_choice',
			'default_value'  => $default_value,
			'allowed_values' => $allowed_values,
			'restorer'       => $restorer,
			'sanitizer'      => $sanitizer,
			'validator'      => $validator,
		];
	}

	/**
	 * Configure the "media id" field
	 *
	 * @param int      $default_value   Default value of the field.
	 * @param string[] $supported_types Array of supported media types; defaults to [ "image", "video" ].
	 * @param ?Closure $restorer        Additional function for value restore.
	 * @param ?Closure $sanitizer       Additional sanitizer function.
	 * @param ?Closure $validator       Additional validation function.
	 *
	 * @return array{type:'integer',default_value:int,min:int,max:?int,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function media_id_field( int $default_value = 0, array $supported_types = [ 'image', 'video' ], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'integer',
			'default_value' => $default_value,
			'min'           => 0,
			'max'           => null,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator ?? Closures::media_id_field_validator( $supported_types ),
		];
	}

	/**
	 * Configure the "post types" field
	 *
	 * @param string[]            $default_value Default value of the field.
	 * @param array<string,mixed> $query_args    Query arguments used for getting a list of post types.
	 * @param ?Closure            $restorer      Additional function for value restore.
	 * @param ?Closure            $sanitizer     Additional sanitizer function.
	 * @param ?Closure            $validator     Additional validation function.
	 *
	 * @return array{type:'array_of_strings',default_value:string[],restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function post_types_field( array $default_value = [ 'post', 'page' ], array $query_args = [ 'post_type__in' => [ 'post', 'page' ] ], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'array_of_strings',
			'default_value' => $default_value,
			'restorer'      => $restorer ?? Closures::post_types_field_restorer( $query_args ),
			'sanitizer'     => $sanitizer,
			'validator'     => $validator ?? Closures::post_types_field_validator( $query_args ),
		];
	}

	/**
	 * Configure the "restricted phrases" field
	 *
	 * @param int      $max_entries      Maximum number of entries allowed in the list.
	 * @param int      $max_entry_length Maximum length of a single entry, in characters.
	 * @param string[] $default_value    Default value of the field.
	 * @param ?Closure $restorer         Additional function for value restore.
	 * @param ?Closure $sanitizer        Additional sanitizer function.
	 * @param ?Closure $validator        Additional validation function.
	 *
	 * @return array{type:'array_of_strings',default_value:string[],restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function restricted_phrases_field( int $max_entries, int $max_entry_length, array $default_value = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'array_of_strings',
			'default_value' => $default_value,
			'restorer'      => $restorer ?? Closures::restricted_phrases_field_restorer_and_sanitizer(),
			'sanitizer'     => $sanitizer ?? Closures::restricted_phrases_field_restorer_and_sanitizer(),
			'validator'     => $validator ?? Closures::restricted_phrases_field_validator( $max_entries, $max_entry_length ),
		];
	}

	/**
	 * Configure the "string of choice" field
	 *
	 * @param string   $default_value  Default value of the field.
	 * @param string[] $allowed_values Array of values allowed as this field's value.
	 * @param ?Closure $restorer       Additional function for value restore.
	 * @param ?Closure $sanitizer      Additional sanitizer function.
	 * @param ?Closure $validator      Additional validation function.
	 *
	 * @return array{type:'string_of_choice',default_value:string,allowed_values:string[],restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function string_of_choice_field( string $default_value = '', array $allowed_values = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'           => 'string_of_choice',
			'default_value'  => $default_value,
			'allowed_values' => $allowed_values,
			'restorer'       => $restorer,
			'sanitizer'      => $sanitizer,
			'validator'      => $validator,
		];
	}

	/**
	 * Configure the "string" field
	 *
	 * @param string   $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'string',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function string_field( string $default_value = '', ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'string',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "textarea" field
	 *
	 * Multi-line counterpart to {@see string_field()}. Same shape and storage
	 * type. The newline-preserving default sanitization (`sanitize_textarea_field()`)
	 * is applied by the `Field_Textarea` class, not by this factory; the
	 * `$sanitizer` argument here is an optional additional sanitizer layered on
	 * top. Use for any value an admin edits in a `<textarea>` — email bodies,
	 * restricted-phrases lists, etc.
	 *
	 * @param string   $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'textarea',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function textarea_field( string $default_value = '', ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'textarea',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator,
		];
	}

	/**
	 * Configure the "unit" field
	 *
	 * @param string   $default_value   Default value of the field.
	 * @param string[] $supported_units Array of supported units.
	 * @param ?Closure $restorer        Additional function for value restore.
	 * @param ?Closure $sanitizer       Additional sanitizer function.
	 * @param ?Closure $validator       Additional validation function.
	 *
	 * @return array{type:'string',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function unit_field( string $default_value = '1px', array $supported_units = [ 'px', '%', 'em', 'rem', 'vw', 'vh' ], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'string',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer,
			'validator'     => $validator ?? Closures::unit_field_validator( $supported_units ),
		];
	}

	/**
	 * Configure the "url" field
	 *
	 * @param string   $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'string',default_value:string,restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function url_field( string $default_value = '', ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'string',
			'default_value' => $default_value,
			'restorer'      => $restorer,
			'sanitizer'     => $sanitizer ?? Closures::url_field_sanitizer(),
			'validator'     => $validator ?? Closures::url_field_validator(),
		];
	}

	/**
	 * Configure the "user roles" field
	 *
	 * @param Utils\Users $users         Users utility instance; hinted as `object` to avoid coupling this shared package to the consumer-rewritten `Utils` namespace.
	 * @param string[]    $default_value Default value of the field.
	 * @param ?Closure    $restorer      Additional function for value restore.
	 * @param ?Closure    $sanitizer     Additional sanitizer function.
	 * @param ?Closure    $validator     Additional validation function.
	 *
	 * @return array{type:'array_of_strings',default_value:string[],restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function user_roles_field( object $users, array $default_value = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'array_of_strings',
			'default_value' => $default_value,
			'restorer'      => $restorer ?? Closures::user_roles_field_restorer( $users ),
			'sanitizer'     => $sanitizer,
			'validator'     => $validator ?? Closures::user_roles_field_validator( $users ),
		];
	}

	/**
	 * Configure the "users" field
	 *
	 * @param string[] $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 *
	 * @return array{type:'array_of_strings',default_value:string[],restorer:?Closure,sanitizer:?Closure,validator:?Closure} Field configuration array.
	 */
	public static function users_field( array $default_value = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ): array {
		return [
			'type'          => 'array_of_strings',
			'default_value' => $default_value,
			'restorer'      => $restorer ?? Closures::users_field_restorer(),
			'sanitizer'     => $sanitizer,
			'validator'     => $validator ?? Closures::users_field_validator(),
		];
	}
}
