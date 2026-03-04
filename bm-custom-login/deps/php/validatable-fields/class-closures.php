<?php
/**
 * Closures class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

use Closure;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use WP_Error;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Closures class
 */
final class Closures {
	/**
	 * Return the field sanitizer closure function that allows
	 * alphanumeric characters only in the "array of strings" field
	 *
	 * @return Closure Field sanitizer closure function.
	 */
	public static function alphanumeric_array_of_strings_field_sanitizer(): Closure {
		/**
		 * Sanitizer for the "alphanumeric_array_of_strings" dynamic field
		 *
		 * @param mixed $values Values to sanitize.
		 *
		 * @return string[] Sanitized values.
		 */
		return function ( $values ): array {
			return array_map(
				function ( $value ) {
					return preg_replace( '/[\W]/', '', $value );
				},
				Utils\Type::ensure_array_of_strings( $values, [] ),
			);
		};
	}

	/**
	 * Return the character tokens field restorer and sanitizer closure function
	 *
	 * @return Closure Field restorer and sanitizer closure function.
	 */
	public static function character_tokens_field_restorer_and_sanitizer(): Closure {
		/**
		 * Restorer for the "character_tokens" dynamic field
		 *
		 * @param mixed $values Values to restore.
		 *
		 * @return string[] Restored value.
		 */
		return function ( $values ): array {
			return array_map(
				fn ( string $item ): string => mb_substr( $item, 0, 1 ),
				Utils\Type::ensure_array_of_strings( $values, [] ),
			);
		};
	}

	/**
	 * Return the character tokens field validator closure function
	 *
	 * @return Closure Field validator closure function.
	 */
	public static function character_tokens_field_validator(): Closure {
		/**
		 * Validator for the dynamic field build based on the
		 * "character tokens" field template
		 *
		 * @param mixed $values Values to validate.
		 *
		 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
		 */
		return function ( $values ) {
			if ( ! is_array( $values ) ) {
				return new WP_Error(
					'non_array_value',
					sprintf(
						// Translators: %s - type of the value given.
						__( 'Value must be an array, %s given.', 'bm-custom-login' ),
						gettype( $values ),
					),
				);
			}

			foreach ( $values as $value ) {
				if ( ! is_string( $value ) || 1 !== mb_strlen( $value ) ) {
					return new WP_Error(
						'field_value_incorrect',
						sprintf(
							// Translators: %s - invalid character token value.
							__( '"%s" is not a valid character token.', 'bm-custom-login' ),
							Utils\Type::ensure_string( $value ),
						),
					);
				}
			}

			return true;
		};
	}

	/**
	 * Return the field sanitizer closure function that removes
	 * disallowed characters from the user-entered CSS
	 *
	 * @return Closure Field sanitizer closure function.
	 */
	public static function css_field_sanitizer(): Closure {
		/**
		 * Sanitizer for the dynamic field build based on the
		 * "css" field template
		 *
		 * @param mixed $value Value to sanitize.
		 *
		 * @return string Sanitized value.
		 */
		return function ( $value ): string {
			return Utils\Strings::sanitize_css( $value );
		};
	}

	/**
	 * Return the date field restorer closure function
	 *
	 * @return Closure Field restorer closure function.
	 */
	public static function date_field_restorer(): Closure {
		/**
		 * Value restorer for the dynamic field build based on the
		 * "date" field template
		 *
		 * @param mixed         $value        Current value.
		 * @param ?Fields_Group $fields_group Instance of the Fields Group this field belongs to; null if field is independent.
		 *
		 * @return string Restored value.
		 */
		return function ( $value, ?Fields_Group $fields_group = null ): string {
			/** @var string $default_value */
			$default_value = null === $fields_group
				? ''
				: $fields_group->get_field_value( 'default_value' );

			// Verify the default value.
			if ( Utils\Type::is_date( $default_value ) ) {
				return $default_value;
			}

			/** @var bool $allow_empty */
			$allow_empty = null === $fields_group
				? true
				: $fields_group->get_field_value( 'allow_empty' );

			// Default value is incorrect; check if empty value is fine.
			if ( true === $allow_empty ) {
				return '';
			}

			return Utils\Date_Time::get_site_datetime()->format( 'Y-m-d' );
		};
	}

