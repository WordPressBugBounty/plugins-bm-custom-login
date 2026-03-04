<?php
/**
 * Set of methods and properties to be inherited by validatable objects
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Set of methods and properties to be inherited by validatable objects
 */
// @phpstan-ignore trait.unused
trait Validatable {
	/**
	 * Validation errors
	 *
	 * @var array<int,WP_Error>
	 */
	protected array $validation_errors = [];

	/**
	 * Add validation error
	 *
	 * @param WP_Error $validation_error Validation error to add.
	 *
	 * @return void
	 */
	protected function add_validation_error( WP_Error $validation_error ): void {
		$this->validation_errors[] = $validation_error;
	}

	/**
	 * Add multiple validation errors at once
	 *
	 * @param array<int,WP_Error> $validation_errors Validation errors to add.
	 *
	 * @return void
	 */
	protected function add_validation_errors( array $validation_errors ): void {
		foreach ( $validation_errors as $validation_error ) {
			$this->add_validation_error( $validation_error );
		}
	}

	/**
	 * Check if any validation errors has been reported
	 *
	 * @return bool Boolean "true" if any validation errors has been reported, "false" otherwise.
	 */
	public function has_validation_errors(): bool {
		return 0 < count( $this->validation_errors );
	}

	/**
	 * Get all validation errors
	 *
	 * @return array<int,WP_Error> Array of validation errors.
	 */
	public function get_validation_errors(): array {
		return $this->validation_errors;
	}

	/**
	 * Get first validation error
	 *
	 * @return WP_Error Instance of WP_Error.
	 */
	public function get_first_validation_error(): WP_Error {
		return $this->validation_errors[0] ?? new WP_Error();
	}
}
