<?php
/**
 * Set of methods and properties to be inherited by fields constrained to a list of allowed values
 *
 * @package Teydea_Studio\Login_Customizer\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Login_Customizer\Dependencies\Validatable_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * Set of methods and properties to be inherited by fields constrained to a list of allowed values
 */
trait Choice_Constrained {
	/**
	 * Array of allowed values
	 *
	 * @var array<int,int|string>
	 */
	protected array $allowed_values;

	/**
	 * Get schema for the REST API
	 *
	 * @return array{type:string,enum?:array<int,int|string>} Schema array.
	 */
	public function get_schema(): array {
		$schema = [
			'type' => $this->value_type,
		];

		/**
		 * A field with no allowed values rejects everything, but `enum` has to
		 * carry at least one member to be a valid JSON Schema keyword — so the
		 * schema cannot describe such a field, and this guard keeps it from
		 * publishing a constraint WordPress would skip. Configuring one is
		 * refused outright by `Configuration::assert_allows_a_value()`; what
		 * remains here is the direct-construction path.
		 */
		if ( [] !== $this->allowed_values ) {
			$schema['enum'] = $this->allowed_values;
		}

		return $schema;
	}
}