	/**
	 * Return the date field validator closure function
	 *
	 * @return Closure Field validator closure function.
	 */
	public static function date_field_validator(): Closure {
		/**
		 * Validator for the dynamic field build based on the
		 * "date" field template
		 *
		 * @param mixed         $value        Value to validate.
		 * @param ?Fields_Group $fields_group Instance of the Fields Group this field belongs to; null if field is independent.
		 *
		 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
		 */
		return function ( $value, ?Fields_Group $fields_group = null ) {
			if ( ! is_string( $value ) ) {
				return new WP_Error(
					'non_string_value',
					sprintf(
						// Translators: %s - type of the value given.
						__( 'Value must be a string, %s given.', 'bm-custom-login' ),
						gettype( $value ),
					),
				);
			}

			/** @var bool $allow_empty */
			$allow_empty = null === $fields_group
				? true
				: $fields_group->get_field_value( 'allow_empty' );

			// Ensure empty value passes if allowed.
			if ( true === $allow_empty && '' === $value ) {
				return true;
			}

			// Validate the date.
			if ( Utils\Type::is_date( $value ) ) {
				return true;
			}

			return new WP_Error(
				'field_value_incorrect',
				sprintf(
					// Translators: %s - invalid date value.
					__( '"%s" is not a valid date.', 'bm-custom-login' ),
					$value,
				),
			);
		};
	}

	/**
	 * Return the media ID field validator closure function
	 *
	 * @param string[] $supported_types Array of supported media types; defaults to [ "image", "video" ].
	 *
	 * @return Closure Field validator closure function.
	 */
	public static function media_id_field_validator( array $supported_types ): Closure {
		/**
		 * Validator for the dynamic field build based on the
		 * "media_id" field template
		 *
		 * @param mixed $value Value to validate.
		 *
		 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
		 */
		return function ( $value ) use ( $supported_types ) {
			if ( ! is_int( $value ) ) {
				return new WP_Error(
					'non_integer_value',
					sprintf(
						// Translators: %s - type of the value given.
						__( 'Value must be an integer, %s given.', 'bm-custom-login' ),
						gettype( $value ),
					),
				);
			}

			// Ensure empty value is always supported.
			if ( 0 === $value ) {
				return true;
			}

			// Get the post type.
			$post_type = get_post_type( $value );

			if ( 'attachment' !== $post_type ) {
				return new WP_Error(
					'invalid_post_type',
					sprintf(
						// Translators: %d - post ID.
						__( 'Post with ID "%d" is not an attachment.', 'bm-custom-login' ),
						$value,
					),
				);
			}

			// Get the mime type.
			$mime_type = get_post_mime_type( $value );

			if ( false === $mime_type ) {
				return new WP_Error(
					'unknown_mime_type',
					sprintf(
						// Translators: %d - attachment ID.
						__( 'MIME type of the attachment with ID "%d" is unknown.', 'bm-custom-login' ),
						$value,
					),
				);
			}

			$type = strtok( $mime_type, '/' );

			if ( ! in_array( $type, $supported_types, true ) ) {
				return new WP_Error(
					'mime_type_incorrect',
					sprintf(
						// Translators: %d - attachment ID.
						__( 'MIME type of the attachment with ID "%d" is not supported by this field.', 'bm-custom-login' ),
						$value,
					),
				);
			}

			return true;
		};
	}

	/**
	 * Unit field validator
	 *
	 * @param string[] $supported_units Array of supported units.
	 *
	 * @return Closure Field validator closure function.
	 */
	public static function unit_field_validator( array $supported_units ): Closure {
		/**
		 * Validator for the dynamic field build based on the
		 * "unit" field template
		 *
		 * @param mixed $value Value to validate.
		 *
		 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
		 */
		return function ( $value ) use ( $supported_units ) {
			if ( ! is_string( $value ) ) {
				return new WP_Error(
					'non_string_value',
					sprintf(
						// Translators: %s - type of the value given.
						__( 'Value must be a string, %s given.', 'bm-custom-login' ),
						gettype( $value ),
					),
				);
			}

			$pattern = sprintf( '/(\d+(%s)+)/', implode( '|', $supported_units ) );

			if ( 1 !== preg_match( $pattern, $value ) ) {
				return new WP_Error(
					'field_value_incorrect',
					sprintf(
						// Translators: %s - invalid unit value.
						__( '"%s" is not a valid unit value.', 'bm-custom-login' ),
						$value,
					),
				);
			}

			return true;
		};
	}

	/**
	 * Return the URL field sanitizer closure function
	 *
	 * @return Closure Field sanitizer closure function.
	 */
	public static function url_field_sanitizer(): Closure {
		/**
		 * Sanitizer for the dynamic field build based on the
		 * "url" field template
		 *
		 * @param mixed $value Value to sanitize.
		 *
		 * @return string Sanitized value.
		 */
		return function ( $value ): string {
			$value = Utils\Type::ensure_string( $value );

			if ( '' === $value ) {
				return $value;
			}

			return is_email( $value )
				? sanitize_email( $value )
				: sanitize_url( $value );
		};
	}

	/**
	 * Return the URL field validator closure function
	 *
	 * @return Closure Field validator closure function.
	 */
	public static function url_field_validator(): Closure {
		/**
		 * Validator for the dynamic field build based on the
		 * "url" field template
		 *
		 * @param mixed $value Value to validate.
		 *
		 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
		 */
		return function ( $value ) {
			if ( ! is_string( $value ) ) {
				return new WP_Error(
					'non_string_value',
					sprintf(
						// Translators: %s - type of the value given.
						__( 'Value must be a string, %s given.', 'bm-custom-login' ),
						gettype( $value ),
					),
				);
			}

			if ( '' === $value || is_email( $value ) ) {
				return true;
			}

			// Validate URL.
			if ( Utils\Type::is_url( $value ) ) {
				return true;
			}

			return new WP_Error(
				'field_value_incorrect',
				sprintf(
					// Translators: %s - invalid URL value.
					__( '"%s" is not a valid URL.', 'bm-custom-login' ),
					$value,
				),
			);
		};
	}

	/**
	 * Return the user roles restorer closure function
	 *
	 * @param Utils\Users $users Users utility instance.
	 *
	 * @return Closure Field restorer closure function.
	 */
	public static function user_roles_field_restorer( object $users ): Closure {
		/**
		 * Value restorer for the dynamic field build based on the
		 * "user_roles" field template
		 *
		 * @param mixed $values Current values.
		 *
		 * @return array Restored values array.
		 */
		return function ( $values ) use ( $users ): array {
			$known_roles = array_column( $users->get_known_user_roles(), 'value' );
			$valid_roles = [];

			if ( is_array( $values ) ) {
				foreach ( $values as $value ) {
					if ( in_array( $value, $known_roles, true ) ) {
						$valid_roles[] = $value;
					}
				}
			}

			return $valid_roles;
		};
	}

	/**
	 * Return the user roles field validator closure function
	 *
	 * @param Utils\Users $users Users utility instance.
	 *
	 * @return Closure Field validator closure function.
	 */
	public static function user_roles_field_validator( object $users ): Closure {
		/**
		 * Validator for the dynamic field build based on the
		 * "user_roles" field template
		 *
		 * @param mixed $values Values to validate.
		 *
		 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
		 */
		return function ( $values ) use ( $users ) {
			if ( ! is_array( $values ) ) {
				return new WP_Error(
					'non_array_value',
					sprintf(
						// Translators: %s - type of the value given.
						__( 'Value must be an array, %s given.', 'bm-custom-login' ),
						gettype( $values ),
					),
				);
			}

			$known_roles = array_column( $users->get_known_user_roles(), 'value' );

			foreach ( $values as $value ) {
				if ( ! in_array( $value, $known_roles, true ) ) {
					return new WP_Error(
						'field_value_out_of_scope',
						sprintf(
							// Translators: %1$s - invalid role value, %2$s - list of known roles.
							__( '"%1$s" is not a value within "%2$s".', 'bm-custom-login' ),
							Utils\Type::ensure_string( $value ),
							implode( ', ', $known_roles ),
						),
					);
				}
			}

			return true;
		};
	}

	/**
	 * Return the users restorer closure function
	 *
	 * @return Closure Field restorer closure function.
	 */
	public static function users_field_restorer(): Closure {
		/**
		 * Value restorer for the dynamic field build based on the
		 * "users" field template
		 *
		 * @param mixed $values Current values.
		 *
		 * @return array Restored values array.
		 */
		return function ( $values ): array {
			$valid_users = [];

			if ( is_array( $values ) ) {
				foreach ( $values as $value ) {
					if ( ! is_string( $value ) ) {
						continue;
					}

					$user = get_user_by( 'login', $value );

					if ( $user instanceof WP_User ) {
						$valid_users[] = $value;
					}
				}
			}

			return $valid_users;
		};
	}

	/**
	 * Return the users field validator closure function
	 *
	 * @return Closure Field validator closure function.
	 */
	public static function users_field_validator(): Closure {
		/**
		 * Validator for the dynamic field build based on the
		 * "users" field template
		 *
		 * @param mixed $values Values to validate.
		 *
		 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
		 */
		return function ( $values ) {
			if ( ! is_array( $values ) ) {
				return new WP_Error(
					'non_array_value',
					sprintf(
						// Translators: %s - type of the value given.
						__( 'Value must be an array, %s given.', 'bm-custom-login' ),
						gettype( $values ),
					),
				);
			}

			foreach ( $values as $value ) {
				if ( ! is_string( $value ) ) {
					continue;
				}

				$user = get_user_by( 'login', $value );

				if ( false === $user ) {
					return new WP_Error(
						'field_value_out_of_scope',
						sprintf(
							// Translators: %s - unknown user value.
							__( '"%s" does not correspond to any known user.', 'bm-custom-login' ),
							Utils\Type::ensure_string( $value ),
						),
					);
				}
			}

			return true;
		};
	}
}
